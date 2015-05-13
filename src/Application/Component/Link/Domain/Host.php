<?php

namespace Application\Component\Link\Domain;

/**
 * Represents a host.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class Host implements HostInterface
{
    /**
     * @var string|null
     */
    protected $subDomain;

    /**
     * @var string
     */
    protected $domain;

    /**
     * @var string
     */
    protected $extension;

    /**
     * {@inheritdoc}
     */
    public function getSubDomain()
    {
        return $this->subDomain;
    }

    /**
     * {@inheritdoc}
     */
    public function setSubDomain($subDomain)
    {
        $this->subDomain = (null !== $subDomain)? (string) $subDomain : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * {@inheritdoc}
     */
    public function setDomain($domain)
    {
        $this->domain = (string) $domain;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * {@inheritdoc}
     */
    public function setExtension($extension)
    {
        $this->extension = (string) $extension;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        $domain = $this->domain . '.' . $this->extension;

        if (null === $this->subDomain) {
            return $domain;
        }

        return $this->subDomain . '.' . $domain;
    }
}
