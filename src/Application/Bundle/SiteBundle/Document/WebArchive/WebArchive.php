<?php

namespace Application\Bundle\SiteBundle\Document\WebArchive;

use Doctrine\Common\Collections\Collection;

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
        if ($this->snapshots instanceof Collection) {
            $snapshots = $this->snapshots->toArray();
        } else {
            $snapshots = $this->snapshots;
        }

        return [
            'snapshots' => $snapshots,
        ];
    }
}
