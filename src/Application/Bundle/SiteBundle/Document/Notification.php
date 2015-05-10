<?php

namespace Application\Bundle\SiteBundle\Document;

use Sonata\NotificationBundle\Model\Message;

/**
 * Message notification.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class Notification extends Message
{
    /**
     * @var integer $id
     */
    protected $id;

    /**
     * Returns the ID.
     *
     * @return string $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Override clone in order to avoid duplicating entries in Doctrine
     */
    public function __clone()
    {
        parent::__clone();

        $this->id = null;
    }
}
