<?php

namespace Application\Bundle\SiteBundle\Consumer;

use Application\Bundle\SiteBundle\Document\Url;
use Application\Bundle\SiteBundle\Manager\UrlManager;
use Application\Component\Link\ParserInterface;
use Sonata\NotificationBundle\Backend\BackendInterface;
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
     * @var UrlManager
     */
    protected $urlManager;

    /**
     * @var BackendInterface
     */
    protected $backend;

    /**
     * Constructor.
     *
     * @param ParserInterface  $parser
     * @param UrlManager       $urlManager
     * @param BackendInterface $backend
     */
    public function __construct(ParserInterface $parser, UrlManager $urlManager, BackendInterface $backend)
    {
        $this->parser = $parser;
        $this->urlManager = $urlManager;
        $this->backend = $backend;
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

        if (!$this->canBeUpdate($url)) {
            $returnInfo->setReturnMessage(sprintf('Url "%s" ("%s") not update!', $url->getUrl(), $url->getId()));
            return;
        }

        // wait 15 seconds before send a request
        sleep(15);

        $this->parser->update($url);

        $url->setStatus($url::STATUS_COMPLETED);

        if ($url->hasProvider('page')) {
            $this->backend->createAndPublish('web_archive', [
                'url' => (string) $url->getId(),
            ]);
        }

        $this->urlManager->save($url);

        $deep = $message->getValue('deep', 0);
        if ($deep > 0) {
            $deep--;

            foreach ($url->getOut() as $subUrl) {
                $this->async($subUrl, $deep);
            }
        }

        $returnInfo->setReturnMessage(sprintf("ID: %s\nURL: %s", $url->getId(), $url->getUrl()));
    }

    /**
     * Create and publish notification.
     *
     * @param Url $url
     * @param int $deep
     */
    protected function async(Url $url, $deep)
    {
        $url->setStatus($url::STATUS_WAITING);
        $this->urlManager->save($url);

        $this->backend->createAndPublish('parser', [
            'url'  => (string) $url->getId(),
            'deep' => $deep
        ]);
    }

    /**
     * @param Url $url
     *
     * @return bool
     */
    protected function canBeUpdate(Url $url)
    {
        if (!$url->isVisited()) {
            return true;
        }

        if ($url::STATUS_WAITING !== $url->getStatus()) {
            return false;
        }

        $date = (new \DateTime())->sub(new \DateInterval('P1W'));
        if ($url->getUpdatedAt() > $date) {
            return false;
        }

        return true;
    }
}
