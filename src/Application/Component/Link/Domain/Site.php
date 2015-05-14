<?php

namespace Application\Component\Link\Domain;

use Application\Component\Link\Exception\InvalidArgumentException;

/**
 * Represents a list of hosts per IP.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class Site implements SiteInterface
{
    /**
     * @var string
     */
    protected $ip;

    /**
     * @var string[]
     */
    protected $hosts = [];

    /**
     * {@inheritdoc}
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * {@inheritdoc}
     */
    public function setIp($ip)
    {
        if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
            throw new InvalidArgumentException(sprintf('IP "%s" is not valid.', $ip));
        }

        $this->ip = (string) $ip;
    }

    /**
     * {@inheritdoc}
     */
    public function addHost($host)
    {
        if ($this->inHost($host)) {
            return;
        }

        $this->hosts[] = $host;
    }

    /**
     * {@inheritdoc}
     */
    public function inHost($host)
    {
        return in_array((string) $host, $this->hosts);
    }

    /**
     * {@inheritdoc}
     */
    public function getHosts()
    {
        return $this->hosts;
    }
}
