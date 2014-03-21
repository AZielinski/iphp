<?php

namespace Az\iPhp\Initializer;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\RegisterListenersPass;

class BasicInitializer implements InitializerInterface
{

    // @TODO: Support for .rc file
    public function initialize()
    {
        $container = new ContainerBuilder();
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $container->addCompilerPass(new RegisterListenersPass('iphp.event_dispatcher', 'iphp.event_listener', 'iphp.event_subscriber'));
        $container->compile();

        $container->get('iphp.runner')->run();
    }

}
