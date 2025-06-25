<?php
namespace Klickmanufaktur\ContaoBaseBundle\ContaoManager;

use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\CoreBundle\ContaoCoreBundle;
use Klickmanufaktur\ContaoBaseBundle\ContaoBaseBundle;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(ContaoBaseBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class]),
        ];
    }
}
