<?php

namespace Application\Bundle\SiteBundle\Repository;

use Application\Bundle\SiteBundle\Document\Notification;
use Doctrine\Common\Persistence\ObjectRepository;

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
     * @param array      $types
     * @param integer    $state
     * @param integer    $batchSize
     * @param array|null $hosts
     *
     * @return []MessageInterface
     */
    public function findByTypes(array $types, $state, $batchSize, array $hosts = null);

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
