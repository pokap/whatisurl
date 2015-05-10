<?php

namespace Application\Component\Link\Domain\Provider;

use Application\Component\Link\Domain\ProviderInterface;

/**
 * Represents information for a feed rss.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class RssProvider implements ProviderInterface
{
    /**
     * @var string|null
     */
    protected $title;

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
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'rss';
    }
}
