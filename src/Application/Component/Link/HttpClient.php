<?php

namespace Application\Component\Link;

use Application\Component\Link\Domain\UrlInterface;
use Zend\Http\Client;
use Zend\Http\Header\Etag;
use Zend\Http\Header\IfModifiedSince;
use Zend\Http\Header\IfNoneMatch;
use Zend\Http\Headers;
use Zend\Http\Request;

/**
 * Decorator http client.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class HttpClient implements HttpClientInterface
{
    /**
     * @var \Zend\Http\Client
     */
    protected $client;

    /**
     * Constructor.
     *
     * @param string $userAgent
     */
    public function __construct($userAgent)
    {
        $this->client = new Client();
        $this->client->setOptions([
            'useragent'     => (string) $userAgent,
            'adapter'       => 'Zend\\Http\\Client\\Adapter\\Curl',
            'curloptions'   => array(
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYPEER => false
            ),
            'maxredirects'  => 0,
            'storeresponse' => false,
            'rfc3986strict' => true,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function get(UrlInterface $url, $timeout = 10)
    {
        $request = null;

        if ($headers = $this->createRequestHeaders($url)) {
            $request = new Request();
            $request->setHeaders($headers);
        }

        return $this->send($url->getUrl(), $timeout, false, $request);
    }

    /**
     * {@inheritdoc}
     */
    public function head(UrlInterface $url, $timeout = 10)
    {
        $request = null;

        if ($headers = $this->createRequestHeaders($url)) {
            $request = new Request();
            $request->setHeaders($headers);
        }

        return $this->send($url->getUrl(), $timeout, true, $request);
    }

    /**
     * Send a request.
     *
     * @param string       $uri
     * @param float        $timeout (Optional)
     * @param bool         $withoutBody (Optional)
     * @param Request|null $request (Optional)
     *
     * @return \Zend\Http\Response
     */
    protected function send($uri, $timeout, $withoutBody = false, Request $request = null)
    {
        $this->client->setOptions([
            'timeout' => $timeout
        ]);

        $this->client->setUri($uri);

        /** @var \Zend\Http\Client\Adapter\Curl $adapter */
        $adapter = $this->client->getAdapter();
        $adapter->setCurlOption(CURLOPT_NOBODY, $withoutBody);
        $adapter->setCurlOption(CURLOPT_FOLLOWLOCATION, false);

        return $this->client->send();
    }

    /**
     * Create headers list for the request.
     *
     * @param UrlInterface $url
     *
     * @return Headers|null
     */
    protected function createRequestHeaders(UrlInterface $url)
    {
        $header = $url->getHttpHeader();
        if (null === $header) {
            return null;
        }

        $headers = new Headers();

        if (null !== $header->getLastModified()) {
            $headers->addHeader(new IfModifiedSince($header->getLastModified()));
        }

        if (null !== $header->getEtag()) {
            $headers->addHeader(new IfNoneMatch($header->getEtag()));
        }

        return $headers;
    }
}
