<?php

namespace Application\Bundle\SiteBundle\Repository;

use Application\Bundle\SiteBundle\Document\Notification;
use Doctrine\Common\Persistence\ObjectRepository;
use Sonata\NotificationBundle\Model\MessageInterface;

/**
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
interface NotificationRepositoryInterface extends ObjectRepository
{
    /**
     * Commit a notification message.
     *
     * @param Notification $notification
     */
    public function save(Notification $notification);

    /**
     * Returns list of messages.
     *
     * @param array   $types
     * @param string  $group
     * @param integer $state
     * @param integer $batchSize
     *
     * @return MessageInterface[]
     */
    public function findByTypesAndGroup(array $types, $state, $group, $batchSize);

    /**
     * Returns list of groups.
     *
     * @param array   $types
     * @param integer $state
     *
     * @return string[]
     */
    public function groupByGroup(array $types, $state);

    /**
     * Returns list of messages.
     *
     * @param array   $types
     * @param integer $state
     * @param integer $batchSize
     *
     * @return MessageInterface[]
     */
    public function findByTypes(array $types, $state, $batchSize);

    /**
     * @param array $types
     * @param       $state
     * @param       $batchSize
     * @param null  $maxAttempts
     * @param int   $attemptDelay
     *
     * @return mixed
     */
    public function findByAttempts(array $types, $state, $batchSize, $maxAttempts = null, $attemptDelay = 10);

    /**
     * @return int[]
     */
    public function countStates();

    /**
     * @param int $maxAge
     */
    public function cleanup($maxAge);
}
