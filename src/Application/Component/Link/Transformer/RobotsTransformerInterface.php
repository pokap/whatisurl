<?php

namespace Application\Component\Link\Transformer;

use Application\Component\Link\Domain\RobotsInterface;
use Roboxt\File;
use Roboxt\UserAgent;

/**
 * Represents a robots transformer service.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
interface RobotsTransformerInterface
{
    /**
     * Transform data file to robots.
     *
     * @param RobotsInterface $robots
     * @param UserAgent|null  $userAgent
     */
    public function transform(RobotsInterface $robots, UserAgent $userAgent = null);

    /**
     * Transform data file from robots.
     *
     * @param File            $file
     * @param RobotsInterface $robots
     */
    public function reverseTransform(File $file, RobotsInterface $robots);
}
