<?php

namespace AlephTools\DDD\Common\Infrastructure;

use Closure;
use Traversable;
use DateTimeInterface;
use AlephTools\DDD\Common\Infrastructure\Enums\AbstractEnum;

/**
 * The utility class for the hash computing.
 */
class Hash
{
    /**
     * Returns the hash of an item.
     *
     * @param mixed $item Any data to be hashed.
     * @param string $algorithm Name of selected hashing algorithm (e.g. "md5", "sha256", "haval160,4", etc..)
     * @param bool $rawOutput When set to TRUE, outputs raw binary data. FALSE outputs lowercase hexits.
     * @return string
     */
    public static function of($item, string $algorithm = 'md5', bool $rawOutput = false): string
    {
        if (is_object($item)) {
            return self::hashOfObject($item, $algorithm, $rawOutput);
        }
        if (is_array($item)) {
            return self::hashOfArray($item, $algorithm, $rawOutput);
        }
        if (is_resource($item)) {
            return self::hashOfResource($item, $algorithm, $rawOutput);
        }
        return hash($algorithm, serialize($item), $rawOutput);
    }

    private static function hashOfArray(array $item, string $algorithm, bool $rawOutput): string
    {
        foreach ($item as &$value) {
            if (is_object($value)) {
                $value = self::hashOfObject($value, $algorithm, $rawOutput);
            } elseif (is_array($value)) {
                $value = self::hashOfArray($value, $algorithm, $rawOutput);
            } elseif (is_resource($value)) {
                $value = self::hashOfResource($value, $algorithm, $rawOutput);
            }
        }
        return hash($algorithm, serialize($item), $rawOutput);
    }

    private static function hashOfObject(object $item, string $algorithm, bool $rawOutput): string
    {
        if ($item instanceof Hashable) {
            return $item->hash();
        }
        if ($item instanceof Traversable) {
            return self::of(iterator_to_array($item, true), $algorithm, $rawOutput);
        }
        if ($item instanceof Closure) {
            return self::of($item(), $algorithm, $rawOutput);
        }
        return hash($algorithm, serialize($item), $rawOutput);
    }

    private static function hashOfResource($item, string $algorithm, bool $rawOutput): string
    {
        $hash = new \stdClass();
        $hash->resource = get_resource_type($item);
        $hash->value = (int)$item;
        return hash($algorithm, serialize($hash), $rawOutput);
    }
}