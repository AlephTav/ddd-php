<?php

namespace AlephTools\DDD\Common\Model\Identity;

use Exception;
use Ramsey\Uuid\Exception\InvalidUuidStringException;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use AlephTools\DDD\Common\Model\Exceptions\InvalidArgumentException;

/**
 * The global identifier (an identifier which is unique within all applications).
 */
class GlobalId extends AbstractId
{
    /**
     * Generates new global identifier.
     *
     * @return static
     * @throws Exception
     */
    public static function create()
    {
        return new static(Uuid::uuid4());
    }

    /**
     * Returns TRUE if the given identity can be a global identifier.
     *
     * @param mixed $identity
     * @return bool
     */
    public static function canBeId($identity): bool
    {
        if ($identity instanceof Uuid || $identity instanceof self) {
            return true;
        }

        if (is_string($identity)) {
            return preg_match('/' . Uuid::VALID_PATTERN . '/D', $identity);
        }

        return false;
    }

    /**
     * Constructor.
     *
     * @param mixed $identity
     * @throws Exception
     */
    public function __construct($identity)
    {
        parent::__construct(['identity' => $this->parse($identity)]);
    }

    protected function setIdentity(?Uuid $identity): void
    {
        $this->identity = $identity;
    }

    /**
     * Parses the identifier.
     *
     * @param mixed $identity
     * @return null|UuidInterface
     */
    protected function parse($identity)
    {
        if ($identity !== null) {
            if ($identity instanceof GlobalId) {
                return $identity->identity;
            }
            if ($identity instanceof Uuid) {
                return $identity;
            }
            $this->assertArgumentTrue(
                is_string($identity) || is_numeric($identity),
                'Invalid UUID: identity must be a string.'
            );
            try {
                if (strlen($identity) === 16) {
                    return Uuid::fromBytes($identity);
                }
                return Uuid::fromString($identity);
            } catch (\InvalidArgumentException | InvalidUuidStringException $ignore) {
                throw new InvalidArgumentException("Invalid UUID: $identity");
            }
        }
        return $identity;
    }
}
