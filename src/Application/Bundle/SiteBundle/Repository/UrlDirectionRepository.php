<?php

namespace Application\Bundle\SiteBundle\Repository;

use Application\Bundle\SiteBundle\Document\Url;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Application\Bundle\SiteBundle\Document\UrlDirection;

/**
 * @author Florent Denis <fdenis@oxent.net>
 */
class UrlDirectionRepository extends DocumentRepository implements UrlDirectionRepositoryInterface
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
     * {@inheritdoc}
     */
    public function save(UrlDirection $direction)
    {
        $this->dm->persist($direction);
        $this->dm->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function exists(Url $from, Url $to)
    {
        $qb = $this->createQueryBuilder();
        $qb->count();
        $qb->field('from')->references($from);
        $qb->field('to')->references($to);
        $qb->limit(1);

        return 0 < $qb->getQuery()->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function findByFrom(Url $url)
    {
        $qb = $this->createQueryBuilder();
        $qb->select('to');
        $qb->field('to')->prime(true);
        $qb->field('from')->references($url);

        return $qb->getQuery()->execute();
    }
}
