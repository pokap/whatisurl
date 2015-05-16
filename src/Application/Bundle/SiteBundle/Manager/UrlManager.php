<?php

namespace Application\Bundle\SiteBundle\Manager;

use Application\Bundle\SiteBundle\Document\Url;
use Application\Bundle\SiteBundle\Repository\UrlRepositoryInterface;
use Zend\Uri\UriInterface;

/**
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class UrlManager extends \Application\Component\Link\Manager\UrlManager
{
    /**
     * @var UrlRepositoryInterface
     */
    protected $repository;

    /**
     * Constructor.
     *
     * @param UrlRepositoryInterface $repository
     */
    public function __construct(UrlRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Check if the url is up to date.
     *
     * @param Url    $url
     * @param string $interval
     *
     * @return bool
     */
    public function isUpToDate(Url $url, $interval = 'P1W')
    {
        if (!$url->isVisited()) {
            return false;
        }

        $date = (new \DateTime())->sub(new \DateInterval($interval));

        if ($url->getUpdatedAt() > $date) {
            return true;
        }

        $expires = $url->getHttpHeader()->getExpires();

        if (null !== $expires && $url->getUpdatedAt() < $expires) {
            return true;
        }

        return false;
    }

    /**
     * Finds a single Url given by an uri.
     *
     * @param UriInterface $uri
     *
     * @return Url|null
     */
    public function findOneByUri(UriInterface $uri)
    {
        $hash = $this->hash($uri->getScheme(), $uri->getHost(), $uri->getPath(), $uri->getQueryAsArray(), $uri->getPort());

        return $this->findOneBy(['hash' => $hash]);
    }

    /**
     * Hash the url identifiers.
     *
     * @param string      $schema
     * @param string      $host
     * @param string|null $path
     * @param array       $queryString
     * @param int|null    $port
     *
     * @return string
     */
    public function hash($schema, $host, $path, array $queryString = [], $port = null)
    {
        if (empty($path)) {
            $path = '/';
        }

        if (empty($port)) {
            $port = 80;
        }

        $this->sort($queryString);

        $id = [
            'schema'        => (string) $schema,
            'host'          => (string) $host,
            'path'          => (string) $path,
            'query_string'  => $queryString,
            'port'          => (int) $port,
        ];

        return sha1(json_encode($id));
    }

    /**
     * Finds an Url given by ID.
     *
     * @param string $id
     *
     * @return Url|null
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Finds a single Url given by criteria.
     *
     * @param array $criteria
     *
     * @return Url|null
     */
    public function findOneBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * Save an url.
     *
     * @param Url $url
     */
    public function save(Url $url)
    {
        $this->repository->save($url);
    }

    /**
     * Recusive alternative to ksort.
     *
     * @param array &$array
     */
    final protected function sort(array &$array)
    {
        ksort($array);

        foreach ($array as $row) {
            if (is_array($row)) {
                $this->sort($row);
            }
        }
    }
}
