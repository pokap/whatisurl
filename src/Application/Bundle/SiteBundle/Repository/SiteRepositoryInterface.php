<?php

namespace Application\Bundle\SiteBundle\Repository;

use Application\Bundle\SiteBundle\Document\Site;
use Doctrine\Common\Persistence\ObjectRepository;

/**
 * @author Florent Denis <fdenis@oxent.net>
 */
interface SiteRepositoryInterface extends ObjectRepository
{
    /**
     * Commit a site.
     *
     * @param Site $site
     */
    public function save(Site $site);

    /**
     * Returns a site given by host.
     *
     * @param string $host
     *
     * @return Site|null
     */
    public function findOneByHost($host);
}
