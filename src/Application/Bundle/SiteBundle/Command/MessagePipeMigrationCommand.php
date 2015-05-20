<?php

namespace Application\Bundle\SiteBundle\Command;

use Application\Bundle\SiteBundle\Document\Notification;
use Application\Bundle\SiteBundle\Document\Url;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MessagePipeMigrationCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('wiu:migration:message-pipe');
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

        $coll = $this->getDoctrineManager()->getDocumentCollection(Notification::class)->getMongoCollection();
        $query = $coll->find(['type' => 'parser', 'group' => ['$exists' => false]], ['body' => true]);
        $count = $query->count();

        $progress = new ProgressBar($output, $count);
        $progress->setBarCharacter('<comment>=</comment>');
        $progress->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');

        foreach ($query as $message) {
            $progress->advance();

            $this->createGroup($message);
        }

        $progress->finish();

        $output->writeln('');
        $output->writeln('<info>Done.</info>');
    }

    /**
     * @param array $message
     *
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    private function createGroup(array $message)
    {
        $collMessage = $this->getDoctrineManager()->getDocumentCollection(Notification::class)->getMongoCollection();

        if (empty($message['body']['url'])) {
            $collMessage->remove(['_id' => $message['_id']]);
            return;
        }

        $coll = $this->getDoctrineManager()->getDocumentCollection(Url::class)->getMongoCollection();
        $url = $coll->findOne(['_id' => new \MongoId($message['body']['url'])], ['host' => true]);

        if (null === $url) {
            $collMessage->remove(['_id' => $message['_id']]);
        } else {
            $collMessage->update(['_id' => $message['_id']], ['$set' => ['group' => $url['host']]]);
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
