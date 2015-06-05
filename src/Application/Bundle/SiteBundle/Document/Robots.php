<?php

namespace Application\Bundle\SiteBundle\Document;

/**
 * Robots document.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class Robots extends \Application\Component\Link\Domain\Robots
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
