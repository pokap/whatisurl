<?php

namespace Application\Component\Link\Analyser;

use Application\Component\Link\AnalyserInterface;
use Application\Component\Link\Domain\UrlInterface;

class MarkdownAnalyser implements AnalyserInterface
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
    public function support($mimeType, $host = null)
    {
        return ['text/markdown', 'text/x-markdown'];
    }

    /**
     * {@inheritdoc}
     */
    public function getFormat()
    {
        return 'markdown';
    }
}
