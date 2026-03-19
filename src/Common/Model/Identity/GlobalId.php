<?php

declare(strict_types=1);

namespace AlephTools\DDD\Common\Model\Identity;

/**
 * The global identifier (an identifier which is unique within all applications).
 *
 * @property-read string $identity
 */
class GlobalId extends AbstractId
{
    public const UUID_PATTERN = '^[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}$';
    public const UUID7_PATTERN = '^[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-7[0-9A-Fa-f]{3}-[89ABab][0-9A-Fa-f]{3}-[0-9A-Fa-f]{12}$';

    /**
     * Generates new global identifier.
     *
     */
    public static function create(): static
    {
        return new static(self::uuid4());
    }

    /**
     * Generates new global identifier using UUID v7 (time-ordered).
     *
     */
    public static function create7(): static
    {
        return new static(self::uuid7());
    }

    /**
     * Generates new uuid4
     *
     */
    private static function uuid4(): string
    {
        $bytes = random_bytes(16);
        $bytes[6] = chr(ord($bytes[6]) & 0x0f | 0x40); // set version to 0100
        $bytes[8] = chr(ord($bytes[8]) & 0x3f | 0x80); // set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4));
    }

    /**
     * Generates new uuid7 (time-ordered UUID)
     * Implementation optimized for high performance
     *
     */
    private static function uuid7(): string
    {
        // Get current Unix timestamp in milliseconds (48 bits)
        $timestampMs = (int)(microtime(true) * 1000);

        // Convert to 6-byte big-endian representation
        $timestampBytes = pack('J', $timestampMs);
        $timestampBytes = substr($timestampBytes, -6);

        // Generate 10 random bytes (80 bits) for the random portion
        $randomBytes = random_bytes(10);

        // Combine timestamp (6 bytes) + random (10 bytes) = 16 bytes total
        $uuidBytes = $timestampBytes . $randomBytes;

        // Set version to 0111 (UUID v7) in the most significant 4 bits of byte 6
        $uuidBytes[6] = chr(ord($uuidBytes[6]) & 0x0f | 0x70);

        // Set variant to 10 in the most significant 2 bits of byte 8
        $uuidBytes[8] = chr(ord($uuidBytes[8]) & 0x3f | 0x80);

        // Convert to hexadecimal string with UUID formatting
        $hex = bin2hex($uuidBytes);

        return sprintf(
            '%s-%s-%s-%s-%s',
            substr($hex, 0, 8),
            substr($hex, 8, 4),
            substr($hex, 12, 4),
            substr($hex, 16, 4),
            substr($hex, 20, 12)
        );
    }

    /**
     * Returns TRUE if the given identity can be a global identifier.
     *
     */
    public static function canBeId(mixed $identity): bool
    {
        if ($identity instanceof self) {
            return true;
        }

        if (is_string($identity)) {
            return (bool)preg_match('/' . self::UUID_PATTERN . '/D', $identity);
        }

        return false;
    }

    /**
     * Returns TRUE if the given identity can be a global identifier of version 7.
     *
     */
    public static function canBeId7(mixed $identity): bool
    {
        if ($identity instanceof self) {
            return true;
        }

        if (is_string($identity)) {
            return (bool)preg_match('/' . self::UUID7_PATTERN . '/D', $identity);
        }

        return false;
    }

    /**
     * Parses the identifier.
     *
     */
    protected function parse(mixed $identity): ?string
    {
        if ($identity instanceof static) {
            /** @var mixed $identity */
            $identity = $identity->identity;
        }
        if ($identity === null) {
            return null;
        }
        $this->assertArgumentTrue(is_string($identity), 'Invalid UUID: identity must be a string.');
        $this->assertArgumentTrue(static::canBeId($identity), "Invalid UUID: $identity");
        return $identity;
    }
}
