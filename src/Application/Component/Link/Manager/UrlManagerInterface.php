<?php

namespace Application\Component\Link\Manager;

use Application\Component\Link\Domain\UrlInterface;
use Zend\Uri\UriInterface;

/**
 * Interprets information given by url domain.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
interface UrlManagerInterface
{
    /**
     * Clear an url.
     *
     * @param string $baseUrl
     * @param string $path
     *
     * @return UriInterface|null
     */
    public function resolvePath($baseUrl, $path);

    /**
     * Does the status code indicate a client error?
     *
     * @param UrlInterface $url
     *
     * @return bool
     */
    public function isClientError(UrlInterface $url);

    /**
     * Is the request forbidden due to ACLs?
     *
     * @param UrlInterface $url
     *
     * @return bool
     */
    public function isForbidden(UrlInterface $url);

    /**
     * Is the current status "informational"?
     *
     * @param UrlInterface $url
     *
     * @return bool
     */
    public function isInformational(UrlInterface $url);

    /**
     * Does the status code indicate the resource is not found?
     *
     * @param UrlInterface $url
     *
     * @return bool
     */
    public function isNotFound(UrlInterface $url);

    /**
     * Do we have a normal, OK response?
     *
     * @param UrlInterface $url
     *
     * @return bool
     */
    public function isOk(UrlInterface $url);

    /**
     * Does the status code reflect a server error?
     *
     * @param UrlInterface $url
     *
     * @return bool
     */
    public function isServerError(UrlInterface $url);

    /**
     * Do we have a redirect?
     *
     * @param UrlInterface $url
     *
     * @return bool
     */
    public function isRedirect(UrlInterface $url);

    /**
     * Was the response successful?
     *
     * @param UrlInterface $url
     *
     * @return bool
     */
    public function isSuccess(UrlInterface $url);

    /**
     * Is the response empty?
     *
     * @param UrlInterface $url
     *
     * @return bool
     */
    public function isEmpty(UrlInterface $url);
}
