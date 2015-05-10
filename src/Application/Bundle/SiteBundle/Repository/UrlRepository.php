<?php

namespace Application\Bundle\SiteBundle\Repository;

use Application\Bundle\SiteBundle\Document\Url;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;

/**
 * @author Florent Denis <fdenis@oxent.net>
 */
class UrlRepository extends DocumentRepository implements UrlRepositoryInterface
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
    public function save(Url $url)
    {
        $url->setUpdatedAt(new \DateTime());

        $this->dm->persist($url);
        $this->dm->flush();
    }
}
