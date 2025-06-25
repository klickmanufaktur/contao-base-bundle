<?php

namespace Klickmanufaktur\ContaoBaseBundle\Service;

use DateTime;
use Exception;
use IntlDateFormatter;

class DateFormatService {

    public static function convert(string $isoDate): string {
        $date = new DateTime($isoDate);

        $formatter = new IntlDateFormatter(
            'de_DE',                  // Locale
            IntlDateFormatter::FULL,  // Datumsformat (vollständig)
            IntlDateFormatter::NONE,  // Zeitformat (keine Ausgabe)
            'Europe/Berlin'
        );

        $formatter->setPattern("EEEE, d. MMMM yyyy");
        $formattedDate = $formatter->format($date);

        if ($formattedDate === false) {
            throw new Exception("Fehler beim Formatieren des Datums.");
        }

        return $formattedDate;
    }

    public static function convertTstamp(int $timestamp): string {
        $date = (new DateTime())->setTimestamp($timestamp);

        $formatter = new IntlDateFormatter(
            'de_DE',                  // Locale
            IntlDateFormatter::FULL,  // Datumsformat (vollständig)
            IntlDateFormatter::NONE,  // Zeitformat (keine Ausgabe)
            'Europe/Berlin'
        );

        $formatter->setPattern("EEEE, dd. MMMM yyyy");
        $formattedDate = $formatter->format($date);

        if ($formattedDate === false) {
            throw new Exception("Fehler beim Formatieren des Datums.");
        }

        return $formattedDate;
    }

}