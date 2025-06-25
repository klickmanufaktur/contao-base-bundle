<?php

namespace Klickmanufaktur\ContaoBaseBundle\Traits;

trait EnumToArray {
    public static function toArray(): array
    {
        return array_combine(
            array_column(self::cases(), 'value'),
            array_map(fn($case) => $case->getLabel(), self::cases())
        );
    }
}