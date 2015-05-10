<?php

namespace Application\Component\Link\Domain;

use WebArchive\Snapshot;

/**
 * WebArchive
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class WebArchive
{
    /**
     * @var string
     */
    protected $url;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @var Snapshot[]
     */
    protected $snapshots = [];

    /**
     * Sets the original URL.
     *
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Returns the original URL.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Sets all snapshots.
     *
     * @param Snapshot[] $snapshots
     */
    public function setSnapshots(array $snapshots)
    {
        $this->snapshots = $snapshots;
    }

    /**
     * Returns that snapshots exists.
     *
     * @return Snapshot[]
     */
    public function hasSnapshots()
    {
        return !empty($this->snapshots);
    }

    /**
     * Returns all snapshots.
     *
     * @return Snapshot[]
     */
    public function getSnapshots()
    {
        return $this->snapshots;
    }
}
