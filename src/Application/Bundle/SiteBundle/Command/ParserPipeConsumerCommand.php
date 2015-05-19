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
    protected $commands = [];

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
        $rootDir = dirname($this->getContainer()->getParameter('kernel.root_dir')); // app dir
        $env = $this->getContainer()->getParameter('kernel.environment');

        $loop = ReactFactory::create();
        $loop->addPeriodicTimer(1, function(TimerInterface $timer) use ($loop, $output, $rootDir, $env) {
            $groups = $this->getNotificationManager()->groupByGroup(['parser'], MessageInterface::STATE_OPEN);

            foreach ($groups as $host => $count) {
                if (isset($this->commands[$host])) {
                    continue;
                }

                $this->commands[$host] = sprintf('cd %s && app/console wiu:notification:parser %s --iteration=250 --env=%s', $rootDir, $host, $env);
            }

            foreach ($this->commands as $key => $command) {
                $process = new Process($command);
                $process->on('exit', function($exitCode, $termSignal) use ($key, $output) {
                    $output->writeln(sprintf('-  Command "%s" exit with code: %d.', $this->commands[$key], $exitCode));

                    unset($this->commands[$key]);
                });

                $loop->addTimer(0.1, function(TimerInterface $timer) use ($process, $output) {
                    $process->start($timer->getLoop());

                    $process->stdout->on('data', function($data) use ($process, $output) {
//                        $output->writeln(sprintf('<info> + Process "%s" says:</info>', $process->getCommand()));
                        $output->write($data);
                    });
                });
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
