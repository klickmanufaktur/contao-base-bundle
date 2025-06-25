<?php

namespace Klickmanufaktur\ContaoBaseBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class KlickmanufakturContaoBaseExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        // Pfad relativ zu dieser Datei: src/DependencyInjection -> src/Resources/config
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.yaml');
    }
}