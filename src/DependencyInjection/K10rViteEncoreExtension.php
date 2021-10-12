<?php

declare(strict_types=1);

namespace K10r\ViteEncoreBundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class K10rViteEncoreExtension extends Extension
{
    /**
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('services.yml');

        $config = $this->processConfiguration(
            $this->getConfiguration($configs, $container),
            $configs
        );

        $serverUrlParts = [
            $config['server']['https'] ? 'https://' : 'http://',
            $config['server']['host'],
            ':',
            $config['server']['port'],
            '/',
        ];

        $container->setParameter('k10r_vite_encore.base', $config['base']);
        $container->setParameter('k10r_vite_encore.use_server', $config['server']['enabled']);
        $container->setParameter(
            'k10r_vite_encore.server',
            implode($serverUrlParts)
        );
    }
}
