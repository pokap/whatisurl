<?php

namespace Application\Bundle\SiteBundle\Analyser;

use Application\Bundle\SiteBundle\Document\Provider\RssProvider;

/**
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class RssAnalyser extends \Application\Component\Link\Analyser\RssAnalyser
{
    /**
     * {@inheritdoc}
     */
    protected function newProvider()
    {
        return new RssProvider();
    }
}
