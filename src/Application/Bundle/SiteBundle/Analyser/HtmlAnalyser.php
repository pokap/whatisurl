<?php

namespace Application\Bundle\SiteBundle\Analyser;

use Application\Bundle\SiteBundle\Document\Provider\PageProvider;

/**
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class HtmlAnalyser extends \Application\Component\Link\Analyser\HtmlAnalyser
{
    /**
     * {@inheritdoc}
     */
    protected function newProvider()
    {
        return new PageProvider();
    }
}
