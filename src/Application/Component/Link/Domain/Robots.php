<?php

namespace Application\Component\Link\Domain;

/**
 * Represents rules for robots.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class Robots implements RobotsInterface
{
    /**
     * @var string
     */
    protected $schema;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var string
     */
    protected $userAgent;

    /**
     * @var array
     */
    protected $directives;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * {@inheritdoc}
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * {@inheritdoc}
     */
    public function setSchema($schema)
    {
        $this->schema = (string) $schema;
    }

    /**
     * {@inheritdoc}
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * {@inheritdoc}
     */
    public function setHost($host)
    {
        $this->host = (string) $host;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * {@inheritdoc}
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = (null !== $userAgent)? (string) $userAgent : null;
    }

    /**
     * {@inheritdoc}
     */
    public function setDirectives(array $directives)
    {
        $this->directives = $directives;
    }

    /**
     * {@inheritdoc}
     */
    public function getDirectives()
    {
        return $this->directives;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }
}
