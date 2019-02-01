<?php

namespace Application\Bundle\SiteBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class ApplicationSiteExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/services'));
        $loader->load('analysers.yml');
        $loader->load('async_producer.yml');
        $loader->load('consumer.yml');
        $loader->load('factories.yml');
        $loader->load('link.yml');
        $loader->load('managers.yml');
        $loader->load('repositories.yml');
        $loader->load('transformers.yml');

        $this->addClassesToCompile(array(
            // controllers
//            'Application\\Bundle\\SiteBundle\\Controller\\Front\\AppController',

            // models
            'Application\\Bundle\\SiteBundle\\Document\\Url',
            'Application\\Bundle\\SiteBundle\\Document\\HttpHeader',
            'Application\\Component\\Link\\Domain\\Url',
            'Application\\Component\\Link\\Domain\\UrlInterface',
            'Application\\Component\\Link\\Domain\\HttpHeader',
            'Application\\Component\\Link\\Domain\\HttpHeaderInterface',

            // factories
            'Application\\Bundle\\SiteBundle\\Factory\\SiteFactory',
            'Application\\Bundle\\SiteBundle\\Factory\\UrlFactory',
            'Application\\Component\\Link\\Factory\\SiteFactory',
            'Application\\Component\\Link\\Factory\\SiteFactoryInterface',
            'Application\\Component\\Link\\Factory\\UrlFactory',
            'Application\\Component\\Link\\Factory\\UrlFactoryInterface',

            // managers
            'Application\\Bundle\\SiteBundle\\Manager\\UrlManager',
            'Application\\Bundle\\SiteBundle\\Manager\\UrlDirectionManager',
            'Application\\Bundle\\SiteBundle\\Repository\\SiteRepository',
            'Application\\Bundle\\SiteBundle\\Repository\\SiteRepositoryInterface',
            'Application\\Bundle\\SiteBundle\\Repository\\UrlRepository',
            'Application\\Bundle\\SiteBundle\\Repository\\UrlRepositoryInterface',
            'Application\\Bundle\\SiteBundle\\Repository\\UrlDirectionRepository',
            'Application\\Bundle\\SiteBundle\\Repository\\UrlDirectionRepositoryInterface',
            'Application\\Component\\Link\\Manager\\UrlManager',
            'Application\\Component\\Link\\Manager\\UrlManagerInterface',

            // http client
            'Application\\Component\\Link\\HttpClient',
            'Application\\Component\\Link\\HttpClientInterface',

            // zend
            'Zend\\Http\\Client',
            'Zend\\Uri\\Uri',
            'Zend\\Uri\\UriInterface',
        ));
    }
}
