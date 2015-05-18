<?php

namespace Application\Bundle\SiteBundle\Consumer;

use Application\Bundle\SiteBundle\AsyncProducer\ParserAsyncProducer;
use Application\Bundle\SiteBundle\AsyncProducer\WebArchiveAsyncProducer;
use Application\Bundle\SiteBundle\Document\Site;
use Application\Bundle\SiteBundle\Document\Url;
use Application\Bundle\SiteBundle\Document\UrlDirection;
use Application\Bundle\SiteBundle\Manager\UrlDirectionManager;
use Application\Bundle\SiteBundle\Manager\UrlManager;
use Application\Bundle\SiteBundle\Repository\SiteRepositoryInterface;
use Application\Component\Link\Factory\ParserReportFactoryInterface;
use Application\Component\Link\ParserInterface;
use Sonata\NotificationBundle\Consumer\ConsumerEvent;
use Sonata\NotificationBundle\Consumer\ConsumerInterface;
use Sonata\NotificationBundle\Consumer\ConsumerReturnInfo;

/**
 * ParserConsumer
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class ParserConsumer implements ConsumerInterface
{
    /**
     * @var ParserInterface
     */
    protected $parser;

    /**
     * @var ParserReportFactoryInterface
     */
    protected $parserReportFactory;

    /**
     * @var UrlManager
     */
    protected $urlManager;

    /**
     * @var UrlDirectionManager
     */
    protected $urlDirectionManager;

    /**
     * @var SiteRepositoryInterface
     */
    protected $siteRepository;

    /**
     * @var ParserAsyncProducer
     */
    protected $parserAsync;

    /**
     * @var WebArchiveAsyncProducer
     */
    protected $webArchiveAsync;

    /**
     * Constructor.
     *
     * @param ParserInterface              $parser
     * @param ParserReportFactoryInterface $parserReportFactory
     * @param UrlManager                   $urlManager
     * @param UrlDirectionManager          $urlDirectionManager
     * @param SiteRepositoryInterface      $siteRepository
     * @param ParserAsyncProducer          $parserAsync
     * @param WebArchiveAsyncProducer      $webArchiveAsync
     */
    public function __construct(
        ParserInterface $parser,
        ParserReportFactoryInterface $parserReportFactory,
        UrlManager $urlManager,
        UrlDirectionManager $urlDirectionManager,
        SiteRepositoryInterface $siteRepository,
        ParserAsyncProducer $parserAsync,
        WebArchiveAsyncProducer $webArchiveAsync)
    {
        $this->parser = $parser;
        $this->parserReportFactory = $parserReportFactory;
        $this->urlManager = $urlManager;
        $this->urlDirectionManager = $urlDirectionManager;
        $this->siteRepository = $siteRepository;
        $this->parserAsync = $parserAsync;
        $this->webArchiveAsync = $webArchiveAsync;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ConsumerEvent $event)
    {
        $event->setReturnInfo($returnInfo = new ConsumerReturnInfo());
        $message = $event->getMessage();

        /** @var Url $url */
        $url = $this->urlManager->find($message->getValue('url'));
        if (null === $url) {
            throw new \RuntimeException(sprintf('URL with ID "%s" not found.', $message->getValue('url')));
        }

        if (!$this->canBeUpdate($url)) {
            $returnInfo->setReturnMessage(sprintf('Url "%s" ("%s") not update!', $url->getUrl(), $url->getId()));
        } else {
            $this->update($url);

            $returnInfo->setReturnMessage(sprintf("ID: %s\nURL: %s", $url->getId(), $url->getUrl()));
        }

        $deep = $message->getValue('deep', 0);
        if ($deep > 0 || $deep === -1) {
            if ($deep > 0) {
                $deep--;
            }

            $outLinks = [$url->getHash()];

            /** @var Url $subUrl */
            foreach ($url->getOut() as $subUrl) {
                if (in_array($subUrl->getHash(), $outLinks)) {
                    continue;
                }

                $outLinks[] = $subUrl->getHash();

                $this->async($subUrl, $deep);

                if (!$this->urlDirectionManager->exists($url, $subUrl)) {
                    $direction = new UrlDirection();
                    $direction->setFrom($url);
                    $direction->setTo($subUrl);

                    $this->urlDirectionManager->save($direction);
                }
            }

            unset($outLinks);
        }
    }

    /**
     * Parse and persist the Url information.
     *
     * @param Url $url
     */
    private function update(Url $url)
    {
        /** @var Site|null $site */
        $site = $this->siteRepository->findOneByHost($url->getHost());

        // wait 15 sec
        $sleep = 15;

        $report = $this->parserReportFactory->create($url);

        if (null !== $site) {
            $interval = time() - $site->getLastAccessAt()->getTimestamp();

            if ($interval >= $sleep) {
                $sleep = 0;
            } else {
                $sleep -= $interval;
            }

            $report->setSite($site);
        }

        // wait 15 seconds before send a request
        sleep($sleep);

        $this->parser->update($report, 300);

        if (null !== $report->getSite()) {
            $site = $report->getSite();

            $this->siteRepository->save($site);
        }

        $url->setStatus($url::STATUS_COMPLETED);

        if ($url->hasProvider('page')) {
            $this->webArchiveAsync->send(['url' => $url]);
        }

        $this->urlManager->save($url);
    }

    /**
     * Create and publish notification.
     *
     * @param Url $url
     * @param int $deep
     */
    private function async(Url $url, $deep)
    {
        $url->setStatus($url::STATUS_WAITING);

        $this->parserAsync->send(['url' => $url, 'deep' => $deep]);

        $this->urlManager->save($url);
    }

    /**
     * @param Url $url
     *
     * @return bool
     */
    private function canBeUpdate(Url $url)
    {
        if (!$url->isVisited()) {
            return true;
        }

        if ($url::STATUS_WAITING !== $url->getStatus()) {
            return false;
        }

        return !$this->urlManager->isUpToDate($url);
    }
}
