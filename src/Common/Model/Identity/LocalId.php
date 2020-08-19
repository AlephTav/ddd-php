<?php

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
     * @param mixed $identity
     * @return bool
     */
    public static function canBeId($identity): bool
    {
        if (is_string($identity) || is_numeric($identity)) {
            return (bool)preg_match('/^[0-9]+$/', (string)$identity);
        }

        return false;
    }

    public function __construct($identity)
    {
        parent::__construct(['identity' => $this->parse($identity)]);
    }

    /**
     * Parses the identifier.
     *
     * @param mixed $identity
     * @return int|null
     */
    protected function parse($identity): ?int
    {
        if ($identity === null) {
            return null;
        }
        if ($identity instanceof self) {
            return $identity->identity;
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
