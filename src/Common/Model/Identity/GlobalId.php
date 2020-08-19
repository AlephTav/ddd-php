<?php

namespace AlephTools\DDD\Common\Model\Identity;

/**
 * The global identifier (an identifier which is unique within all applications).
 *
 * @property-read string $identity
 */
class GlobalId extends AbstractId
{
    public const UUID4_PATTERN = '^[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}$';

    /**
     * Generates new global identifier.
     *
     * @return static
     */
    public static function create()
    {
        return new static(self::uuid4());
    }

    /**
     * Generates new uuid4
     *
     * @return string
     */
    private static function uuid4(): string
    {
        $bytes = random_bytes(16);
        $bytes[6] = chr(ord($bytes[6]) & 0x0f | 0x40); // set version to 0100
        $bytes[8] = chr(ord($bytes[8]) & 0x3f | 0x80); // set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4));
    }

    /**
     * Returns TRUE if the given identity can be a global identifier.
     *
     * @param mixed $identity
     * @return bool
     */
    public static function canBeId($identity): bool
    {
        if ($identity instanceof self) {
            return true;
        }

        if (is_string($identity)) {
            return (bool)preg_match('/' . self::UUID4_PATTERN . '/D', $identity);
        }

        return false;
    }

    /**
     * Constructor.
     *
     * @param mixed $identity
     */
    public function __construct($identity)
    {
        parent::__construct(['identity' => $this->parse($identity)]);
    }

    /**
     * Parses the identifier.
     *
     * @param mixed $identity
     * @return null|string
     */
    protected function parse($identity): ?string
    {
        if ($identity === null) {
            return null;
        }
        if ($identity instanceof self) {
            return $identity->identity;
        }
        $this->assertArgumentTrue(is_string($identity), 'Invalid UUID: identity must be a string.');
        $this->assertArgumentTrue(static::canBeId($identity), "Invalid UUID: $identity");
        return $identity;
    }
}