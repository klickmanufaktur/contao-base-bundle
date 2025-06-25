<?php

namespace Klickmanufaktur\ContaoBaseBundle\Service\DCA;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

class FrontendModulePaletteGeneratorService {

    public static function generate(): void {
        $baseDir       = __DIR__ . '/../../../src/Controller/FrontendModule';
        $baseNamespace = 'App\\Controller\\FrontendModule';

        $directory = new RecursiveDirectoryIterator($baseDir);
        $iterator  = new RecursiveIteratorIterator($directory);
        $phpFiles  = new RegexIterator($iterator, '/^.+\.php$/i', RegexIterator::GET_MATCH);

        foreach ($phpFiles as $files) {
            $file = $files[0];

            // Erzeuge den relativen Pfad ab $baseDir (ohne führenden Slash)
            $relativePath = ltrim(str_replace($baseDir, '', $file), DIRECTORY_SEPARATOR);

            // Ersetze Verzeichnis-Trennzeichen durch Backslashes und entferne ".php"
            $classPath    = str_replace(DIRECTORY_SEPARATOR, '\\', substr($relativePath, 0, -4));

            // Zusammensetzen: Namespace + "\" + Klassenname (z. B. "Events\ToubizEventReaderModule")
            $className    = $baseNamespace . '\\' . $classPath;

            if (class_exists($className)) {
                if (method_exists($className, 'getPalettes') && defined("$className::TYPE")) {
                    $GLOBALS['TL_DCA']['tl_module']['palettes'][$className::TYPE] = $className::getPalettes();
                }

                if (method_exists($className, 'getFields')) {
                    try {
                        $fields = $className::getFields();
                        foreach ($fields as $fieldName => $config) {
                            $GLOBALS['TL_DCA']['tl_module']['fields'][$fieldName] = $config;
                        }
                    } catch (\LogicException $e) {
                        // Optional: Fehlerbehandlung
                    }
                }
            }
        }
    }

}
