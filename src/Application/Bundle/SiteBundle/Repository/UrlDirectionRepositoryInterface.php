<?php

namespace Application\Bundle\SiteBundle\Repository;

use Application\Bundle\SiteBundle\Document\Url;
use Application\Bundle\SiteBundle\Document\UrlDirection;
use Doctrine\Common\Persistence\ObjectRepository;

/**
 * @author Florent Denis <fdenis@oxent.net>
 */
interface UrlDirectionRepositoryInterface extends ObjectRepository
{
    /**
     * Commit a direction.
     *
     * @param UrlDirection $direction
     */
    public function save(UrlDirection $direction);

    /**
     * Search if this direction already exists.
     *
     * @param Url $from
     * @param Url $to
     *
     * @return bool
     */
    public function exists(Url $from, Url $to);

    /**
     * Find list of url out.
     *
     * @param Url $url
     *
     * @return UrlDirection[]
     */
    public function findByFrom(Url $url);
}
