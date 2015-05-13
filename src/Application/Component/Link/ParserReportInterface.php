<?php

namespace Application\Component\Link;

use Application\Component\Link\Domain\RobotsInterface;
use Application\Component\Link\Domain\SiteInterface;
use Application\Component\Link\Domain\UrlInterface;

/**
 * Representation of a report given by the parser.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
interface ParserReportInterface
{
    /**
     * Returns the url.
     *
     * @return UrlInterface
     */
    public function getUrl();

    /**
     * Returns the robots directive.
     *
     * @return RobotsInterface
     */
    public function getRobots();

    /**
     * Sets the robots directive.
     *
     * @param RobotsInterface $robots
     */
    public function setRobots(RobotsInterface $robots);

    /**
     * Returns the site information.
     *
     * @return SiteInterface
     */
    public function getSite();

    /**
     * Sets the site information.
     *
     * @param SiteInterface $site
     */
    public function setSite(SiteInterface $site);
}
