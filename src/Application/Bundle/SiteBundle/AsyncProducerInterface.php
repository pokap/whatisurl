<?php

namespace Application\Bundle\SiteBundle;

/**
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
interface AsyncProducerInterface
{
    /**
     * Execute an asynchrone task with options.
     *
     * @param array $options
     */
    public function send(array $options = []);

    /**
     * Returns type name.
     *
     * @return string
     */
    public function getType();
}
