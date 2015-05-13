<?php

namespace Application\Component\Link\Factory;

use Application\Component\Link\Domain\UrlInterface;
use Application\Component\Link\ParserReportInterface;

/**
 * Representation for parser report factory.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
interface ParserReportFactoryInterface
{
    /**
     * Create new instance of parser report.
     *
     * @param UrlInterface $url
     *
     * @return ParserReportInterface
     */
    public function create(UrlInterface $url);
}
