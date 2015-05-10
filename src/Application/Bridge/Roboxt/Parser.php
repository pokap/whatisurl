<?php

namespace Application\Bridge\Roboxt;

use PhpCollection\Sequence;
use Roboxt\Directive\Directive;
use Roboxt\File;
use Roboxt\UserAgent;

/**
 * {@inheritdoc}
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class Parser extends \Roboxt\Parser
{
    /**
     * {@inheritdoc}
     */
    public function parse($filePath)
    {
        $file  = new File($this->read($filePath));
        $lines = explode("\n", $file->getContent());

        foreach ($lines as $content) {
            $content = trim($content);

            // Skip empty lines
            if (empty($content) || '#' === $content[0] || false !== strpos($content, ':')) {
                continue;
            }

            list($name, $value) = explode(':', $content);
            $directive = new Directive($name, trim($value));

            // If the directive's name is "User-Agent" then register a UserAgent in the file
            if (!isset($userAgent) || $directive->isUserAgent()) {
                $userAgent = new UserAgent($directive->getValue(), new Sequence());
                $file->addUserAgent($userAgent);
            } else {
                $userAgent->addDirective($directive);
            }
        }

        return $file;
    }
}
