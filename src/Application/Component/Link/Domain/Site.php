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
     * @var HostInterface[]
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
    public function addHost(HostInterface $host)
    {
        if ($this->inHost((string) $host)) {
            throw new InvalidArgumentException(sprintf('Host "%s" already registred.', (string) $host));
        }

        $this->hosts[] = $host;
    }

    /**
     * {@inheritdoc}
     */
    public function inHost($host)
    {
        foreach ($this->hosts as $siteHost) {
            if (strval($siteHost) === $host) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getHosts()
    {
        return $this->hosts;
    }
}
