<?php

namespace Application\Component\Link\Factory;

use Application\Component\Link\Domain\Host;
use Application\Component\Link\Domain\HostInterface;
use Application\Component\Link\Exception\InvalidArgumentException;

/**
 * Host factory.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class HostFactory implements HostFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create($host)
    {
        $pos = strrpos($host, '.');

        if (false === $pos) {
            throw new InvalidArgumentException(sprintf('Invalid host, "%s" given.', $host));
        }

        $result = $this->newHost();
        $result->setExtension(substr($host, $pos + 1));

        $host = substr($host, 0, $pos);
        $pos = strrpos($host, '.');

        if (false === $pos) {
            $result->setDomain($host);
        } else {
            $result->setDomain(substr($host, $pos + 1));
            $result->setSubDomain(substr($host, 0, $pos));
        }

        return $result;
    }

    /**
     * Create new instance of host.
     *
     * @return HostInterface
     */
    protected function newHost()
    {
        return new Host();
    }
}
