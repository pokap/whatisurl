<?php

namespace Application\Component\Link\Factory;

use Application\Component\Link\AnalyseReportInterface;
use Application\Component\Link\Domain\ProviderInterface;

/**
 * Representation for analyse report factory.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
interface AnalyseReportFactoryInterface
{
    /**
     * Create new instance of analyse report.
     *
     * @param ProviderInterface $provider
     *
     * @return AnalyseReportInterface
     */
    public function create(ProviderInterface $provider);
}
