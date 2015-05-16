<?php

namespace Application\Bundle\SiteBundle\Factory;

use Application\Bundle\SiteBundle\Document\HttpHeader;
use Application\Bundle\SiteBundle\Document\Url;
use Application\Bundle\SiteBundle\Manager\UrlManager;

/**
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class UrlFactory extends \Application\Component\Link\Factory\UrlFactory
{
    /**
     * {@inheritdoc}
     */
    protected function newUrl($schema, $host, $path, array $queryString = [], $port = null)
    {
        $hash = $this->manager->hash($schema, $host, $path, $queryString, $port);

        $url = $this->manager->findOneBy(['hash' => $hash]);

        if (null === $url) {
            $url = new Url($schema, $host, $path, $queryString, $port);
            $url->setHash($hash);
        }

        return $url;
    }

    /**
     * {@inheritdoc}
     */
    public function createHttpHeader()
    {
        return new HttpHeader();
    }
}
