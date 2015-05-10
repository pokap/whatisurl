<?php

namespace Application\Bundle\SiteBundle\Repository;

use Application\Bundle\SiteBundle\Document\Url;
use Doctrine\Common\Persistence\ObjectRepository;

/**
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
interface UrlRepositoryInterface extends ObjectRepository
{
    /**
     * Commit an url.
     *
     * @param Url $url
     */
    public function save(Url $url);
}
