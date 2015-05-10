<?php

namespace Application\Component\Link\Factory;

use Application\Component\Link\Domain\HttpHeaderInterface;
use Application\Component\Link\Domain\UrlInterface;
use Application\Component\Link\Exception\InvalidArgumentException;
use Zend\Uri\UriInterface;

/**
 * Representation for link factory.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
interface UrlFactoryInterface
{
    /**
     * Create new instance of link.
     *
     * @param UriInterface $uri
     * @param string|null  $contentTypePresumed
     *
     * @return UrlInterface
     *
     * @throws InvalidArgumentException
     */
    public function create(UriInterface $uri, $contentTypePresumed = null);

    /**
     * Check that URI is compatible URL.
     *
     * @param UriInterface $uri
     *
     * @return bool
     */
    public function isCompatible(UriInterface $uri);

    /**
     * Create new instance of HttpHeader domain.
     *
     * @return HttpHeaderInterface
     */
    public function createHttpHeader();
}
