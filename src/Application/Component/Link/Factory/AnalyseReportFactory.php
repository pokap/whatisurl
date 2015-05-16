<?php

namespace Application\Component\Link\Factory;

use Application\Component\Link\AnalyseReport;
use Application\Component\Link\Domain\ProviderInterface;

/**
 * Analyse report factory.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class AnalyseReportFactory implements AnalyseReportFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(ProviderInterface $provider = null)
    {
        return new AnalyseReport($provider);
    }
}
