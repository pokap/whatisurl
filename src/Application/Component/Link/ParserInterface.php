<?php

namespace Application\Component\Link;

use Application\Component\Link\Domain\UrlInterface;
use Application\Component\Link\Exception\InvalidArgumentException;
use Zend\Uri\UriInterface;

/**
 * Service for analysed an url.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
interface ParserInterface
{
    /**
     * Analyse an url.
     *
     * @param UriInterface $uri
     * @param float        $timeout (Optional)
     *
     * @return ParserReportInterface
     *
     * @throws InvalidArgumentException
     */
    public function parse(UriInterface $uri, $timeout = 10.);

    /**
     * Analyse and update the link.
     *
     * @param UrlInterface $link
     * @param float        $timeout
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public function update(UrlInterface $link, $timeout = 10.);
}
