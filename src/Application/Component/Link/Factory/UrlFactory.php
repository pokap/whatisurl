<?php

namespace Application\Component\Link\Factory;

use Application\Component\Link\Domain\HttpHeader;
use Application\Component\Link\Domain\HttpHeaderInterface;
use Application\Component\Link\Domain\Url;
use Application\Component\Link\Domain\UrlInterface;
use Application\Component\Link\Exception\InvalidArgumentException;
use Zend\Uri\UriInterface;

/**
 * Url factory service.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class UrlFactory implements UrlFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(UriInterface $uri, $contentTypePresumed = null)
    {
	    if (!$this->isCompatible($uri)) {
            throw new InvalidArgumentException('Url is not compatible.');
        }

        $url = $this->newUrl($uri->getScheme(), $uri->getHost(), $uri->getPath(), $uri->getQueryAsArray(), $uri->getPort());

        if (null !== $contentTypePresumed) {
            $header = $url->getHttpHeader();

            if (null === $header) {
                $url->setHttpHeader($header = $this->createHttpHeader());
            }

            $header->setContentTypePresumed($contentTypePresumed);
        }

        return $url;
    }

    /**
     * {@inheritdoc}
     */
    public function isCompatible(UriInterface $uri)
    {
        return null !== $uri->getHost() && $uri->isValid();
    }

    /**
     * Create new instance of HttpHeader domain.
     *
     * @return HttpHeaderInterface
     */
    public function createHttpHeader()
    {
        return new HttpHeader();
    }

    /**
     * Create new instance of Url domain.
     *
     * @param string   $schema
     * @param string   $host
     * @param string   $path
     * @param array    $queryString
     * @param int|null $port
     *
     * @return UrlInterface
     */
    protected function newUrl($schema, $host, $path, array $queryString = [], $port = null)
    {
        return new Url($schema, $host, $path, $queryString, $port);
    }
}
