<?php

namespace AlephTools\DDD\Common\Model;

use AlephTools\DDD\Common\Infrastructure\Hash;
use AlephTools\DDD\Common\Infrastructure\ValueObject;

/**
 * @property-read string $hash
 * @property-read string $password
 */
class Password extends ValueObject
{
    //region Constants

    public const HASH_MAX_LENGTH = 255;
    public const PASSWORD_MIN_LENGTH = 1;
    public const PASSWORD_MAX_LENGTH = 255;
    public const RANDOM_PASSWORD_LENGTH = 32;

    //endregion

    protected ?string $hash = null;
    protected ?string $password = null;

    /**
     * The cached hash value.
     *
     * @var string
     */
    private $computedHash;

    /**
     * Generates the random password.
     *
     * @return static
     */
    public static function random()
    {
        return new static(bin2hex(random_bytes(static::RANDOM_PASSWORD_LENGTH >> 1)));
    }

    public function __construct($password)
    {
        if (is_array($password)) {
            parent::__construct($password);
        } else {
            parent::__construct([
                'password' => $password,
                'hash' => $this->encodePassword($password)
            ]);
        }
    }

    protected function encodePassword(?string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    protected function validateHash(): void
    {
        $this->assertArgumentNotEmpty($this->hash, 'Password hash must not be empty.');
        $this->assertArgumentMaxLength(
            $this->hash,
            static::HASH_MAX_LENGTH,
            'Password hash must be at most ' . static::HASH_MAX_LENGTH . ' characters.'
        );
    }

    protected function validatePassword(): void
    {
        if ($this->password !== null) {
            $this->assertArgumentLength(
                $this->password,
                static::PASSWORD_MIN_LENGTH,
                static::PASSWORD_MAX_LENGTH,
                'Password must be at least ' . static::PASSWORD_MIN_LENGTH .
                ' and at most ' . static::PASSWORD_MAX_LENGTH . ' characters.'
            );
        }
    }

    /**
     * Generates a hash value for this domain object.
     *
     * @return string
     */
    public function hash(): string
    {
        if ($this->computedHash !== null) {
            return $this->computedHash;
        }
        return $this->computedHash = Hash::of($this->password === null ? $this->hash : $this->password);
    }

    /**
     * Compares two domain objects.
     *
     * @param mixed $other
     * @return bool
     */
    public function equals($other): bool
    {
        if ($other instanceof static) {
            if ($this->password !== null && $other->password !== null) {
                return $this->password === $other->password;
            } else if ($this->password !== null) {
                return password_verify($this->password, $other->hash);
            } else if ($other->password !== null) {
                return password_verify($other->password, $this->hash);
            }
            return $this->hash === $other->hash;
        }

        return false;
    }
}
