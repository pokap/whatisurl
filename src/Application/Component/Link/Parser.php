<?php

/*
 * this file is part of the pok package.
 *
 * (c) florent denis <dflorent.pokap@gmail.com>
 *
 * for the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Component\Link;

use Application\Component\Link\Domain\HttpHeaderInterface;
use Application\Component\Link\Domain\UrlInterface;
use Application\Component\Link\Exception\AnalyserFailedException;
use Application\Component\Link\Factory\RobotsFactoryInterface;
use Application\Component\Link\Factory\UrlFactoryInterface;
use Application\Component\Link\Manager\UrlManagerInterface;
use Application\Component\Link\Transformer\RobotsTransformerInterface;
use Application\Bridge\Roboxt\Parser as RoboxtParser;
use Psr\Log\LoggerAwareTrait;
use Roboxt\File;
use Zend\Uri\UriInterface;

/**
 * Parser.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class Parser implements ParserInterface
{
    use LoggerAwareTrait;

    /**
     * @var HttpClientInterface
     */
    protected $client;

    /**
     * @var UrlFactoryInterface
     */
    protected $urlFactory;

    /**
     * @var UrlManagerInterface
     */
    protected $urlManager;

    /**
     * @var RobotsFactoryInterface
     */
    protected $robotsFactory;

    /**
     * @var RobotsTransformerInterface
     */
    protected $robotsTransformer;

    /**
     * @var RoboxtParser
     */
    protected $robotsParser;

    /**
     * @var AnalyserInterface[]
     */
    protected $analysers;

    /**
     * @var string
     */
    protected $agent;

    /**
     * Constructor.
     *
     * @param HttpClientInterface        $client
     * @param UrlFactoryInterface        $urlFactory
     * @param UrlManagerInterface        $urlManager
     * @param RobotsFactoryInterface     $robotsFactory
     * @param RobotsTransformerInterface $robotsTransformer
     * @param RoboxtParser               $robotsParser
     * @param AnalyserInterface[]        $analysers
     * @param string                     $agent
     */
    public function __construct(
        HttpClientInterface $client,
        UrlFactoryInterface $urlFactory,
        UrlManagerInterface $urlManager,
        RobotsFactoryInterface $robotsFactory,
        RobotsTransformerInterface $robotsTransformer,
        RoboxtParser $robotsParser,
        array $analysers,
        $agent)
    {
        $this->client = $client;
        $this->urlFactory = $urlFactory;
        $this->urlManager = $urlManager;
        $this->robotsFactory = $robotsFactory;
        $this->robotsTransformer = $robotsTransformer;
        $this->robotsParser = $robotsParser;
        $this->analysers = $analysers;
        $this->agent = $agent;
    }

    /**
     * {@inheritdoc}
     */
    public function parse(UriInterface $uri, $timeout = 10.)
    {
        $link = $this->urlFactory->create($uri);

        $this->update($link, $timeout);

        return $link;
    }

    /**
     * {@inheritdoc}
     */
    public function update(UrlInterface $link, $timeout = 10.)
    {
        $startTime = microtime(true);

        // TODO: move to directive
        if (in_array($link->getSchema(), ['http', 'https'])) {
            $file = $this->retrieveRobotstxt($link);

            if (0 < count($file->allUserAgents()) && !$file->isUrlAllowedByUserAgent($link->getPath(), $this->agent)) {
                $this->initHttpHeader($link, 403);

                return;
            }

            $timeout -= microtime(true) - $startTime;
        }

        if ($timeout > 0) {
            $continue = $this->requestHead($link, $timeout);
            if (false === $continue) {
                return;
            }

            $timeout -= microtime(true) - $startTime;

            if ($timeout > 0 && $this->urlManager->isSuccess($link)) {
                $this->analyseContent($link, $timeout);
            }
        }

        // TODO: Adding site factory action

        if ($timeout > 0) {
            $site = $this->siteFactory->create(gethostbyname($link->getHost()));

            if (!$site->inHost($link->getHost())) {
                $site->addHost($this->hostFactory->create($link->getHost()));
            }
        }
    }

    /**
     * Retrieve the robotstxt.
     *
     * @param UrlInterface $link
     *
     * @return \Roboxt\File
     */
    protected function retrieveRobotstxt(UrlInterface $link)
    {
        $robots = $this->robotsFactory->create($link->getHost());

        if (null === $robots->getUserAgent()) {
            $file = $this->robotsParser->parse($link->getBaseUrl() . '/robots.txt');

            $this->robotsTransformer->transform($robots, $file->getUserAgent($this->agent));
        } else {
            $file = new File();

            $this->robotsTransformer->reverseTransform($file, $robots);
        }

        return $file;
    }

    /**
     * Complete headers link.
     *
     * @param UrlInterface $link
     * @param float        $timeout (Optional)
     *
     * @return bool Continue state
     */
    protected function requestHead(UrlInterface $link, $timeout = 10.)
    {
        $response = $this->client->head($link, $timeout);
        if (304 === $response->getStatusCode()) {
            return false;
        }

        $linkHeader = $this->initHttpHeader($link, $response->getStatusCode());

        if ($response->getHeaders()->has('Content-Type')) {
            /** @var \Zend\Http\Header\ContentType $contentType */
            $contentType = $response->getHeaders()->get('Content-Type');

            $linkHeader->setContentType($contentType->getMediaType());
        }

        if ($response->getHeaders()->has('Last-Modified')) {
            /** @var \Zend\Http\Header\LastModified $lastModified */
            $lastModified = $response->getHeaders()->get('Last-Modified');

            $linkHeader->setLastModified($lastModified->date());
        }

        if ($response->getHeaders()->has('Etag')) {
            /** @var \Zend\Http\Header\Etag $etag */
            $etag = $response->getHeaders()->get('Etag');

            $linkHeader->setEtag($etag->getFieldValue());
        }

        if ($response->getHeaders()->has('Content-Disposition')) {
            /** @var \Zend\Http\Header\ContentDisposition $disposition */
            $disposition = $response->getHeaders()->get('Content-Disposition');

            $linkHeader->setContentDisposition($disposition->getFieldValue());
        }

        if ($response->getHeaders()->has('Content-Language')) {
            /** @var \Zend\Http\Header\ContentLanguage $language */
            $language = $response->getHeaders()->get('Content-Language');

            $linkHeader->setContentLanguage($language->getFieldValue());
        }

        if ($response->isRedirect() && $response->getHeaders()->has('Location')) {
            /** @var \Zend\Http\Header\Location $location */
            $location = $response->getHeaders()->get('Location');

            if ($urlLocation = $this->urlManager->resolvePath($link->getUrl(), $location->getUri())) {
                $linkLocation = $this->urlFactory->create($urlLocation);

                $link->addOutUrls([$linkLocation]);
            }
        }

        return true;
    }

    /**
     * Analyse body link.
     *
     * @param UrlInterface $link
     * @param float        $timeout (Optional)
     */
    protected function analyseContent(UrlInterface $link, $timeout = 10.)
    {
//        sleep(1);

        $response = $this->client->get($link, $timeout);

        foreach ($this->analysers as $analyser) {
            $support = $analyser->support($link->getHttpHeader()->getContentType());
            $supportPresumed = false;

            if (!$support) {
                $supportPresumed = $analyser->support($link->getHttpHeader()->getContentTypePresumed());

                if (!$supportPresumed) {
                    continue;
                }
            }

            try {
                $report = $analyser->analyse($response->getBody(), $link);
            } catch (AnalyserFailedException $e) {
                if ($supportPresumed) {
                    $link->getHttpHeader()->setContentTypePresumed(null);
                }

                continue;
            } catch (\Exception $e) {
                if ($this->logger) {
                    $this->logger->critical($e->getMessage(), ['exception' => $e]);
                }

                continue;
            }

            $link->addProvider($report->getProvider());
            $link->addOutUrls($report->getUrlsFound());
        }
    }

    /**
     * Initialize http-header.
     *
     * @param UrlInterface $link
     * @param int          $statusCode
     *
     * @return HttpHeaderInterface
     */
    private function initHttpHeader(UrlInterface $link, $statusCode)
    {
        $linkHeader = $link->getHttpHeader();

        if (null === $linkHeader) {
            $link->setHttpHeader($linkHeader = $this->urlFactory->createHttpHeader());
        }

        $linkHeader->setStatusCode($statusCode);

        return $linkHeader;
    }
}
