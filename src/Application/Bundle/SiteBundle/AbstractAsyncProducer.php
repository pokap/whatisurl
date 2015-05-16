<?php

namespace Application\Bundle\SiteBundle;

use Sonata\NotificationBundle\Backend\BackendInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
abstract class AbstractAsyncProducer implements AsyncProducerInterface
{
    /**
     * @var BackendInterface
     */
    protected $backend;

    /**
     * Constructor.
     *
     * @param BackendInterface $backend
     */
    public function __construct(BackendInterface $backend)
    {
        $this->backend = $backend;
    }

    /**
     * Create and publish in the queue.
     *
     * @param array $body
     */
    final protected function publish(array $body)
    {
        $this->backend->createAndPublish($this->getType(), $body);
    }

    /**
     * Resolve options.
     *
     * @param array $options
     *
     * @return array
     */
    final protected function resolve(array $options = [])
    {
        $this->configureOptions($resolver = new OptionsResolver());

        return $resolver->resolve($options);
    }

    /**
     * Configure options.
     *
     * @param OptionsResolver $resolver
     */
    abstract protected function configureOptions(OptionsResolver $resolver);
}
