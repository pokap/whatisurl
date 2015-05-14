<?php

namespace Application\Bundle\SiteBundle\Analyser;

use Application\Bundle\SiteBundle\Document\Provider\EmbedProvider;

/**
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class EmbedAnalyser extends \Application\Component\Link\Analyser\EmbedAnalyser
{
    /**
     * {@inheritdoc}
     */
    protected function newProvider()
    {
        return new EmbedProvider();
    }
}
