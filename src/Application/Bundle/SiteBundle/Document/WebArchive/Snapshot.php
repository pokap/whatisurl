<?php

namespace Application\Bundle\SiteBundle\Document\WebArchive;

/**
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class Snapshot extends \WebArchive\Snapshot implements \JsonSerializable
{
    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'url'       => $this->url,
            'date'      => $this->date->format(DATE_ISO8601),
        ];
    }
}
