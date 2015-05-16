<?php

namespace Application\Component\Link;

use Application\Component\Link\Domain\UrlInterface;
use Application\Component\Link\Domain\ProviderInterface;

/**
 * Report given by the analyser.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class AnalyseReport implements AnalyseReportInterface
{
    /**
     * @var ProviderInterface|null
     */
    protected $provider;

    /**
     * @var UrlInterface[]
     */
    protected $urls = [];

    /**
     * Constructor.
     *
     * @param ProviderInterface|null $provider
     */
    public function __construct(ProviderInterface $provider = null)
    {
        $this->provider = $provider;
    }

    /**
     * {@inheritdoc}
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * {@inheritdoc}
     */
    public function hasProvider()
    {
        return null !== $this->provider;
    }

    /**
     * {@inheritdoc}
     */
    public function addUrl(UrlInterface $url)
    {
        $this->urls[] = $url;
    }

    /**
     * {@inheritdoc}
     */
    public function hasUrlsFound()
    {
        return !empty($this->urls);
    }

    /**
     * {@inheritdoc}
     */
    public function getUrlsFound()
    {
        return $this->urls;
    }
}
