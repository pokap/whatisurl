<?php

namespace Application\Bundle\SiteBundle\Document\Provider;

/**
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class RssProvider extends \Application\Component\Link\Domain\Provider\RssProvider implements \JsonSerializable
{
    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'title' => $this->title,
        ];
    }
}
