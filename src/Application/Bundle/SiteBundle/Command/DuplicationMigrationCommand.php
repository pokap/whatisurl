<?php

namespace Application\Bundle\SiteBundle\Command;

use Application\Bundle\SiteBundle\Document\Url;
use Application\Bundle\SiteBundle\Document\UrlDirection;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DuplicationMigrationCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('wiu:migration:duplication');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $confirmation = $this->getHelper('dialog')->askConfirmation($output, '<error>WARNING! do? (y/N)</error>', false);
        if (!$confirmation) {
            $output->writeln('<comment>Cancelled!</comment>');
            return 1;
        }

        $coll = $this->getDoctrineManager()->getDocumentCollection(Url::class)->getMongoCollection();
        $query = $coll->find([], ['hash' => true]);
        $count = $query->count();

        $progress = new ProgressBar($output, $count);
        $progress->setBarCharacter('<comment>=</comment>');
        $progress->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');

        foreach (iterator_to_array($query) as $url) {
            $progress->advance();

            $this->cleanDuplicate($url);
        }

        $progress->finish();

        $output->writeln('');
        $output->writeln('<info>Done.</info>');
    }

    /**
     * @param array $url
     *
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    private function cleanDuplicate(array $url)
    {
        $coll = $this->getDoctrineManager()->getDocumentCollection(Url::class)->getMongoCollection();

        $query = $coll->find(['hash' => $url['hash'], '_id' => ['$ne' => $url['_id']]], ['_id' => true]);
        $count = $query->count();

        if ($count === 0) {
            return;
        }

        foreach (iterator_to_array($query) as $duplicate) {
            $this->removeDirection($duplicate);

            $coll->remove(['_id' => $duplicate['_id']], ['justOne' => true]);
        }
    }

    /**
     * @param array $url
     *
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    private function removeDirection(array $url)
    {
        $coll = $this->getDoctrineManager()->getDocumentCollection(UrlDirection::class)->getMongoCollection();
        $coll->remove(['$or' => [['to.$id' => $url['_id']], ['from.$id' => $url['_id']]]], ['justOne' => false]);
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
