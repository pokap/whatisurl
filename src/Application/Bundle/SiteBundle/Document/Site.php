<?php

namespace Application\Bundle\SiteBundle\Document;

/**
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class Site extends \Application\Component\Link\Domain\Site
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var \DateTime
     */
    protected $lastAccessAt;

    /**
     * Returns the document ID.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the last access.
     *
     * @return \DateTime
     */
    public function getLastAccessAt()
    {
        return $this->lastAccessAt;
    }

    /**
     * Sets the last access.
     *
     * @param \DateTime $lastAccessAt
     */
    public function setLastAccessAt($lastAccessAt)
    {
        $this->lastAccessAt = $lastAccessAt;
    }
}
