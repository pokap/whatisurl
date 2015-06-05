<?php

namespace Application\Bundle\SiteBundle\Iterator;

use Application\Bundle\SiteBundle\Manager\NotificationManager;
use Sonata\NotificationBundle\Model\MessageInterface;

/**
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class MessageManagerMessageIterator extends \Sonata\NotificationBundle\Iterator\MessageManagerMessageIterator
{
    /**
     * @var NotificationManager
     */
    protected $messageManager;

    /**
     * @var string
     */
    protected $group;

    /**
     * {@inheritdoc}
     */
    public function __construct(NotificationManager $notificationManager, $types = array(), $group, $pause = 1500000, $batchSize = 10)
    {
        parent::__construct($notificationManager, $types, $pause, $batchSize);

        $this->group = $group;
    }

    /**
     * {@inheritdoc}
     */
    protected function findNextMessages($types)
    {
        return $this->messageManager->findByTypesAndGroup($types, MessageInterface::STATE_OPEN, $this->group, $this->batchSize);
    }
}
