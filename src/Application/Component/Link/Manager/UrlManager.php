<?php

namespace Application\Component\Link\Manager;

use Application\Component\Link\Domain\UrlInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Zend\Uri\Uri;
use Zend\Uri\Exception\ExceptionInterface as ZendUriExceptionInterface;
use Zend\Uri\UriInterface;

/**
 * Interprets information given by url domain.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class UrlManager implements UrlManagerInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function resolvePath($baseUrl, $path)
    {
        if (empty($path) || $path[0] === '#') {
            return null;
        }

        try {
            $uri = new Uri($path);
            // remove useless fragment
            $uri->setFragment(null);

            if (!$uri->isValid()) {
                return null;
            }

            if ($uri->isAbsolute()) {
                return $uri;
            }

            $uri->resolve($baseUrl);

            if (!$uri->isValid() || !$uri->isAbsolute() || !$uri->getHost()) {
                return null;
            }

            return $uri;
        } catch (ZendUriExceptionInterface $exception) {
            if (null !== $this->logger) {
                $this->logger->notice('Fail compute real-path between "{base_url}" and "{path}".', [
                    'base_url' => $baseUrl,
                    'path' => $path,
                    'exception' => $exception,
                ]);
            }

            return null;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isClientError(UrlInterface $url)
    {
        if (!$url->isVisited()) {
            throw new \LogicException('This URL has not been visited!');
        }

        $code = $url->getHttpHeader()->getStatusCode();

        return ($code < 500 && $code >= 400);
    }

    /**
     * {@inheritdoc}
     */
    public function isForbidden(UrlInterface $url)
    {
        if (!$url->isVisited()) {
            throw new \LogicException('This URL has not been visited!');
        }

        return (403 === $url->getHttpHeader()->getStatusCode());
    }

    /**
     * {@inheritdoc}
     */
    public function isInformational(UrlInterface $url)
    {
        if (!$url->isVisited()) {
            throw new \LogicException('This URL has not been visited!');
        }

        $code = $url->getHttpHeader()->getStatusCode();

        return ($code >= 100 && $code < 200);
    }

    /**
     * {@inheritdoc}
     */
    public function isNotFound(UrlInterface $url)
    {
        if (!$url->isVisited()) {
            throw new \LogicException('This URL has not been visited!');
        }

        return (404 === $url->getHttpHeader()->getStatusCode());
    }

    /**
     * {@inheritdoc}
     */
    public function isOk(UrlInterface $url)
    {
        if (!$url->isVisited()) {
            throw new \LogicException('This URL has not been visited!');
        }

        return (200 === $url->getHttpHeader()->getStatusCode());
    }

    /**
     * {@inheritdoc}
     */
    public function isServerError(UrlInterface $url)
    {
        if (!$url->isVisited()) {
            throw new \LogicException('This URL has not been visited!');
        }

        $code = $url->getHttpHeader()->getStatusCode();

        return (500 <= $code && 600 > $code);
    }

    /**
     * {@inheritdoc}
     */
    public function isRedirect(UrlInterface $url)
    {
        if (!$url->isVisited()) {
            throw new \LogicException('This URL has not been visited!');
        }

        $code = $url->getHttpHeader()->getStatusCode();

        return in_array($code, [201, 301, 302, 303, 307, 308]);
    }

    /**
     * {@inheritdoc}
     */
    public function isSuccess(UrlInterface $url)
    {
        if (!$url->isVisited()) {
            throw new \LogicException('This URL has not been visited!');
        }

        $code = $url->getHttpHeader()->getStatusCode();

        return (200 <= $code && 300 > $code);
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty(UrlInterface $url)
    {
        if (!$url->isVisited()) {
            throw new \LogicException('This URL has not been visited!');
        }

        $code = $url->getHttpHeader()->getStatusCode();

        return in_array($code, [204, 304]);
    }
}
