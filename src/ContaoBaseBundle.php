<?php
namespace Klickmanufaktur\ContaoBaseBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

class ContaoBaseBundle extends AbstractBundle
{
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        // Pfad relativ zu src/ContaoBaseBundle.php → ../config/services.yaml
        $container->import(__DIR__ . '/config/services.yaml');
    }
}
