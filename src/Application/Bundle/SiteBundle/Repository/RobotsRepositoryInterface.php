<?php

namespace Application\Bundle\SiteBundle\Repository;

use Application\Bundle\SiteBundle\Document\Robots;
use Doctrine\Common\Persistence\ObjectRepository;

/**
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
interface RobotsRepositoryInterface extends ObjectRepository
{
    /**
     * Commit a robots.
     *
     * @param Robots $robots
     */
    public function save(Robots $robots);
}
