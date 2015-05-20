<?php

namespace Application\Bundle\SiteBundle\Command;

use React\EventLoop\Factory as ReactFactory;
use React\EventLoop\Timer\TimerInterface;
use React\ChildProcess\Process;
use Sonata\NotificationBundle\Model\MessageInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class ParserPipeConsumerCommand extends ContainerAwareCommand
{
    protected $running = [];
    protected $offset = 0;

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->setName('wiu:notification:parser:start');
        $this->setDescription('Parser pipe.');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $groups = $this->getNotificationManager()->groupByGroup(['parser'], MessageInterface::STATE_OPEN);

        $loop = ReactFactory::create();
        $loop->addPeriodicTimer(1, function(TimerInterface $timer) use ($groups, $loop, $output) {
            if (count($this->running) >= 10) {
                return;
            }

            $rootDir = dirname($this->getContainer()->getParameter('kernel.root_dir')); // app dir
            $env = $this->getContainer()->getParameter('kernel.environment');

            $groupToCommand = array_splice($groups, $this->offset, 10 - count($this->running));

            $output->writeln(sprintf('<info>OFFSET %d</info>', $this->offset));

            $commands = [];
            foreach ($groupToCommand as $host => $count) {
                if (in_array($host, $this->running)) {
                    continue;
                }

                $commands[$host] = sprintf('cd %s && app/console wiu:notification:parser "%s" --iteration=10 --env=%s', $rootDir, $host, $env);
            }

            $this->offset += count($commands);

            foreach ($commands as $host => $command) {
                $this->running[] = $host;

                $output->writeln(sprintf('<comment>Start consume host "%s".</comment>', $host));

                $process = new Process($command);
                $process->on('exit', function($exitCode, $termSignal) use ($host, $command, $output) {
                    $output->writeln(sprintf('-  Command "%s" exit with code: %d.', $command, $exitCode));

                    unset($this->running[array_search($host, $this->running)]);
                });

                $loop->addTimer(0.1, function(TimerInterface $timer) use ($host, $process, $output) {
                    $process->start($timer->getLoop());

                    $process->stdout->on('data', function($data) use ($process, $output) {
                        $output->writeln($data);
                    });
                });
            }

            if (empty($this->running) && (($this->offset + 10) >= count($groups))) {
                $loop->stop();
            }
        });

        $loop->run();
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
}
