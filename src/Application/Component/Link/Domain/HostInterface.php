<?php

namespace Application\Component\Link\Domain;

/**
 * Interface that represents a host.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
interface HostInterface
{
    /**
     * Returns the sub domain.
     *
     * @return string|null
     */
    public function getSubDomain();

    /**
     * Sets the sub domain.
     *
     * @param string|null $subDomain
     */
    public function setSubDomain($subDomain);

    /**
     * Returns the domain.
     *
     * @return string
     */
    public function getDomain();

    /**
     * Sets the domain.
     *
     * @param string $domain
     */
    public function setDomain($domain);

    /**
     * Returns the domain extension.
     *
     * @return string
     */
    public function getExtension();

    /**
     * Sets the domain extension.
     *
     * @param string $extension
     */
    public function setExtension($extension);

    /**
     * Returns the host.
     *
     * @return string
     */
    public function __toString();
}
