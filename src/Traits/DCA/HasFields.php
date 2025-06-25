<?php

namespace Klickmanufaktur\ContaoBaseBundle\Traits\DCA;

/**
 * Trait HasFields
 *
 * @property static array $fields
 */
trait HasFields
{
    public static function getFields(): array
    {
        if (empty(static::$fields)) {
            throw new \LogicException('Please define a non-empty static $fields property in ' . static::class);
        }
        return static::$fields;
    }
}