<?php

namespace Application\Component\Link\Factory;

use Application\Component\Link\Domain\SiteInterface;

/**
 * Represents a site factory.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
interface SiteFactoryInterface
{
    /**
     * Create a new site object.
     *
     * @param string $ip
     *
     * @return SiteInterface
     */
    public function create($ip);
}
