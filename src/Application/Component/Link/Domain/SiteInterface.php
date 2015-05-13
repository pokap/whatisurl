<?php

namespace Application\Component\Link\Domain;

use Application\Component\Link\Exception\InvalidArgumentException;

/**
 * Interface that represents a list of hosts per IP.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
interface SiteInterface
{
    /**
     * Returns the IP.
     *
     * @return string
     */
    public function getIp();

    /**
     * Sets the IP server who host.
     *
     * @param string $ip
     *
     * @throws InvalidArgumentException When IP is not valid
     */
    public function setIp($ip);

    /**
     * Adding a host when young found one.
     *
     * @param HostInterface $host
     *
     * @throws InvalidArgumentException When host already exists
     */
    public function addHost(HostInterface $host);

    /**
     * Checks if a host exists in an hosts list.
     *
     * @param string $host
     *
     * @return bool
     */
    public function inHost($host);

    /**
     * Returns list of hosts.
     *
     * @return HostInterface[]
     */
    public function getHosts();
}
