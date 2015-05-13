<?php

namespace Application\Component\Link;

use Application\Component\Link\Domain\RobotsInterface;
use Application\Component\Link\Domain\SiteInterface;
use Application\Component\Link\Domain\UrlInterface;

/**
 * Report given by the parser.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class ParserReport implements ParserReportInterface
{
    /**
     * @var UrlInterface
     */
    protected $url;

    /**
     * @var RobotsInterface
     */
    private $robots;

    /**
     * @var SiteInterface
     */
    private $site;

    /**
     * Constructor.
     *
     * @param UrlInterface $url
     */
    public function __construct(UrlInterface $url)
    {
        $this->url = $url;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * {@inheritdoc}
     */
    public function getRobots()
    {
        return $this->robots;
    }

    /**
     * {@inheritdoc}
     */
    public function setRobots(RobotsInterface $robots)
    {
        $this->robots = $robots;
    }

    /**
     * {@inheritdoc}
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * {@inheritdoc}
     */
    public function setSite(SiteInterface $site)
    {
        $this->site = $site;
    }
}
