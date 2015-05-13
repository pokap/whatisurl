<?php

namespace Application\Bundle\SiteBundle\Factory;

use Application\Bundle\SiteBundle\Document\Host;

/**
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class HostFactory extends \Application\Component\Link\Factory\HostFactory
{
    /**
     * {@inheritdoc}
     */
    protected function newHost()
    {
        return new Host();
    }
}
