<?php

namespace Application\Component\Link\Factory;

use Application\Component\Link\Domain\Site;
use Application\Component\Link\Domain\SiteInterface;

/**
 * Site factory.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class SiteFactory implements SiteFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create($ip)
    {
        $site = $this->newSite();
        $site->setIp($ip);

        return $site;
    }

    /**
     * Create new instance of site.
     *
     * @return SiteInterface
     */
    protected function newSite()
    {
        return new Site();
    }
}
