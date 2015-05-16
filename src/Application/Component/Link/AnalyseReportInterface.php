<?php

namespace Application\Component\Link;

use Application\Component\Link\Domain\UrlInterface;
use Application\Component\Link\Domain\ProviderInterface;

/**
 * Representation of a report given by the analyser.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
interface AnalyseReportInterface
{
    /**
     * Returns the provider.
     *
     * @return ProviderInterface|null
     */
    public function getProvider();

    /**
     * Check if a provider has been sets.
     *
     * @return bool
     */
    public function hasProvider();

    /**
     * Add a new URL.
     *
     * @param UrlInterface $url
     */
    public function addUrl(UrlInterface $url);

    /**
     * Returns TRUE if the analyser found new URLs.
     *
     * @return bool
     */
    public function hasUrlsFound();

    /**
     * Returns list of URLs found.
     *
     * @return UrlInterface[]
     */
    public function getUrlsFound();
}
