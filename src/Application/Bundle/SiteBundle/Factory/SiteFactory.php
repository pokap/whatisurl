<?php

namespace Application\Bundle\SiteBundle\Factory;

use Application\Bundle\SiteBundle\Document\Site;
use Application\Bundle\SiteBundle\Repository\SiteRepositoryInterface;
use Application\Component\Link\Domain\SiteInterface;

/**
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class SiteFactory extends \Application\Component\Link\Factory\SiteFactory
{
    /**
     * @var SiteRepositoryInterface
     */
    protected $repository;

    /**
     * Constructor.
     *
     * @param SiteRepositoryInterface $repository
     */
    public function __construct(SiteRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function create($ip)
    {
        /** @var SiteInterface|null $robots */
        $site = $this->repository->findOneBy(['ip' => $ip]);

        if (null !== $site) {
            return $site;
        }

        return parent::create($ip);
    }

    /**
     * {@inheritdoc}
     */
    protected function newSite()
    {
        return new Site();
    }
}
