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
     * @param mixed $identity
     */
    public static function canBeId($identity): bool
    {
        if (is_string($identity) || is_numeric($identity)) {
            return (bool)preg_match('/^[0-9]+$/', (string)$identity);
        }

        return false;
    }

    /**
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
     */
    protected function parse($identity): ?int
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
