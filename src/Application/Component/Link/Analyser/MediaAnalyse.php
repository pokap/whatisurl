<?php

namespace Application\Component\Link\Analyser;

use Application\Component\Link\AnalyserInterface;
use Application\Component\Link\Domain\UrlInterface;

class MediaAnalyser implements AnalyserInterface
{
    /**
     * {@inheritdoc}
     */
    public function analyse($content, UrlInterface $url)
    {
        // TODO: Implement analyse() method.
    }

    /**
     * {@inheritdoc}
     */
    public function support($mimeType)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormat()
    {
        return 'media';
    }
}
