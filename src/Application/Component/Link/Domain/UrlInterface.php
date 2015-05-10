<?php

namespace Application\Component\Link\Domain;

/**
 * Interface that represents an URL.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
interface UrlInterface
{
    /**
     * Returns the url.
     *
     * @return string
     */
    public function getUrl();

    /**
     * Returns the base url.
     *
     * @return string
     */
    public function getBaseUrl();

    /**
     * Returns the schema.
     *
     * @return string
     */
    public function getSchema();

    /**
     * Returns the host.
     *
     * @return string
     */
    public function getHost();

    /**
     * Returns the port.
     *
     * @return int
     */
    public function getPort();

    /**
     * Returns the path.
     *
     * @return string
     */
    public function getPath();

    /**
     * Returns the list of query.
     *
     * @return array
     */
    public function getQueryString();

    /**
     * Sets HTTP Header information.
     *
     * @param HttpHeaderInterface|null $httpHeader
     */
    public function setHttpHeader(HttpHeaderInterface $httpHeader = null);

    /**
     * Returns HTTP Header information.
     *
     * @return HttpHeaderInterface|null
     */
    public function getHttpHeader();

    /**
     * Returns If this url has been visited.
     *
     * @return bool
     */
    public function isVisited();

    /**
     * Sets a link provider.
     *
     * @param ProviderInterface $provider
     */
    public function addProvider(ProviderInterface $provider);

    /**
     * Remove a provider.
     *
     * @param string $name
     */
    public function removeProvider($name);

    /**
     * Returns TRUE if provider exists.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasProvider($name);

    /**
     * Returns the links provider.
     *
     * @return ProviderInterface[]
     */
    public function getProviders();

    /**
     * Adding urls.
     *
     * @param UrlInterface[] $urls
     */
    public function addOutUrls(array $urls);

    /**
     * Returns list of out urls.
     *
     * @return UrlInterface[]
     */
    public function getOut();
}
