<?php

namespace Application\Bundle\SiteBundle\Document;

/**
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class Site extends \Application\Component\Link\Domain\Site
{
    /**
     * @var string
     */
    protected $id;

    /**
     * Returns the document ID.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
}
