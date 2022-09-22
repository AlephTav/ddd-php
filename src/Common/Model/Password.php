<?php

declare(strict_types=1);

namespace AlephTools\DDD\Common\Model;

use AlephTools\DDD\Common\Infrastructure\Hash;
use AlephTools\DDD\Common\Infrastructure\ValueObject;
use InvalidArgumentException;
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

    protected ?string $hash = null;
    protected ?string $password = null;

    /**
     * @var callable(string, string|int|null):scalar
     */
    protected static mixed $hashFunction = 'password_hash';

    /**
     * @psalm-param callable(string, string|int|null):scalar $func
     */
    final public static function setHashFunction(callable $func): void
    {
        self::$hashFunction = $func;
    }

    /**
     * The cached hash value.
     *
     */
    private ?string $computedHash = null;

    /**
     * Generates the random password.
     *
     */
    public static function random(int $passLength = 32): static
    {
        if ($passLength <= 0) {
            throw new InvalidArgumentException('Password length must be greater than zero.');
        }
        return new static(substr(bin2hex(random_bytes($passLength)), -$passLength));
    }

    /**
     * @param array<string,mixed>|string|null $password
     */
    public function __construct(null|string|array $password)
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

    protected function encodePassword(mixed $password): string
    {
        $hash = is_scalar($password) || $password === null ? $this->hashPassword((string)$password) : '';
        if ($hash === '') {
            throw new RuntimeException('Failed to hash password.');
        }
        return $hash;
    }

    protected function hashPassword(string $password): string
    {
        return (string)(self::$hashFunction)($password, PASSWORD_DEFAULT);
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
