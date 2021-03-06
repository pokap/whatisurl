<?php

namespace Application\Bundle\SiteBundle\Document\Provider;

/**
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class PageProvider extends \Application\Component\Link\Domain\Provider\PageProvider implements \JsonSerializable
{
    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'icon'          => $this->icon,
            'title'         => $this->title,
            'description'   => $this->description,
            'keywords'      => $this->keywords,
            'canonical'     => $this->canonical,
            'archive'       => $this->archive,
        ];
    }
}
