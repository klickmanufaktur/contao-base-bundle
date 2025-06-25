<?php

namespace Klickmanufaktur\ContaoBaseBundle\Service\DCA;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

class ContentElementPaletteGeneratorService {

    public static function generate(): void {
        $baseDir = __DIR__ . '/../../../src/Controller/ContentElement';
        $baseNamespace = 'App\\Controller\\ContentElement';

        $directory = new RecursiveDirectoryIterator($baseDir);
        $iterator = new RecursiveIteratorIterator($directory);
        $phpFiles = new RegexIterator($iterator, '/^.+\.php$/i', \RecursiveRegexIterator::GET_MATCH);

        foreach ($phpFiles as $files) {
            $file = $files[0];

            // Erzeuge den vollqualifizierten Klassennamen basierend auf dem Dateipfad
            $relativePath = str_replace($baseDir . DIRECTORY_SEPARATOR, '', $file);
            $classPath = str_replace(['/', '\\'], '\\', $relativePath);
            $className = $baseNamespace . '\\' . str_replace('.php', '', $classPath);

            if (class_exists($className)) {
                if (method_exists($className, 'getPalettes') && defined("$className::TYPE")) {
                    $GLOBALS['TL_DCA']['tl_content']['palettes'][$className::TYPE] = $className::getPalettes();
                }

                if (method_exists($className, 'getFields')) {
                    try {
                        $fields = $className::getFields();
                        foreach ($fields as $fieldName => $config) {
                            $GLOBALS['TL_DCA']['tl_content']['fields'][$fieldName] = $config;
                        }
                    } catch (\LogicException $e) {
                        // Optional: Fehlerbehandlung
                    }
                }
            }
        }
    }

}
