<?php

namespace Application\Component\Link\Analyser;

use Application\Component\Link\AnalyserInterface;
use Application\Component\Link\Domain\Provider\EmbedProvider;
use Application\Component\Link\Domain\UrlInterface;
use Application\Component\Link\Exception\AnalyserFailedException;
use Application\Component\Link\Factory\AnalyseReportFactoryInterface;
use Embed\Adapters\AdapterInterface;
use Embed\Embed;
use Embed\Request;
use Embed\Url;

/**
 * Service for analysed a media that can be embed.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class EmbedAnalyser implements AnalyserInterface
{
    const RESOLVER_CLASS_NAME = 'Application\\Bridge\\Embed\\RequestResolvers\\Curl';

    /**
     * @var AnalyseReportFactoryInterface
     */
    protected $reportFactory;

    /**
     * Constructor.
     *
     * @param AnalyseReportFactoryInterface $reportFactory
     */
    public function __construct(AnalyseReportFactoryInterface $reportFactory)
    {
        $this->reportFactory = $reportFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function analyse($content, UrlInterface $url)
    {
        $info = Embed::create($this->createRequest($content, $url));

        if (false === $info) {
            throw new AnalyserFailedException(sprintf('Url "%s" is not supported.', $url->getUrl()));
        }

        return $this->reportFactory->create($this->transform($info));
    }

    /**
     * {@inheritdoc}
     */
    public function support($mimeType, $host = null)
    {
        if (null === $host) {
            return false;
        }

        $host = array_reverse(explode('.', $host));

        if (class_exists('Embed\\Adapters\\'.ucfirst(strtolower($host[1])))) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormat()
    {
        return 'embed';
    }

    /**
     * Transform adapter info into embed provider.
     *
     * @param AdapterInterface $info
     *
     * @return EmbedProvider
     */
    protected function transform($info)
    {
        $provider = $this->newProvider();
        $provider->setTitle($info->getTitle());
        $provider->setDescription($info->getDescription());
        $provider->setUrl($info->getUrl());
        $provider->setType($info->getType());
        $provider->setImage($info->getImage());
        $provider->setImageWidth($info->getImageWidth());
        $provider->setImageHeight($info->getImageHeight());
        $provider->setImages($info->getImages());
        $provider->setCode($info->getCode());
        $provider->setWidth($info->getWidth());
        $provider->setHeight($info->getHeight());
        $provider->setAuthorName($info->getAuthorName());
        $provider->setAuthorUrl($info->getAuthorUrl());
        $provider->setProviderName($info->getProviderName());
        $provider->setProviderUrl($info->getProviderUrl());
        $provider->setPublishedTime($info->getPublishedTime());

        return $provider;
    }

    /**
     * Create new instance of embed provider.
     *
     * @return EmbedProvider
     */
    protected function newProvider()
    {
        return new EmbedProvider();
    }

    /**
     * Create instance of embed request.
     *
     * @param string       $content
     * @param UrlInterface $url
     *
     * @return Request
     */
    protected function createRequest($content, UrlInterface $url)
    {
        $literalUrl = $url->getUrl();

        $request = new Request(new Url($literalUrl), self::RESOLVER_CLASS_NAME);

        /** @var \Application\Bridge\Embed\RequestResolvers\Curl $resolver */
        $resolver = $request->resolver;
        $resolver->setResult('url', $literalUrl);
        $resolver->setResult('http_code', $url->getHttpHeader()->getStatusCode());
        $resolver->setResult('mime_type', $url->getHttpHeader()->getContentType());
        $resolver->setContent($content);

        return $request;
    }
}
