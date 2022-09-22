<?php

declare(strict_types=1);

namespace AlephTools\DDD\Common\Model\Identity;

use AlephTools\DDD\Common\Model\Exceptions\InvalidArgumentException;

/**
 * The local identifier (an identifier which is unique within a single application).
 *
 * @property-read int $identity
 */
class LocalId extends AbstractId
{
    /**
     * Returns TRUE if the given identity can be a local identifier.
     *
     */
    public static function canBeId(mixed $identity): bool
    {
        if (is_string($identity) || is_numeric($identity)) {
            return (bool)preg_match('/^[0-9]+$/', (string)$identity);
        }

        return false;
    }

    /**
     * Parses the identifier.
     *
     */
    protected function parse(mixed $identity): ?int
    {
        if ($identity instanceof static) {
            /** @var mixed $identity */
            $identity = $identity->identity;
        }
        if ($identity === null) {
            return null;
        }
        if (is_numeric($identity)) {
            return (int)$identity;
        }
        if (is_string($identity)) {
            throw new InvalidArgumentException("Invalid identifier: $identity");
        }
        throw new InvalidArgumentException('Invalid identifier: identity must be an integer.');
    }
}
