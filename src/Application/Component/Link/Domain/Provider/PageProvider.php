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
        $this->icon = $icon;
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
        $this->title = $title;
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
