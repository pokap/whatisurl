<?php

namespace Application\Component\Link;

use Application\Component\Link\Domain\UrlInterface;
use Application\Component\Link\Exception\AnalyserFailedException;

/**
 * Service for analysed a content type.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
interface AnalyserInterface
{
    /**
     * Analyse a content and return a report.
     *
     * @param string       $content
     * @param UrlInterface $url
     *
     * @return AnalyseReportInterface
     *
     * @throws AnalyserFailedException When analyser failed
     */
    public function analyse($content, UrlInterface $url);

    /**
     * Returns that this analyser support the type of link.
     *
     * @param string $mimeType
     *
     * @return bool
     */
    public function support($mimeType);

    /**
     * Returns the format.
     *
     * @return string
     */
    public function getFormat();
}
