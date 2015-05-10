<?php

namespace Application\Bundle\SiteBundle\Manager;

use Application\Bundle\SiteBundle\Document\Url;
use Application\Bundle\SiteBundle\Document\UrlDirection;
use Application\Bundle\SiteBundle\Repository\UrlDirectionRepositoryInterface;

/**
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class UrlDirectionManager
{
    /**
     * @var UrlDirectionRepositoryInterface
     */
    protected $repository;

    /**
     * Constructor.
     *
     * @param UrlDirectionRepositoryInterface $repository
     */
    public function __construct(UrlDirectionRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Search if this direction already exists.
     *
     * @param Url $from
     * @param Url $to
     *
     * @return bool
     */
    public function exists(Url $from, Url $to)
    {
        $this->repository->exists($from, $to);
    }

    /**
     * Save an url direction.
     *
     * @param UrlDirection $direction
     */
    public function save(UrlDirection $direction)
    {
        $this->repository->save($direction);
    }

    /**
     * Find list of url out.
     *
     * @param Url $url
     *
     * @return Url[]
     */
    public function findByFrom(Url $url)
    {
        $result = [];
        foreach ($this->repository->findByFrom($url) as $direction) {
            $result[] = $direction->getTo();
        }

        return $result;
    }
}
