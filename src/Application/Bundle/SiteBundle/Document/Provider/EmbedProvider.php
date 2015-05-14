<?php

namespace Application\Bundle\SiteBundle\Document\Provider;

/**
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class EmbedProvider extends \Application\Component\Link\Domain\Provider\EmbedProvider implements \JsonSerializable
{
    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'title'             => $this->title,
            'type'              => $this->type,
            'image'             => $this->image,
            'image_width'       => $this->imageWidth,
            'image_height'      => $this->imageHeight,
            'code'              => $this->code,
            'width'             => $this->width,
            'height'            => $this->height,
            'author_name'       => $this->authorName,
            'author_url'        => $this->authorUrl,
            'provider_name'     => $this->providerName,
            'provider_url'      => $this->providerUrl,
        ];
    }
}
