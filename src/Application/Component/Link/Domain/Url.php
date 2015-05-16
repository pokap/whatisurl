<?php

namespace Application\Component\Link\Domain;

/**
 * Represents an URL.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class Url implements UrlInterface
{
    /**
     * @var string
     */
    protected $schema;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var int
     */
    protected $port = 80;

    /**
     * @var string
     */
    protected $path = '/';

    /**
     * @var array
     */
    protected $queryString;

    /**
     * @var HttpHeaderInterface|null
     */
    protected $httpHeader;

    /**
     * @var ProviderInterface[]
     */
    protected $providers = [];

    /**
     * @var UrlInterface[]
     */
    protected $out = [];

    /**
     * Constructor.
     *
     * @param string      $schema
     * @param string      $host
     * @param string|null $path
     * @param array       $queryString
     * @param int|null    $port
     */
    public function __construct($schema, $host, $path = null, array $queryString = [], $port = null)
    {
        $this->schema = (string) $schema;
        $this->host = (string) $host;
        $this->queryString = static::cleanQueryString($queryString);

        if (!empty($path)) {
            $this->path = (string) $path;
        }

        if (null !== $port) {
            $this->port = (int) $port;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        $url = $this->getBaseUrl() . $this->path;

        if (!empty($this->queryString)) {
            $url .= '?' . http_build_query($this->queryString);
        }

        return $url;
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseUrl()
    {
        $url = $this->schema . '://' . $this->host;

        if ($this->port && 80 !== $this->port) {
            $url .= ':' . $this->port;
        }

        return $url;
    }

    /**
     * {@inheritdoc}
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * {@inheritdoc}
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * {@inheritdoc}
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryString()
    {
        return $this->queryString;
    }

    /**
     * {@inheritdoc}
     */
    public function setHttpHeader(HttpHeaderInterface $httpHeader = null)
    {
        $this->httpHeader = $httpHeader;
    }

    /**
     * {@inheritdoc}
     */
    public function getHttpHeader()
    {
        return $this->httpHeader;
    }

    /**
     * {@inheritdoc}
     */
    public function isVisited()
    {
        return null !== $this->httpHeader && null !== $this->httpHeader->getStatusCode();
    }

    /**
     * {@inheritdoc}
     */
    public function addProvider(ProviderInterface $provider)
    {
        $this->providers[$provider->getName()] = $provider;
    }

    /**
     * {@inheritdoc}
     */
    public function removeProvider($name)
    {
        unset($this->providers[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function hasProvider($name)
    {
        return isset($this->providers[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getProviders()
    {
        return $this->providers;
    }

    /**
     * {@inheritdoc}
     */
    public function addOutUrls(array $urls)
    {
        $this->out = array_merge($this->out, $urls);
    }

    /**
     * {@inheritdoc}
     */
    public function getOut()
    {
        return $this->out;
    }

    /**
     * Clean list of query string.
     *
     * @param array $queryString
     *
     * @return array
     */
    public static function cleanQueryString(array $queryString)
    {
        $result = [];
        foreach ($queryString as $key => $value) {
            if (is_int($key) && empty($value)) {
                continue;
            }

            $result[$key] = is_array($value)? static::cleanQueryString($value) : (string) $value;
        }

        return $result;
    }
}
