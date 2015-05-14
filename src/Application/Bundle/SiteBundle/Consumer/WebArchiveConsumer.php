<?php

namespace Application\Bundle\SiteBundle\Consumer;

use Application\Bundle\SiteBundle\Document\Provider\PageProvider;
use Application\Bundle\SiteBundle\Document\Url;
use Application\Bundle\SiteBundle\Document\WebArchive\Snapshot as DocumentSnapshot;
use Application\Bundle\SiteBundle\Document\WebArchive\WebArchive;
use Application\Bundle\SiteBundle\Manager\UrlManager;
use Application\Component\Link\Domain\UrlInterface;
use Sonata\NotificationBundle\Consumer\ConsumerReturnInfo;
use Sonata\NotificationBundle\Consumer\ConsumerEvent;
use Sonata\NotificationBundle\Consumer\ConsumerInterface;
use WebArchive\Provider\WayBackProvider;
use WebArchive\Request;
use WebArchive\Client;
use WebArchive\Snapshot;

/**
 * WebArchiveConsumer
 *
 * @author Florent Denis <dflorent.pokap@ekino.com>
 */
class WebArchiveConsumer implements ConsumerInterface
{
    /**
     * @var UrlManager
     */
    protected $urlManager;

    /**
     * Constructor.
     *
     * @param UrlManager $urlManager
     */
    public function __construct(UrlManager $urlManager)
    {
        $this->urlManager = $urlManager;
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
            throw new \RuntimeException(sprintf('URL with ID "%s" not found.', $message->getValue('id')));
        }

        if (!$url->hasProvider('page')) {
            $returnInfo->setReturnMessage(sprintf('This resource "%s" is not supported.', $url->getUrl()));

            return;
        }

        /** @var PageProvider $page */
        $page = $url->getProviders()['page'];

        // retrieve web archive
        $archive = $page->getArchive();
        if (null === $archive) {
            $page->setArchive($archive = new WebArchive());
        }

        // wait 15 seconds before send a request
        sleep(15);

        $snapshots = $archive->getSnapshots();
        foreach ($this->getSnapshots($url, $archive) as $item) {
            $snapshots[] = $this->transformSnapshot($item);
        }

        $archive->setSnapshots($snapshots);

        $url->addProvider($page);

        $this->urlManager->save($url);

        $returnInfo->setReturnMessage(sprintf('%d snapshots found.', count($snapshots)));
    }

    /**
     * @param Snapshot $snapshot
     *
     * @return DocumentSnapshot
     */
    protected function transformSnapshot(Snapshot $snapshot)
    {
        return new DocumentSnapshot($snapshot->getDate(), $snapshot->getUrl());
    }

    /**
     * Retrieve list of snapshot which are new.
     *
     * @param UrlInterface $url
     * @param WebArchive   $archive
     *
     * @return \WebArchive\Snapshot[]
     */
    private function getSnapshots(UrlInterface $url, WebArchive $archive)
    {
        // mandatory latency
        if ($archive->getUpdatedAt() && time() < $archive->getUpdatedAt()->add(new \DateInterval('P1W'))->getTimestamp()) {
            return [];
        }

        $collection = $this->generateSnapshotCollection($url);
        $snapshots = $archive->getSnapshots();

        if (!empty($snapshots)) {
            // retrieve last snapshot date saved
            /** @var \Datetime $lastDate */
            $lastDate = end($snapshots)->getDate();

            $snapshotList = [];
            /** @var \WebArchive\Snapshot $snapshot */
            foreach ($collection->getSnapshots() as $snapshot) {
                if ($lastDate > $snapshot->getDate()) {
                    continue;
                }

                $snapshotList[] = $snapshot;
            }

            return $snapshotList;
        }

        return $collection->getSnapshots();
    }

    /**
     * Returns an instance of snapshot collection.
     *
     * @param UrlInterface $url
     *
     * @return \WebArchive\SnapshotCollection
     */
    private function generateSnapshotCollection(UrlInterface $url)
    {
        $client = new Client(
            new Request($url->getUrl(), ['timeout' => 10]),
            new WayBackProvider()
        );

        sleep(15);

        return $client->retrieve();
    }
}
