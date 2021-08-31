<?php

declare(strict_types=1);

namespace AlephTools\DDD\Common\Model;

use AlephTools\DDD\Common\Infrastructure\Hash;
use AlephTools\DDD\Common\Infrastructure\ValueObject;
use RuntimeException;

/**
 * @property-read string $hash
 * @property-read string|null $password
 */
class Password extends ValueObject
{
    public const HASH_MAX_LENGTH = 255;
    public const PASSWORD_MIN_LENGTH = 1;
    public const PASSWORD_MAX_LENGTH = 255;
    public const RANDOM_PASSWORD_LENGTH = 32;

    protected ?string $hash = null;
    protected ?string $password = null;

    /**
     * The cached hash value.
     *
     */
    private ?string $computedHash = null;

    /**
     * Generates the random password.
     *
     * @return static
     */
    public static function random()
    {
        return new static(bin2hex(random_bytes((int)static::RANDOM_PASSWORD_LENGTH >> 1)));
    }

    /**
     * @param array<string,mixed>|string|null $password
     */
    public function __construct($password)
    {
        if (is_array($password)) {
            parent::__construct($password);
        } else {
            parent::__construct([
                'password' => $password,
                'hash' => $this->encodePassword($password),
            ]);
        }
    }

    /**
     * @param mixed $password
     */
    protected function encodePassword($password): string
    {
        $hash = is_scalar($password) || $password === null ? $this->hashPassword((string)$password) : null;
        if (!$hash) {
            throw new RuntimeException('Failed to hash password.');
        }
        return $hash;
    }

    /**
     * @return string|false|null
     */
    protected function hashPassword(string $password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    protected function validateHash(): void
    {
        $this->assertArgumentNotEmpty($this->hash, 'Password hash must not be empty.');
        $this->assertArgumentMaxLength(
            $this->hash,
            (int)static::HASH_MAX_LENGTH,
            'Password hash must be at most ' . (string)static::HASH_MAX_LENGTH . ' characters.'
        );
    }

    protected function validatePassword(): void
    {
        if ($this->password !== null) {
            $this->assertArgumentLength(
                $this->password,
                (int)static::PASSWORD_MIN_LENGTH,
                (int)static::PASSWORD_MAX_LENGTH,
                'Password must be at least ' . (string)static::PASSWORD_MIN_LENGTH .
                ' and at most ' . (string)static::PASSWORD_MAX_LENGTH . ' characters.'
            );
        }
    }

    /**
     * Generates a hash value for this domain object.
     *
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
     */
    public function equals($other): bool
    {
        if ($other instanceof static) {
            if ($this->password !== null && $other->password !== null) {
                return $this->password === $other->password;
            } elseif ($this->password !== null) {
                return password_verify($this->password, (string)$other->hash);
            } elseif ($other->password !== null) {
                return password_verify($other->password, $this->hash);
            }
            return $this->hash === $other->hash;
        }

        return false;
    }
}
