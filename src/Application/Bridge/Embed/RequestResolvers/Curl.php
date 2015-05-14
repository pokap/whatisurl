<?php

namespace Application\Bridge\Embed\RequestResolvers;

/**
 * {@inheritdoc}
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class Curl extends \Embed\RequestResolvers\Curl
{
    /**
     * Sets the content.
     *
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * Sets a value for a result.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function setResult($name, $value)
    {
        if (null === $this->result) {
            $this->result = [];
        }

        $this->result[$name] = $value;
    }
}
