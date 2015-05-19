<?php

namespace Application\Bundle\SiteBundle\Manager;

use Application\Bundle\SiteBundle\Repository\NotificationRepositoryInterface;
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
    protected $repository;

    /**
     * Constructor.
     *
     * @param NotificationRepositoryInterface $repository
     */
    public function __construct(NotificationRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function save($message, $andFlush = true)
    {
        $this->repository->save($message, $andFlush);
    }

    /**
     * {@inheritDoc}
     */
    public function findByTypesAndGroup(array $types, $state, $group, $batchSize)
    {
        return $this->repository->findByTypesAndGroup($types, $state, $group, $batchSize);
    }

    /**
     * {@inheritDoc}
     */
    public function groupByGroup(array $types, $state)
    {
        return $this->repository->groupByGroup($types, $state);
    }

    /**
     * {@inheritDoc}
     */
    public function findByTypes(array $types, $state, $batchSize)
    {
        return $this->repository->findByTypes($types, $state, $batchSize);
    }

    /**
     * {@inheritDoc}
     */
    public function findByAttempts(array $types, $state, $batchSize, $maxAttempts = null, $attemptDelay = 10)
    {
        return $this->repository->findByAttempts($types, $state, $batchSize, $maxAttempts, $attemptDelay);
    }

    /**
     * {@inheritDoc}
     */
    public function countStates()
    {
        return $this->repository->countStates();
    }

    /**
     * {@inheritDoc}
     */
    public function cleanup($maxAge)
    {
        $this->repository->cleanup($maxAge);
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
        return $this->repository->getClassName();
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return $this->repository->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return $this->repository->find($id);
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
