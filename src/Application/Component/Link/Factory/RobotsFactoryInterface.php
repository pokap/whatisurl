<?php

namespace Application\Component\Link\Factory;

use Application\Component\Link\Domain\RobotsInterface;

/**
 * Representation for robots factory.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
interface RobotsFactoryInterface
{
    /**
     * Create new instance of robots domain.
     *
     * @param string $host
     *
     * @return RobotsInterface
     */
    public function create($host);
}
