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
        $request = new Request();
        $request->setUri($url->getUrl());
        $request->setMethod('GET');

        if ($headers = $this->createRequestHeaders($url)) {
            $request->setHeaders($headers);
        }

        return $this->send($request, $timeout, false);
    }

    /**
     * {@inheritdoc}
     */
    public function head(UrlInterface $url, $timeout = 10)
    {
        $request = new Request();
        $request->setUri($url->getUrl());
        $request->setMethod('HEAD');

        if ($headers = $this->createRequestHeaders($url)) {
            $request->setHeaders($headers);
        }

        return $this->send($request, $timeout, true);
    }

    /**
     * Send a request.
     *
     * @param Request $request
     * @param float   $timeout (Optional)
     * @param bool    $withoutBody (Optional)
     *
     * @return \Zend\Http\Response
     */
    protected function send(Request $request, $timeout, $withoutBody = false)
    {
        $this->client->setOptions([
            'timeout' => $timeout
        ]);

        /** @var \Zend\Http\Client\Adapter\Curl $adapter */
        $adapter = $this->client->getAdapter();
        $adapter->setCurlOption(CURLOPT_NOBODY, $withoutBody);
        $adapter->setCurlOption(CURLOPT_FOLLOWLOCATION, false);

        return $this->client->send($request);
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
            $value = new IfModifiedSince($header->getLastModified());
            $value->setDate($header->getLastModified());

            $headers->addHeader($value);
        }

        if (null !== $header->getEtag()) {
            $headers->addHeader(new IfNoneMatch($header->getEtag()));
        }

        return $headers;
    }
}
