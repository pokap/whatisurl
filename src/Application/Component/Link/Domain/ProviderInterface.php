<?php

namespace Application\Component\Link\Domain;

/**
 * Interface that represents a link provider.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
interface ProviderInterface
{
    /**
     * Returns the provider name.
     *
     * @return string
     */
    public function getName();
}
