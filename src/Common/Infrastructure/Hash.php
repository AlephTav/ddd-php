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
        return self::hashOfScalar($item, $algorithm, $rawOutput);
    }

    private static function hashOfObject(object $item, string $algorithm, bool $rawOutput): string
    {
        if ($item instanceof Hashable) {
            return $item->hash();
        }
        if ($item instanceof AbstractEnum) {
            return self::hashOfScalar($item->toString(), $algorithm, $rawOutput);
        }
        if ($item instanceof DateTimeInterface) {
            return self::hashOfScalar($item->format('U.u'), $algorithm, $rawOutput);
        }
        if ($item instanceof Serializable) {
            return self::hashOfArray([get_class($item), $item->toArray()], $algorithm, $rawOutput);
        }
        if ($item instanceof Traversable) {
            return self::hashOfArray(iterator_to_array($item), $algorithm, $rawOutput);
        }
        if ($item instanceof Closure) {
            return self::of($item(), $algorithm, $rawOutput);
        }
        return hash($algorithm, serialize($item), $rawOutput);
    }

    private static function hashOfArray(array $item, string $algorithm, bool $rawOutput): string
    {
        $hash = '';
        foreach ($item as $k => $v) {
            $hash .= 'k' . static::of($k, $algorithm, $rawOutput) . 'v' . static::of($v, $algorithm, $rawOutput);
        }
        return hash($algorithm, $hash, $rawOutput);
    }

    private static function hashOfResource($item, string $algorithm, bool $rawOutput): string
    {
        return hash($algorithm, get_resource_type($item) . (int)$item, $rawOutput);
    }

    private static function hashOfScalar($item, string $algorithm, bool $rawOutput): string
    {
        return hash($algorithm, $item, $rawOutput);
    }
}