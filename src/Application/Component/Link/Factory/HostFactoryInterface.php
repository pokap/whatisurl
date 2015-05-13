<?php

namespace Application\Component\Link\Factory;

use Application\Component\Link\Domain\HostInterface;
use Application\Component\Link\Exception\InvalidArgumentException;

/**
 * Represents a host factory.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
interface HostFactoryInterface
{
    /**
     * Parse and create a host object.
     *
     * @param string $host
     *
     * @return HostInterface
     *
     * @throws InvalidArgumentException
     */
    public function create($host);
}
