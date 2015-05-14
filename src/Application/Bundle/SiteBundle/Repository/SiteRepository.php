<?php

namespace Application\Bundle\SiteBundle\Repository;

use Application\Bundle\SiteBundle\Document\Site;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;

/**
 * @author Florent Denis <fdenis@oxent.net>
 */
class SiteRepository extends DocumentRepository implements SiteRepositoryInterface
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
    public function save(Site $site)
    {
        $site->setLastAccessAt(new \DateTime());

        $this->dm->persist($site);
        $this->dm->flush();
    }
}
