<?php

namespace Application\Component\Link\Domain;

/**
 * Represents rules for robots.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
interface RobotsInterface
{
    /**
     * Returns the host.
     *
     * @return string
     */
    public function getHost();

    /**
     * Sets the host.
     *
     * @param string $host
     */
    public function setHost($host);

    /**
     * Returns the name of user-agent.
     *
     * @return string|null
     */
    public function getUserAgent();

    /**
     * Sets the name of user-agent.
     *
     * @param string|null $userAgent
     */
    public function setUserAgent($userAgent);

    /**
     * Sets a collection of directives.
     *
     * @param array $directives
     */
    public function setDirectives(array $directives);

    /**
     * Returns a collection of directives.
     *
     * @return array
     */
    public function getDirectives();

    /**
     * Returns the updated date.
     *
     * @return \DateTime
     */
    public function getUpdatedAt();

    /**
     * Sets the updated date.
     *
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt);
}
