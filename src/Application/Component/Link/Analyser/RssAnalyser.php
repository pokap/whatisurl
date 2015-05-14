<?php

namespace Application\Component\Link\Analyser;

use Application\Component\Link\AnalyserInterface;
use Application\Component\Link\Exception\AnalyserFailedException;
use Application\Component\Link\Factory\AnalyseReportFactoryInterface;
use Application\Component\Link\Factory\UrlFactoryInterface;
use Application\Component\Link\Domain\HttpHeader;
use Application\Component\Link\Domain\UrlInterface;
use Application\Component\Link\Domain\Provider\RssProvider;
use Zend\Feed\Reader\Reader as ZendReader;
use Zend\Feed\Reader\Feed\Atom as FeedAtom;
use Zend\Feed\Reader\Exception\ExceptionInterface as ZendReaderExceptionInterface;

/**
 * Service for analysed a RSS.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class RssAnalyser implements AnalyserInterface
{
    /**
     * @var AnalyseReportFactoryInterface
     */
    protected $reportFactory;

    /**
     * @var UrlFactoryInterface
     */
    protected $urlFactory;

    /**
     * Constructor.
     *
     * @param AnalyseReportFactoryInterface $reportFactory
     * @param UrlFactoryInterface          $urlFactory
     */
    public function __construct(AnalyseReportFactoryInterface $reportFactory, UrlFactoryInterface $urlFactory)
    {
        $this->reportFactory = $reportFactory;
        $this->urlFactory = $urlFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function analyse($content, UrlInterface $link)
    {
        try {
            $feed = ZendReader::importString($content);
        } catch (ZendReaderExceptionInterface $e) {
            throw new AnalyserFailedException(sprintf('Rss import content failed. Resource "%s".', $link->getUrl()), 0, $e);
        }

        $provider = $this->newProvider();
        $provider->setTitle($feed->getTitle());

        if (null === $link->getHttpHeader()) {
            $link->setHttpHeader($this->urlFactory->createHttpHeader());
        }

        if ($feed instanceof FeedAtom) {
            $link->getHttpHeader()->setContentTypePresumed('application/atom+xml');
        } else {
            $link->getHttpHeader()->setContentTypePresumed('application/rss+xml');
        }

        return $this->reportFactory->create($provider);
    }

    /**
     * {@inheritdoc}
     */
    public function support($mimeType, $host)
    {
        return in_array($mimeType, ['text/rss+xml', 'application/rss+xml', 'application/atom+xml']);
    }

    /**
     * {@inheritdoc}
     */
    public function getFormat()
    {
        return 'rss';
    }

    /**
     * Create new instance of rss provider.
     *
     * @return RssProvider
     */
    protected function newProvider()
    {
        return new RssProvider();
    }
}
