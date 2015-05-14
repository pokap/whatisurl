<?php

namespace Application\Component\Link\Analyser;

use Application\Component\Link\AnalyserInterface;
use Application\Component\Link\Domain\Provider\EmbedProvider;
use Application\Component\Link\Domain\UrlInterface;
use Application\Component\Link\Exception\AnalyserFailedException;
use Application\Component\Link\Factory\AnalyseReportFactoryInterface;
use Embed\Providers\OEmbed;
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
        $oembed = new OEmbed();
        $oembed->init($this->createRequest($content, $url));
        $oembed->run();

        if (!count($oembed->bag->getAll())) {
            throw new AnalyserFailedException(sprintf('Url "%s" is not supported.', $url->getUrl()));
        }

        return $this->reportFactory->create($this->transform($oembed));
    }

    /**
     * {@inheritdoc}
     */
    public function support($mimeType, $host = null)
    {
        return true;
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
     * @param OEmbed $oembed
     *
     * @return EmbedProvider
     */
    protected function transform($oembed)
    {
        $images = $oembed->getImagesUrls();

        $provider = $this->newProvider();
        $provider->setTitle($oembed->getTitle());
        $provider->setType($oembed->getType());
        $provider->setImage(!empty($images)? current($images) : null);
        $provider->setImageWidth($oembed->bag->has('thumbnail_width')? $oembed->bag->get('thumbnail_width') : null);
        $provider->setImageHeight($oembed->bag->has('thumbnail_height')? $oembed->bag->get('thumbnail_height') : null);
        $provider->setCode($oembed->getCode());
        $provider->setWidth($oembed->getWidth());
        $provider->setHeight($oembed->getHeight());
        $provider->setAuthorName($oembed->getAuthorName());
        $provider->setAuthorUrl($oembed->getAuthorUrl());
        $provider->setProviderName($oembed->getProviderName());
        $provider->setProviderUrl($oembed->getProviderUrl());

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
