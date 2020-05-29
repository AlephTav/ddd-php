<?php

namespace AlephTools\DDD\Common\Model\Identity;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Exception\InvalidUuidStringException;
use AlephTools\DDD\Common\Model\Exceptions\InvalidArgumentException;

/**
 * The global identifier (an identifier which is unique within all applications).
 *
 * @property-read Uuid $identity
 */
class GlobalId extends AbstractId
{
    /**
     * Generates new global identifier.
     *
     * @return static
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
     */
    public function __construct($identity)
    {
        parent::__construct(['identity' => $this->parse($identity)]);
    }

    /**
     * Compact serialization of GlobalId
     *
     * @return array
     */
    public function __serialize(): array
    {
        return ["\0*\0identity" => $this->identity->getFields()->getBytes()];
    }

    /**
     * Unserialization of GlobalId
     *
     * @param array $data
     */
    public function __unserialize(array $data): void
    {
        $this->__wakeup();
        $this->identity = $this->parse($data["\0*\0identity"]);
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
