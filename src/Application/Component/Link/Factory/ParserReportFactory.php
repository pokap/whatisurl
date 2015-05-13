<?php

namespace Application\Component\Link\Factory;

use Application\Component\Link\ParserReport;
use Application\Component\Link\Domain\UrlInterface;

/**
 * Parser report factory.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class ParserReportFactory implements ParserReportFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(UrlInterface $url)
    {
        return new ParserReport($url);
    }
}
