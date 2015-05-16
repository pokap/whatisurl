<?php

namespace Application\Bundle\SiteBundle\AsyncProducer;

use Application\Bundle\SiteBundle\AbstractAsyncProducer;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class WebArchiveAsyncProducer extends AbstractAsyncProducer
{
    /**
     * {@inheritdoc}
     */
    public function send(array $options = [])
    {
        $options = $this->resolve($options);

        /** @var \Application\Bundle\SiteBundle\Document\Url $url */
        $url = $options['url'];

        $this->publish([
            'url' => (string) $url->getId(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'web_archive';
    }

    /**
     * Configure options.
     *
     * @param OptionsResolver $resolver
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['url']);

        $resolver->setAllowedTypes([
            'url' => 'Application\Bundle\SiteBundle\Document\Url'
        ]);
    }
}
