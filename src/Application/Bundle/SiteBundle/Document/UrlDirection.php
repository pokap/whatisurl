<?php

namespace Application\Bundle\SiteBundle\Document;

use Application\Component\Link\Domain\UrlInterface;

/**
 * Rules urls relation.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class UrlDirection
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var UrlInterface
     */
    protected $from;

    /**
     * @var UrlInterface
     */
    protected $to;

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
     * Returns the URL source.
     *
     * @return UrlInterface
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Sets the url source.
     *
     * @param UrlInterface $from
     */
    public function setFrom(UrlInterface $from)
    {
        $this->from = $from;
    }

    /**
     * Returns the URL destination.
     *
     * @return UrlInterface
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Sets the url destination.
     *
     * @param UrlInterface $to
     */
    public function setTo(UrlInterface $to)
    {
        $this->to = $to;
    }
}
