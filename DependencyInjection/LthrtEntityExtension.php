<?php

namespace Lthrt\EntityBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Parser;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class LthrtEntityExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(
        array            $configs,
        ContainerBuilder $container
    ) {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);
        $loader        = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
        $env = $container->getParameter("kernel.environment");
        if ('test' == $env) {
            $container->setParameter('class_aliases', $this->getTestAliases($container));
        } else {
            $container->setParameter('class_aliases', $this->getClassAliases($container));
        }

    }

    public function getAliases(
        $config,
        $aliases = []
    ) {
        $yml         = new Parser();
        $readAliases = $yml->parse(file_get_contents($config));
        if (isset($readAliases['class_aliases'])) {
            foreach ($readAliases['class_aliases'] as $alias => $class) {
                $aliases[$alias] = $class;
            }
        }

        return $aliases;
    }

    public function getClassAliases(ContainerBuilder $container)
    {
        $config = $container->getParameter('kernel.root_dir') . '/config/aliases.yml';
        if (file_exists($config)) {
            return $this->getAliases($config);
        } else {
            return [];
        }
    }

    public function getTestAliases(ContainerBuilder $container)
    {
        $aliases = $this->getClassAliases($container);
        $config  = __DIR__ . '/../Resources/config/aliases_test.yml';
        if (file_exists($config)) {
            return $this->getAliases($config, $aliases);
        } else {
            return $aliases;
        }
    }
}
