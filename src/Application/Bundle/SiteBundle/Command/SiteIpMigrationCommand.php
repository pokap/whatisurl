<?php

namespace Application\Bundle\SiteBundle\Command;

use Application\Bundle\SiteBundle\Document\Site;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SiteIpMigrationCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('wiu:migration:site-ip');
        $this->setDescription('Check that hosts has been always the same ip.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $coll = $this->getDoctrineManager()->getDocumentCollection(Site::class)->getMongoCollection();
        $query = $coll->find([], ['ip' => true, 'hosts' => true]);

        foreach (iterator_to_array($query) as $site) {
            foreach ($site['hosts'] as $num => $host) {
                $ip = gethostbyname($host);

                // fail or nothing changed
                if ($ip === $host || $ip === $site['ip']) {
                    continue;
                }

                $output->writeln(sprintf('Host "%s" change from "%s" to "%s".', $host, $site['ip'], $ip));

                $this->updateSite($ip, $host);

                $coll->update(['_id' => $site['_id']], ['$pull' => ['hosts' => $host]]);
            }
        }

        $output->writeln('');
        $output->writeln('<info>Done.</info>');
    }

    /**
     * @param string $ip
     * @param string $host
     *
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    private function updateSite($ip, $host)
    {
        $coll = $this->getDoctrineManager()->getDocumentCollection(Site::class)->getMongoCollection();

        $site = $coll->findOne(['ip' => $ip], ['_id' => true]);

        if (null === $site) {
            $site = [
                'ip'                => $ip,
                'hosts'             => [$host],
                'last_access_at'    => new \MongoDate()
            ];

            $coll->insert($site);
        } else {
            $coll->update(['_id' => $site['_id']], ['$push' => ['hosts' => $host]]);
        }
    }

    /**
     * Returns the doctrine document manager.
     *
     * @return \Doctrine\ODM\MongoDB\DocumentManager
     */
    private function getDoctrineManager()
    {
        return $this->getContainer()->get('doctrine.odm.mongodb.document_manager');
    }
}
