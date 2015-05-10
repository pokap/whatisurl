<?php

namespace Application\Component\Link;

use Application\Component\Link\Domain\UrlInterface;

/**
 * Represents http client.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
interface HttpClientInterface
{
    /**
     * Returns response of GET request.
     *
     * @param UrlInterface $url
     * @param float        $timeout (Optional)
     *
     * @return \Zend\Http\Response
     */
    public function get(UrlInterface $url, $timeout = 10.);

    /**
     * Returns response of HEAD request.
     *
     * @param UrlInterface $url
     * @param float        $timeout (Optional)
     *
     * @return \Zend\Http\Response
     */
    public function head(UrlInterface $url, $timeout = 10.);
}
