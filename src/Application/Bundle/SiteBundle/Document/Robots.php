<?php

namespace Application\Bundle\SiteBundle\Document;

/**
 * Robots document.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class Robots extends \Application\Component\Link\Domain\Robots
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

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
     * Returns the updated date.
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Sets the updated date.
     *
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }
}
