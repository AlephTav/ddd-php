<?php

namespace AlephTools\DDD\Common\Infrastructure;

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
            if ($item instanceof Hashable) {
                return $item->hash();
            }
            return hash($algorithm, spl_object_hash($item), $rawOutput);
        }
        if (is_array($item)) {
            $hash = '';
            foreach ($item as $k => $v) {
                $hash .= self::of($k, $algorithm, $rawOutput) . self::of($v, $algorithm, $rawOutput);
            }
            return hash($algorithm, $hash, $rawOutput);
        }
        return hash($algorithm, $item, $rawOutput);
    }
}