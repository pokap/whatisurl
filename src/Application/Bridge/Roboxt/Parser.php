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
     * @var string
     */
    protected $userAgent;

    /**
     * Constructor.
     *
     * @param string $userAgent
     */
    public function __construct($userAgent)
    {
        $this->userAgent = (string) $userAgent;
    }

    /**
     * {@inheritdoc}
     */
    public function read($filePath)
    {
        $headers = [];
        $headers['Accept'] = 'text/plain';

        $opts = array(
            'http' => array(
                'method'          => 'GET',
                'header'          => $this->buildHeaders($headers),
                'timeout'         => 1,
                'ignore_errors'   => true,
                // need to set configuration options
                'user_agent'      => $this->userAgent,
                'follow_location' => 0,
                'max_redirects'   => 0,
            )
        );

        // http://php.net/manual/en/reserved.variables.httpresponseheader.php
        $content = @file_get_contents($filePath, false, stream_context_create($opts));

        if (empty($http_response_header) && $content === false) {
            return null;
        }

        $data = explode(" ", $http_response_header[0]);
        $status = (int) $data[1];

        // is not successful
        if ($status < 200 || $status >= 300) {
            return null;
        }

        return $content;
    }

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
            if (empty($content) || '#' === $content[0] || false === strpos($content, ':')) {
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

    /**
     * @param array $headers
     *
     * @return string
     */
    protected function buildHeaders(array $headers)
    {
        $data = '';
        foreach ($headers as $name => $value) {
            $data .= sprintf("%s: %s\r\n", $name, $value);
        }

        return $data;
    }
}
