<?php

namespace Klickmanufaktur\ContaoBaseBundle\Traits\DCA;

/**
 * Trait HasPalettes
 *
 * @property static string $palettes
 */
trait HasPalettes
{
    public static function getPalettes(): string
    {
        if (empty(static::$palettes)) {
            throw new \LogicException('Please define a non-empty static $palettes property in ' . static::class);
        }
        return static::$palettes;
    }
}