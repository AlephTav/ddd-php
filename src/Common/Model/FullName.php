<?php

namespace AlephTools\DDD\Common\Model;

use AlephTools\DDD\Common\Infrastructure\Sanitizer;
use AlephTools\DDD\Common\Infrastructure\ValueObject;

/**
 * @property-read string $firstName
 * @property-read string $lastName
 */
class FullName extends ValueObject
{
    public const FIRST_NAME_MAX_LENGTH = 50;
    public const LAST_NAME_MAX_LENGTH = 50;

    private $firstName;
    private $lastName;

    /**
     * Parses the full name string in format "firstName lastName".
     *
     * @param string $fullName
     * @return FullName
     */
    public static function parse(?string $fullName)
    {
        if ($fullName === null || $fullName === '') {
            return new FullName();
        }
        $parts = explode(' ', trim($fullName), 2);
        $firstName = array_shift($parts);
        $lastName = array_shift($parts);
        return new FullName($firstName, $lastName);
    }

    /**
     * Constructor. Available formats:
     * FullName()
     * FullName(string $firstName, string $lastName)
     * FullName(array $properties)
     *
     * @param array|string|null $firstName
     * @param string|null $lastName
     */
    public function __construct($firstName = null, string $lastName = null)
    {
        if (is_array($firstName)) {
            parent::__construct($firstName);
        } else {
            parent::__construct([
                'firstName' => $firstName,
                'lastName' => $lastName
            ]);
        }
    }

    /**
     * Returns the full name in format "firstName lastName".
     *
     * @return string
     */
    public function asFormattedName(): string
    {
        return trim(trim($this->firstName) . ' ' . trim($this->lastName));
    }

    //region Setters and Validators

    protected function setFirstName(?string $firstName): void
    {
        $this->firstName = Sanitizer::sanitizeName($firstName);
    }

    protected function setLastName(?string $lastName): void
    {
        $this->lastName = Sanitizer::sanitizeName($lastName);
    }

    protected function validateFirstName(): void
    {
        $this->assertArgumentLength(
            $this->firstName,
            0,
            static::FIRST_NAME_MAX_LENGTH,
            'First name must be at most ' . static::FIRST_NAME_MAX_LENGTH . ' characters.'
        );
    }

    protected function validateLastName(): void
    {
        $this->assertArgumentLength(
            $this->lastName,
            0,
            static::LAST_NAME_MAX_LENGTH,
            'Last name must be at most ' . static::LAST_NAME_MAX_LENGTH . ' characters.'
        );
    }

    //endregion
}