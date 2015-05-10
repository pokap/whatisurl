<?php

namespace Application\Bundle\SiteBundle\Transformer;

use Application\Bundle\SiteBundle\Repository\RobotsRepositoryInterface;
use Application\Component\Link\Domain\RobotsInterface;
use Roboxt\UserAgent;

/**
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class RobotsTransformer extends \Application\Component\Link\Transformer\RobotsTransformer
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
    public function transform(RobotsInterface $robots, UserAgent $userAgent = null)
    {
        parent::transform($robots, $userAgent);

        $this->repository->save($robots);
    }
}
