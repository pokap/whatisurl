<?php

namespace Application\Bundle\SiteBundle\Manager;

use Application\Bundle\SiteBundle\Repository\NotificationRepositoryInterface;
use Application\Bundle\SiteBundle\Repository\SiteRepositoryInterface;
use Sonata\NotificationBundle\Model\MessageInterface;
use Sonata\NotificationBundle\Model\MessageManagerInterface;

/**
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class NotificationManager implements MessageManagerInterface
{
    /**
     * @var NotificationRepositoryInterface
     */
    protected $messageRepository;

    /**
     * @var SiteRepositoryInterface
     */
    protected $siteRepository;

    /**
     * Constructor.
     *
     * @param NotificationRepositoryInterface $messageRepository
     * @param SiteRepositoryInterface         $siteRepository
     */
    public function __construct(
        NotificationRepositoryInterface $messageRepository,
        SiteRepositoryInterface $siteRepository)
    {
        $this->messageRepository = $messageRepository;
        $this->siteRepository = $siteRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function save($message, $andFlush = true)
    {
        $this->messageRepository->save($message, $andFlush);
    }

    /**
     * {@inheritDoc}
     */
    public function findByTypes(array $types, $state, $batchSize)
    {
        $hosts = null;

        if ($types === ['parser']) {
            $batchSize = 1;

            if ($site = $this->siteRepository->findOneLastAccess()) {
                $hosts = $site->getHosts();
            }
        }

        return $this->messageRepository->findByTypes($types, $state, $batchSize, $hosts);
    }

    /**
     * {@inheritDoc}
     */
    public function findByAttempts(array $types, $state, $batchSize, $maxAttempts = null, $attemptDelay = 10)
    {
        return $this->messageRepository->findByAttempts($types, $state, $batchSize, $maxAttempts, $attemptDelay);
    }

    /**
     * {@inheritDoc}
     */
    public function countStates()
    {
        return $this->messageRepository->countStates();
    }

    /**
     * {@inheritDoc}
     */
    public function cleanup($maxAge)
    {
        $this->messageRepository->cleanup($maxAge);
    }

    /**
     * {@inheritdoc}
     */
    public function cancel(MessageInterface $message, $force = false)
    {
        if (($message->isRunning() || $message->isError()) && !$force) {
            return;
        }

        $message->setState(MessageInterface::STATE_CANCELLED);

        $this->save($message);
    }

    /**
     * {@inheritdoc}
     */
    public function restart(MessageInterface $message)
    {
        if ($message->isOpen() || $message->isRunning() || $message->isCancelled()) {
            return;
        }

        $this->cancel($message, true);

        $newMessage = clone $message;
        $newMessage->setRestartCount($message->getRestartCount() + 1);
        $newMessage->setType($message->getType());

        return $newMessage;
    }

    /**
     * {@inheritdoc}
     */
    public function getClass()
    {
        return $this->messageRepository->getClassName();
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return $this->messageRepository->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->messageRepository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return $this->messageRepository->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return $this->messageRepository->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        $class = $this->getClass();

        return new $class();
    }

    /**
     * {@inheritdoc}
     *
     * @deprecated
     */
    public function delete($entity, $andFlush = true)
    {
        throw new \LogicException('You can not delete a notification message.');
    }

    /**
     * {@inheritdoc}
     *
     * @deprecated
     */
    public function getTableName()
    {
        throw new \LogicException('Is not a table, but a collection. Idiot!');
    }

    /**
     * {@inheritdoc}
     *
     * @deprecated
     */
    public function getConnection()
    {
        throw new \LogicException('Is too dangerous to use directly a connection.');
    }
}
