<?php

namespace Application\Component\Link\Domain\Provider;

use Application\Component\Link\Domain\ProviderInterface;

/**
 * Represents information for a media that can be embed.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class EmbedProvider implements ProviderInterface
{
    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $image;

    /**
     * @var int
     */
    protected $imageWidth;

    /**
     * @var int
     */
    protected $imageHeight;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var int
     */
    protected $width;

    /**
     * @var int
     */
    protected $height;

    /**
     * @var string
     */
    protected $authorName;

    /**
     * @var string
     */
    protected $authorUrl;

    /**
     * @var string
     */
    protected $providerName;

    /**
     * @var string
     */
    protected $providerUrl;

    /**
     * @return string|null
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     */
    public function setTitle($title)
    {
        $this->title = !empty($title)? (string) $title : null;
    }

    /**
     * @return string|null
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string|null $type
     */
    public function setType($type)
    {
        $this->type = !empty($type)? (string) $type : null;
    }

    /**
     * @return string|null
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param string|null $image
     */
    public function setImage($image)
    {
        $this->image = !empty($image)? (string) $image : null;
    }

    /**
     * @return int|null
     */
    public function getImageWidth()
    {
        return $this->imageWidth;
    }

    /**
     * @param int|null $imageWidth
     */
    public function setImageWidth($imageWidth)
    {
        $this->imageWidth = (null !== $imageWidth)? (int) $imageWidth : null;
    }

    /**
     * @return int|null
     */
    public function getImageHeight()
    {
        return $this->imageHeight;
    }

    /**
     * @param int|null $imageHeight
     */
    public function setImageHeight($imageHeight)
    {
        $this->imageHeight = (null !== $imageHeight)? (int) $imageHeight : null;
    }

    /**
     * @return string|null
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string|null $code
     */
    public function setCode($code)
    {
        $this->code = !empty($code)? (string) $code : null;
    }

    /**
     * @return int|null
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param int|null $width
     */
    public function setWidth($width)
    {
        $this->width = !empty($width)? (int) $width : null;
    }

    /**
     * @return int|null
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param int|null $height
     */
    public function setHeight($height)
    {
        $this->height = !empty($height)? (int) $height : null;
    }

    /**
     * @return string|null
     */
    public function getAuthorName()
    {
        return $this->authorName;
    }

    /**
     * @param string|null $authorName
     */
    public function setAuthorName($authorName)
    {
        $this->authorName = !empty($authorName)? (string) $authorName : null;
    }

    /**
     * @return string|null
     */
    public function getAuthorUrl()
    {
        return $this->authorUrl;
    }

    /**
     * @param string|null $authorUrl
     */
    public function setAuthorUrl($authorUrl)
    {
        $this->authorUrl = !empty($authorUrl)? (string) $authorUrl : null;
    }

    /**
     * @return string|null
     */
    public function getProviderName()
    {
        return $this->providerName;
    }

    /**
     * @param string|null $providerName
     */
    public function setProviderName($providerName)
    {
        $this->providerName = !empty($providerName)? (string) $providerName : null;
    }

    /**
     * @return string|null
     */
    public function getProviderUrl()
    {
        return $this->providerUrl;
    }

    /**
     * @param string|null $providerUrl
     */
    public function setProviderUrl($providerUrl)
    {
        $this->providerUrl = !empty($providerUrl)? (string) $providerUrl : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'embed';
    }
}
