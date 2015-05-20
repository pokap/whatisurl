<?php

namespace Application\Bundle\SiteBundle\Command;

use Application\Bundle\SiteBundle\Document\Site;
use Application\Bundle\SiteBundle\Document\Url;
use Application\Bundle\SiteBundle\Document\UrlDirection;
use Application\Bundle\SiteBundle\Iterator\MessageManagerMessageIterator;
use Sonata\NotificationBundle\Model\MessageInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class ParserConsumerCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->setName('wiu:notification:parser');
        $this->setDescription('Parser.');
        $this->addArgument('host', InputArgument::REQUIRED);
        $this->addOption('iteration', 'i', InputOption::VALUE_OPTIONAL, 'Only run n iterations before exiting', false);
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $startMemoryUsage = memory_get_usage(true);
        $i = 0;
        $iterator = $this->createIterator($input->getArgument('host'));
        foreach ($iterator as $message) {
            $i++;

            if (!$message instanceof MessageInterface) {
                throw new \RuntimeException('The iterator must return a MessageInterface instance');
            }

            $date = new \DateTime();
            $output->write(sprintf("[%s] #%s: ", $date->format('r'), $i));
            $memoryUsage = memory_get_usage(true);

            try {
                $start = microtime(true);

                $message->setStartedAt(new \DateTime());
                $message->setState(MessageInterface::STATE_IN_PROGRESS);
                $this->getNotificationManager()->save($message);

                $this->process($message, $output);

                $message->setCompletedAt(new \DateTime());
                $message->setState(MessageInterface::STATE_DONE);
                $this->getNotificationManager()->save($message);

                $currentMemory = memory_get_usage(true);

                $output->writeln(sprintf("<comment>OK! </comment> - %0.04fs, %ss, %s, %s - %s = %s, %0.02f%%",
                    microtime(true) - $start,
                    $date->format('U') - $message->getCreatedAt()->format('U'),
                    $this->formatMemory($currentMemory - $memoryUsage),
                    $this->formatMemory($currentMemory),
                    $this->formatMemory($startMemoryUsage),
                    $this->formatMemory($currentMemory - $startMemoryUsage),
                    ($currentMemory - $startMemoryUsage) / $startMemoryUsage * 100
                ));
            } catch (\Exception $e) {
                $output->writeln(sprintf("<error>KO! - %s</error>", $e->getMessage()));
            }

            if ($input->getOption('iteration') && $i >= (int) $input->getOption('iteration')) {
                $output->writeln('End of iteration cycle');

                return;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function process(MessageInterface $message, OutputInterface $output)
    {
        /** @var Url $url */
        $url = $this->getUrlManager()->find($message->getValue('url'));
        if (null === $url) {
            throw new \RuntimeException(sprintf('URL with ID "%s" not found.', $message->getValue('url')));
        }

        if (!$this->canBeUpdate($url)) {
            $output->writeln(sprintf('<comment>Url "%s" ("%s") not update!</comment>', $url->getUrl(), $url->getId()));
        } else {
            $this->update($url);

            $output->writeln(sprintf('<info>ID: %s, URL: %s</info>', $url->getId(), $url->getUrl()));
        }

        $deep = (int) $message->getValue('deep');
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

                $subUrl->setStatus($subUrl::STATUS_WAITING);
                $this->getUrlManager()->save($subUrl);

                if (!$this->getUrlDirectionManager()->exists($url, $subUrl)) {
                    $direction = new UrlDirection();
                    $direction->setFrom($url);
                    $direction->setTo($subUrl);

                    $this->getUrlDirectionManager()->save($direction);
                }

                $this->async($subUrl, $deep);
            }
        }
    }

    /**
     * Parse and persist the Url information.
     *
     * @param Url $url
     */
    protected function update(Url $url)
    {
        /** @var Site|null $site */
        $site = $this->getSiteRepository()->findOneByHost($url->getHost());

        // wait 15 sec
        $sleep = 15;

        $report = $this->getParserReportFactory()->create($url);

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

        $this->getParser()->update($report, 300);

        if (null !== $report->getSite()) {
            $site = $report->getSite();

            $this->getSiteRepository()->save($site);
        }

        $url->setStatus($url::STATUS_COMPLETED);

        if ($url->hasProvider('page')) {
            $this->getWebArchiveAsyncProducer()->send(['url' => $url]);
        }

        $this->getUrlManager()->save($url);
    }

    /**
     * Create and publish notification.
     *
     * @param Url $url
     * @param int $deep
     */
    protected function async(Url $url, $deep)
    {
        $this->getParserAsyncProducer()->send(['url' => $url, 'deep' => $deep]);
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

        return !$this->getUrlManager()->isUpToDate($url);
    }

    /**
     * Find open messages.
     *
     * @param string $host
     *
     * @return \Iterator
     */
    protected function createIterator($host)
    {
        return new MessageManagerMessageIterator($this->getNotificationManager(), ['parser'], $host);
    }

    /**
     * @param $memory
     *
     * @return string
     */
    private function formatMemory($memory)
    {
        if ($memory < 1024) {
            return $memory."b";
        } elseif ($memory < 1048576) {
            return round($memory / 1024, 2)."Kb";
        }

        return round($memory / 1048576, 2)."Mb";
    }

    /**
     * Returns the site repository.
     *
     * @return \Application\Bundle\SiteBundle\Repository\SiteRepository
     */
    private function getSiteRepository()
    {
        return $this->getContainer()->get('site.link.site_repository');
    }

    /**
     * Returns the notification manager.
     *
     * @return \Application\Bundle\SiteBundle\Manager\NotificationManager
     */
    private function getNotificationManager()
    {
        return $this->getContainer()->get('site.notification.manager');
    }

    /**
     * Returns the url manager.
     *
     * @return \Application\Bundle\SiteBundle\Manager\UrlManager
     */
    private function getUrlManager()
    {
        return $this->getContainer()->get('site.link.url_manager');
    }

    /**
     * Returns the url direction manager.
     *
     * @return \Application\Bundle\SiteBundle\Manager\UrlDirectionManager
     */
    private function getUrlDirectionManager()
    {
        return $this->getContainer()->get('site.link.url_direction_manager');
    }

    /**
     * Returns the parser report factory.
     *
     * @return \Application\Component\Link\Factory\ParserReportFactory
     */
    private function getParserReportFactory()
    {
        return $this->getContainer()->get('site.link.parser_report_factory');
    }

    /**
     * Returns the web archive async producer.
     *
     * @return \Application\Bundle\SiteBundle\AsyncProducer\WebArchiveAsyncProducer
     */
    private function getWebArchiveAsyncProducer()
    {
        return $this->getContainer()->get('site.link.web_archive_async_producer');
    }

    /**
     * Returns the parser async producer.
     *
     * @return \Application\Bundle\SiteBundle\AsyncProducer\ParserAsyncProducer
     */
    private function getParserAsyncProducer()
    {
        return $this->getContainer()->get('site.link.parser_async_producer');
    }

    /**
     * Returns the parser.
     *
     * @return \Application\Component\Link\Parser
     */
    private function getParser()
    {
        return $this->getContainer()->get('site.link.parser');
    }
}
