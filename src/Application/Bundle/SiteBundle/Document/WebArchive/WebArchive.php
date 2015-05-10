<?php

namespace Application\Bundle\SiteBundle\Document\WebArchive;

/**
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class WebArchive extends \Application\Component\Link\Domain\WebArchive implements \JsonSerializable
{
    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'url'           => $this->url,
            'snapshots'     => $this->snapshots,
        ];
    }
}
