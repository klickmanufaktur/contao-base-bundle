<?php

namespace Klickmanufaktur\ContaoBaseBundle\Service\DCA;

use Contao\CoreBundle\DataContainer\PaletteManipulator;

class HeadlineInjectorService
{
    /**
     * Fügt automatisch die Felddefinition für "headline_color" hinzu und injiziert das Feld
     * in jede Palette des übergebenen Tables, in der ein "headline"-Feld vorkommt.
     *
     * @param string $table Name der Tabelle (z. B. 'tl_news')
     */
    public static function inject(string $table): void
    {
        // Sicherstellen, dass die Felder-Subarray existiert
        $GLOBALS['TL_DCA'][$table]['fields'] ??= [];

        // Felddefinition hinzufügen, falls nicht bereits vorhanden
        if (!isset($GLOBALS['TL_DCA'][$table]['fields']['headline_color'])) {
            $GLOBALS['TL_DCA'][$table]['fields']['headline_color'] = [
                'label'     => ['Überschrift – Farbe', 'Bitte wählen Sie eine Farbe für die Überschrift aus.'],
                'exclude'   => true,
                'inputType' => 'select',
                'options'   => [
                    'default' => 'Standard',
                    'primary' => 'Primär',
                    'green'   => 'Grün',
                    'blue'    => 'Blau',
                    'red'     => 'Rot',
                ],
                'default' => 'default',
                'eval'    => ['maxlength' => 255, 'tl_class' => 'w25'],
                'sql'     => "varchar(255) NOT NULL default ''",
            ];
        }

        if (!isset($GLOBALS['TL_DCA'][$table]['fields']['headline_isDisplay'])) {
            $GLOBALS['TL_DCA'][$table]['fields']['headline_isDisplay'] = [
                'label'     => ['Überschrift – Display', 'Bitte wählen Sie aus, ob die Überschrift vergrößert werden soll.'],
                'exclude'   => true,
                'inputType' => 'checkbox',
                'default' => 0,
                'eval'    => ['tl_class' => 'w25'],
                'sql' => ['type' => 'boolean', 'default' => true],
            ];
        }

        // Palette anpassen: Alle Paletten des Tables durchlaufen, sofern vorhanden
        $palettes = $GLOBALS['TL_DCA'][$table]['palettes'] ?? [];
        if (is_array($palettes)) {
            foreach ($palettes as $paletteName => &$paletteDefinition) {
                // Nur String-Paletten berücksichtigen, die "headline" enthalten
                if (is_string($paletteDefinition) && str_contains($paletteDefinition, 'headline')) {
                    try {
                        PaletteManipulator::create()
                            ->addField('headline_isDisplay', 'headline', PaletteManipulator::POSITION_AFTER)
                            ->addField('headline_color', 'headline', PaletteManipulator::POSITION_AFTER)
                            ->applyToPalette($paletteName, $table);
                    } catch (\Throwable $e) {
                        // Optional: Fehlerprotokollierung, z. B.:
                        // error_log(sprintf('HeadlineColorInjectorService error in table "%s": %s', $table, $e->getMessage()));
                    }
                }
            }
        }
    }
}
