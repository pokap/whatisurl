<?php

namespace Application\Bundle\SiteBundle\Repository;

use Application\Bundle\SiteBundle\Document\Robots;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;

/**
 * @author Florent Denis <fdenis@oxent.net>
 */
class RobotsRepository extends DocumentRepository implements RobotsRepositoryInterface
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
    public function save(Robots $robots)
    {
        $robots->setUpdatedAt(new \DateTime());

        $this->dm->persist($robots);
        $this->dm->flush();
    }
}
