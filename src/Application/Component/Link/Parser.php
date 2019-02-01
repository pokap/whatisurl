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
use Application\Component\Link\Factory\ParserReportFactoryInterface;
use Application\Component\Link\Factory\RobotsFactoryInterface;
use Application\Component\Link\Factory\SiteFactoryInterface;
use Application\Component\Link\Factory\UrlFactoryInterface;
use Application\Component\Link\Manager\UrlManagerInterface;
use Application\Component\Link\Transformer\RobotsTransformerInterface;
use Application\Bridge\Roboxt\Parser as RoboxtParser;
use Psr\Log\LoggerAwareTrait;
use Roboxt\File;
use Symfony\Component\HttpFoundation\Response;
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
     * @var SiteFactoryInterface
     */
    protected $siteFactory;

    /**
     * @var ParserReportFactoryInterface
     */
    protected $parserReportFactory;

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
     * @param HttpClientInterface          $client
     * @param UrlFactoryInterface          $urlFactory
     * @param UrlManagerInterface          $urlManager
     * @param RobotsFactoryInterface       $robotsFactory
     * @param RobotsTransformerInterface   $robotsTransformer
     * @param RoboxtParser                 $robotsParser
     * @param SiteFactoryInterface         $siteFactory
     * @param ParserReportFactoryInterface $parserReportFactory
     * @param AnalyserInterface[]          $analysers
     * @param string                       $agent
     */
    public function __construct(
        HttpClientInterface $client,
        UrlFactoryInterface $urlFactory,
        UrlManagerInterface $urlManager,
        RobotsFactoryInterface $robotsFactory,
        RobotsTransformerInterface $robotsTransformer,
        RoboxtParser $robotsParser,
        SiteFactoryInterface $siteFactory,
        ParserReportFactoryInterface $parserReportFactory,
        array $analysers,
        $agent)
    {
        $this->client = $client;
        $this->urlFactory = $urlFactory;
        $this->urlManager = $urlManager;
        $this->robotsFactory = $robotsFactory;
        $this->robotsTransformer = $robotsTransformer;
        $this->robotsParser = $robotsParser;
        $this->siteFactory = $siteFactory;
        $this->parserReportFactory = $parserReportFactory;
        $this->analysers = $analysers;
        $this->agent = $agent;
    }

    /**
     * {@inheritdoc}
     */
    public function parse(UriInterface $uri, float $timeout = 10.): ParserReportInterface
    {
        $report = $this->parserReportFactory->create($this->urlFactory->create($uri));

        $this->update($report, $timeout);

        return $report;
    }

    /**
     * {@inheritdoc}
     */
    public function update(ParserReportInterface $report, float $timeout = 10.): void
    {
        $link = $report->getUrl();
        $startTime = microtime(true);

        // Host is not an IP ?
        if (filter_var($link->getHost(), FILTER_VALIDATE_IP) !== false) {
            $site = $report->getSite();
            if (null === $site) {
                $site = $this->siteFactory->create($link->getHost());
            }

            $report->setSite($site);
        } else {
            $ip = gethostbyname($link->getHost());

            // not found
            if ($ip === $link->getHost()) {
                $this->initHttpHeader($link, 404);

                return;
            }

            if ($timeout > 0) {
                $site = $report->getSite();

                if (null === $site) {
                    $site = $this->siteFactory->create($ip);
                }

                $site->addHost($link->getHost());

                $report->setSite($site);
            }

            $timeout -= microtime(true) - $startTime;
            if ($timeout <= 0) {
                return;
            }
        }

        // TODO: move to directive
        if (in_array($link->getSchema(), ['http', 'https'])) {
            $file = $this->retrieveRobotstxt($report);

            if (null !== $file->getUserAgent($this->agent) && !$file->isUrlAllowedByUserAgent($link->getPath(), $this->agent)) {
                $this->initHttpHeader($link, ('/' === $link->getPath())? 403 : 404);

                return;
            }

            $timeout -= microtime(true) - $startTime;
            if ($timeout <= 0) {
                return;
            }
        }

        $continue = $this->requestHead($link, $timeout);
        if (false === $continue) {
            return;
        }

        $timeout -= microtime(true) - $startTime;
        if ($timeout <= 0) {
            return;
        }

        if ($timeout > 0 && !$this->urlManager->isEmpty($link) && !$this->urlManager->isTooHeavy($link)) {
            $this->analyseContent($link, $timeout);
        }
    }

    /**
     * Retrieve the robotstxt.
     *
     * @param ParserReportInterface $report
     *
     * @return \Roboxt\File
     */
    protected function retrieveRobotstxt(ParserReportInterface $report)
    {
        $link = $report->getUrl();
        $robots = $this->robotsFactory->create($link->getSchema(), $link->getHost());

        $date = (new \DateTime())->sub(new \DateInterval('P1M'));

        if ($robots->getUpdatedAt() < $date) {
            $file = $this->robotsParser->parse($link->getBaseUrl() . '/robots.txt');

            $this->robotsTransformer->transform($robots, $file->getUserAgent($this->agent));
        } else {
            $file = new File();

            $this->robotsTransformer->reverseTransform($file, $robots);
        }

        $report->setRobots($robots);

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
        if (Response::HTTP_NOT_MODIFIED === $response->getStatusCode()) {
            return false;
        }

        $linkHeader = $this->initHttpHeader($link, $response->getStatusCode());

        if ($response->getHeaders()->has('Content-Type')) {
            /** @var \Zend\Http\Header\ContentType $contentType */
            $contentType = $response->getHeaders()->get('Content-Type');

            $linkHeader->setContentType($contentType->getMediaType());
        }

        if ($response->getHeaders()->has('Content-Length')) {
            /** @var \Zend\Http\Header\ContentLength $contentLength */
            $contentLength = $response->getHeaders()->get('Content-Length');

            $linkHeader->setContentLength($contentLength->getFieldValue());
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

        if ($response->getHeaders()->has('Content-MD5')) {
            /** @var \Zend\Http\Header\ContentMD5 $md5 */
            $md5 = $response->getHeaders()->get('Content-MD5');

            $linkHeader->setContentMD5($md5->getFieldValue());
        }

        if ($response->getHeaders()->has('Expires')) {
            /** @var \Zend\Http\Header\Expires $expires */
            $expires = $response->getHeaders()->get('Expires');

            $linkHeader->setExpires($expires->date());
        }

        if ($response->getHeaders()->has('Date')) {
            /** @var \Zend\Http\Header\Date $date */
            $date = $response->getHeaders()->get('Date');

            $linkHeader->setDate($date->date());
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
     *
     * @return bool
     */
    protected function analyseContent(UrlInterface $link, $timeout = 10.)
    {
        $response = $this->client->get($link, $timeout);
        if (in_array($response->getStatusCode(), [Response::HTTP_NO_CONTENT, Response::HTTP_NOT_MODIFIED])) {
            return false;
        }

        foreach ($this->analysers as $analyser) {
            $support = $analyser->support($link->getHttpHeader()->getContentType(), $link->getHost());
            $supportPresumed = false;

            if (!$support) {
                $supportPresumed = $analyser->support($link->getHttpHeader()->getContentTypePresumed(), $link->getHost());

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

            if ($report->hasProvider()) {
                $link->addProvider($report->getProvider());
            }

            $link->addOutUrls($report->getUrlsFound());
        }

        return true;
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
