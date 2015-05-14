<?php

namespace Application\Bundle\SiteBundle\Repository;

use Application\Bundle\SiteBundle\Document\Notification;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Sonata\NotificationBundle\Model\MessageInterface;

/**
 * @author Florent Denis <fdenis@oxent.net>
 */
class NotificationRepository extends DocumentRepository implements NotificationRepositoryInterface
{
    /**
     * Constructor.
     *
     * @param DocumentManager $dm        The DocumentManager to use
     * @param string          $modelName Document class name
     */
    public function __construct(DocumentManager $dm, $modelName)
    {
        parent::__construct($dm, $dm->getUnitOfWork(), $dm->getClassMetadata($modelName));
    }

    /**
     * {@inheritDoc}
     */
    public function save(Notification $notification, $andFlush = true)
    {
        // Hack for ConsumerHandlerCommand->optimize()
        if ($notification->getId() && !$this->dm->getUnitOfWork()->isInIdentityMap($notification)) {
            $this->dm->getUnitOfWork()->merge($notification);
        }

        $this->dm->persist($notification);
        $this->dm->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function findByTypes(array $types, $state, $batchSize, array $hosts = null)
    {
        $builder = $this->prepareStateQuery($state, $types, $batchSize);

        if (null !== $hosts) {
            $builder->field('hosts')->in($hosts);
        }

        return $builder->getQuery()->toArray();
    }

    /**
     * {@inheritDoc}
     */
    public function findByAttempts(array $types, $state, $batchSize, $maxAttempts = null, $attemptDelay = 10)
    {
        $builder = $this->prepareStateQuery($state, $types, $batchSize);

        if ($maxAttempts) {
            $delayDate = (new \DateTime())->add(\DateInterval::createFromDateString(($attemptDelay * -1) . ' second'));

            $builder->field('restartCount')->lt($maxAttempts);
            $builder->field('updatedAt')->lt($delayDate);
        }

        return $builder->getQuery()->toArray();
    }

    /**
     * {@inheritDoc}
     */
    public function countStates()
    {
        $collection = $this->dm->getDocumentCollection($this->class);

        $result = $collection->group(['state' => 1], ['count' => 0], 'function(obj, prev) { prev.count++; }');

        $states = [
            MessageInterface::STATE_ERROR           => 0,
            MessageInterface::STATE_CANCELLED       => 0,
            MessageInterface::STATE_OPEN            => 0,
            MessageInterface::STATE_IN_PROGRESS     => 0,
            MessageInterface::STATE_DONE            => 0,
        ];

        foreach ($result['retval'] as $data) {
            $state = (int) $data['state'];

            $states[$state] = (int) $data['count'];
        }

        return $states;
    }

    /**
     * {@inheritDoc}
     */
    public function cleanup($maxAge)
    {
        $date = new \DateTime('now');
        $date->sub(new \DateInterval(sprintf('PT%sS', $maxAge)));

        $qb = $this->createQueryBuilder();
        $qb->remove();
        $qb->field('state')->equals(MessageInterface::STATE_DONE);
        $qb->field('completedAt')->lt($date);

        $qb->getQuery()->execute();
    }

    /**
     * @param int   $state
     * @param array $types
     * @param int   $batchSize
     *
     * @return \Doctrine\ODM\MongoDB\Query\Builder
     */
    protected function prepareStateQuery($state, $types, $batchSize)
    {
        $query = $this->createQueryBuilder()
            ->field('state')->equals($state)
            ->sort('createdAt');

        if (!empty($types)) {
            if (isset($types['exclude']) || isset($types['include'])) {
                if (isset($types['exclude'])) {
                    $query->field('type')->notIn((array) $types['exclude']);
                } else {
                    $query->field('type')->in((array) $types['include']);
                }
            } else { // BC
                $query->field('type')->in($types);
            }
        }

        $query->limit($batchSize);

        return $query;
    }
}
