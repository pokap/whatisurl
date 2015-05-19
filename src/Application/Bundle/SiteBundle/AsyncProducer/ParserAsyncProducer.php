<?php

namespace Application\Bundle\SiteBundle\AsyncProducer;

use Application\Bundle\SiteBundle\AbstractAsyncProducer;
use Application\Bundle\SiteBundle\Document\Site;
use Application\Bundle\SiteBundle\Document\Url;
use Application\Bundle\SiteBundle\Repository\SiteRepositoryInterface;
use Sonata\NotificationBundle\Backend\BackendInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class ParserAsyncProducer extends AbstractAsyncProducer
{
    /**
     * {@inheritdoc}
     */
    public function send(array $options = [])
    {
        $options = $this->resolve($options);

        /** @var Url $url */
        $url = $options['url'];

        $this->publish([
            'url'   => (string) $url->getId(),
            'deep'  => $options['deep'],
        ], $url->getHost());
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'parser';
    }

    /**
     * Configure options.
     *
     * @param OptionsResolver $resolver
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['url']);

        $resolver->setDefaults([
            'deep' => 1
        ]);

        $resolver->setAllowedTypes([
            'url' => 'Application\Bundle\SiteBundle\Document\Url'
        ]);
    }
}
