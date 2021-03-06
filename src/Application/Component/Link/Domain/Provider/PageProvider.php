<?php

namespace Application\Component\Link\Domain\Provider;

use Application\Component\Link\Domain\ProviderInterface;
use Application\Component\Link\Domain\WebArchive;

/**
 * Represents information for a web page.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class PageProvider implements ProviderInterface
{
    /**
     * @var string|null
     */
    protected $icon;

    /**
     * @var string|null
     */
    protected $title;

    /**
     * @var string|null
     */
    protected $description;

    /**
     * @var array|null
     */
    protected $keywords;

    /**
     * @var string|null
     */
    protected $canonical;

    /**
     * @var WebArchive|null
     */
    protected $archive;

    /**
     * Sets the icon url.
     *
     * @param string|null $icon
     */
    public function setIcon($icon)
    {
        $this->icon = (null !== $icon)? (string) $icon : null;
    }

    /**
     * Returns the icon url.
     *
     * @return string|null
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Sets the title page.
     *
     * @param string|null $title
     */
    public function setTitle($title)
    {
        $this->title = (null !== $title)? (string) $title : null;;
    }

    /**
     * Returns the title of web page.
     *
     * @return string|null
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Returns the description of web page.
     *
     * @return null|string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the description of web page.
     *
     * @param null|string $description
     */
    public function setDescription($description)
    {
        $this->description = (null !== $description)? (string) $description : null;;
    }

    /**
     * Returns the keywords of web page.
     *
     * @return array|null
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Sets the keywords of web page.
     *
     * @param array|null $keywords
     */
    public function setKeywords(array $keywords = null)
    {
        $this->keywords = $keywords;
    }

    /**
     * @return null|string
     */
    public function getCanonical()
    {
        return $this->canonical;
    }

    /**
     * @param null|string $canonical
     */
    public function setCanonical($canonical)
    {
        $this->canonical = (null !== $canonical)? (string) $canonical : null;;
    }

    /**
     * Returns web archive.
     *
     * @return WebArchive|null
     */
    public function getArchive()
    {
        return $this->archive;
    }

    /**
     * Sets the web archive info.
     *
     * @param WebArchive|null $archive
     */
    public function setArchive(WebArchive $archive = null)
    {
        $this->archive = $archive;
    }

    /**
     * Returns TRUE if this page is archived.
     *
     * @return boolean
     */
    public function isArchived()
    {
        return null !== $this->archive && $this->archive->hasSnapshots();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'page';
    }
}
