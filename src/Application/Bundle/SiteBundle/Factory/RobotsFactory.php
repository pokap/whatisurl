<?php

namespace Application\Bundle\SiteBundle\Factory;

use Application\Bundle\SiteBundle\Document\Robots;
use Application\Bundle\SiteBundle\Repository\RobotsRepositoryInterface;

/**
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class RobotsFactory extends \Application\Component\Link\Factory\RobotsFactory
{
    /**
     * @var RobotsRepositoryInterface
     */
    protected $repository;

    /**
     * Constructor.
     *
     * @param RobotsRepositoryInterface $repository
     */
    public function __construct(RobotsRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function create($schema, $host)
    {
        /** @var Robots|null $robots */
        $robots = $this->repository->findOneBy([
            'schema'    => $schema,
            'host'      => $host,
        ]);

        if (null !== $robots) {
            return $robots;
        }

        return parent::create($schema, $host);
    }

    /**
     * {@inheritdoc}
     */
    protected function newRobots()
    {
        return new Robots();
    }
}
