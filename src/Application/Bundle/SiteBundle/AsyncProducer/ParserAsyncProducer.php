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
     * @var SiteRepositoryInterface
     */
    protected $siteRepository;

    /**
     * {@inheritdoc}
     *
     * @param SiteRepositoryInterface $siteRepository
     */
    public function __construct(BackendInterface $backend, SiteRepositoryInterface $siteRepository)
    {
        parent::__construct($backend);

        $this->siteRepository = $siteRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function send(array $options = [])
    {
        $options = $this->resolve($options);

        /** @var Url $url */
        $url = $options['url'];

        $site = $this->retrieveSite($url);

        $this->publish([
            'url'  => (string) $url->getId(),
            'ip'   => $site->getIp(),
            'deep' => $options['deep'],
        ]);
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

    /**
     * Returns the site given by host url.
     *
     * @param Url $url
     *
     * @return Site
     *
     * @throws \RuntimeException When site is not found.
     */
    private function retrieveSite(Url $url)
    {
        $site = $this->siteRepository->findOneByHost($url->getHost());

        if (null === $site) {
            throw new \RuntimeException(sprintf('Site not found for the host "%s".', $url->getHost()));
        }

        return $site;
    }
}
