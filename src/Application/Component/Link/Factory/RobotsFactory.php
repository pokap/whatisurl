<?php

namespace Application\Component\Link\Factory;

use Application\Component\Link\Domain\Robots;
use Application\Component\Link\Domain\RobotsInterface;

/**
 * Robots factory service.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class RobotsFactory implements RobotsFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create($host)
    {
        $robots = $this->newRobots();
        $robots->setHost($host);

        return $robots;
    }

    /**
     * Create new instance of robots domain.
     *
     * @return RobotsInterface
     */
    protected function newRobots()
    {
        return new Robots();
    }
}
