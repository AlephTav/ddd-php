<?php

namespace AlephTools\DDD\Common\Model\Identity;

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
            return preg_match('/^[0-9]+$/', $identity);
        }

        return false;
    }

    public function __construct($identity)
    {
        parent::__construct(['identity' => $this->parse($identity)]);
    }

    protected function setIdentity(?int $identity): void
    {
        $this->identity = $identity;
    }

    /**
     * Parses the identifier.
     *
     * @param $identity
     * @return int|null
     */
    protected function parse($identity)
    {
        if ($identity !== null) {
            if ($identity instanceof LocalId) {
                return $identity->identity;
            }
            $this->assertArgumentTrue(
                is_string($identity) || is_numeric($identity),
                'Invalid identifier: identity must be an integer.'
            );
            $this->assertArgumentTrue(is_numeric($identity), "Invalid identifier: $identity");
        }
        return $identity;
    }
}
