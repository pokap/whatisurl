<?php

namespace Application\Bundle\SiteBundle\Document;

use Doctrine\Common\Collections\Collection;

/**
 * Url document.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class Url extends \Application\Component\Link\Domain\Url implements \JsonSerializable
{
    const STATUS_WAITING = 'waiting';
    const STATUS_COMPLETED = 'completed';

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var string
     */
    protected $hash;

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
     * Returns the status.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Sets the status.
     *
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Returns the hash identifier.
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Sets the hash identifier.
     *
     * @param string $hash
     */
    public function setHash($hash)
    {
        $this->hash = (string) $hash;
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

    /**
     * Remove sub data.
     */
    public function clear()
    {
        $this->out = null;
        $this->providers = null;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        if ($this->providers instanceof Collection) {
            $providers = $this->providers->toArray();
        } else {
            $providers = $this->providers;
        }

        if (null !== $this->out) {
            $out = [];
            foreach ($this->out as $key => $subLink) {
                $out[$key] = clone $subLink;
                $out[$key]->clear();
            }
        } else {
            $out = null;
        }

        $result = [
            'schema'        => $this->schema,
            'host'          => $this->host,
            'port'          => $this->port,
            'path'          => $this->path,
            'query_string'  => $this->queryString,
            'http_header'   => $this->httpHeader,
        ];

        if (null !== $providers) {
            $result['providers'] = $providers;
        }
        if (null !== $out) {
            $result['out'] = $out;
        }

        return $result;
    }
}
