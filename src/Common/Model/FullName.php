<?php

namespace AlephTools\DDD\Common\Model;

use AlephTools\DDD\Common\Infrastructure\Sanitizer;
use AlephTools\DDD\Common\Infrastructure\ValueObject;

/**
 * @property-read string $firstName
 * @property-read string $lastName
 * @property-read string $middleName
 */
class FullName extends ValueObject
{
    public const FIRST_NAME_MAX_LENGTH = 50;
    public const LAST_NAME_MAX_LENGTH = 50;
    public const MIDDLE_NAME_MAX_LENGTH = 50;

    protected string $firstName = '';
    protected string $lastName = '';
    protected string $middleName = '';

    /**
     * Parses the full name from the given format.
     * The available formats: f, l, m, fl, lf, flm, fml, lfm, lmf, mfl, mlf, where
     * "f" - the first name, "l" - the last name and "m" - the middle name.
     *
     * @param string|null $fullName
     * @param string $format
     * @return static
     */
    public static function parse(?string $fullName, string $format = 'fl')
    {
        if ($fullName === null || $fullName === '') {
            return new FullName();
        }

        $n = 0;
        $parts = explode(' ', Sanitizer::sanitizeName($fullName));
        $format = strtolower($format ?: 'fl');
        $length = strlen($format);
        $firstName = $lastName = $middleName = '';
        while ($parts && $n < $length) {
            $name = array_shift($parts);
            $field = $format[$n];
            if ($field === 'l') {
                $lastName = $name;
            } else if ($field === 'f') {
                $firstName = $name;
            } else if ($field === 'm') {
                $middleName = $name;
            }
            ++$n;
        }
        return new FullName($firstName, $lastName, $middleName);
    }

    /**
     * Constructor. Available formats:
     * FullName()
     * FullName(string $firstName, string $lastName)
     * FullName(array $properties)
     *
     * @param array|string|null $firstName
     * @param string|null $lastName
     * @param string|null $middleName
     */
    public function __construct($firstName = null, string $lastName = null, string $middleName = null)
    {
        if (is_array($firstName)) {
            parent::__construct($firstName);
        } else {
            parent::__construct([
                'firstName' => $firstName,
                'lastName' => $lastName,
                'middleName' => $middleName
            ]);
        }
    }

    /**
     * Returns the full name in the given format.
     * The available formats: f, l, m, fl, lf, flm, fml, lfm, lmf, mfl, mlf, where
     * "f" - the first name, "l" - the last name and "m" - the middle name.
     *
     * @param string $format
     * @return string
     */
    public function asFormattedName(string $format = 'fl'): string
    {
        $name = [];
        $format = strtolower($format);
        for ($length = strlen($format), $i = 0; $i < $length; ++$i) {
            $field = $format[$i];
            if ($field === 'l') {
                $name[] = $this->lastName;
            } else if ($field === 'f') {
                $name[] = $this->firstName;
            } else if ($field === 'm') {
                $name[] = $this->middleName;
            }
        }
        return trim(implode(' ', $name));
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

    protected function setMiddleName(?string $middleName): void
    {
        $this->middleName = Sanitizer::sanitizeName($middleName);
    }

    protected function validateFirstName(): void
    {
        $this->assertArgumentMaxLength(
            $this->firstName,
            static::FIRST_NAME_MAX_LENGTH,
            'First name must be at most ' . static::FIRST_NAME_MAX_LENGTH . ' characters.'
        );
    }

    protected function validateLastName(): void
    {
        $this->assertArgumentMaxLength(
            $this->lastName,
            static::LAST_NAME_MAX_LENGTH,
            'Last name must be at most ' . static::LAST_NAME_MAX_LENGTH . ' characters.'
        );
    }

    protected function validateMiddleName(): void
    {
        $this->assertArgumentMaxLength(
            $this->middleName,
            static::MIDDLE_NAME_MAX_LENGTH,
            'Middle name must be at most ' . static::MIDDLE_NAME_MAX_LENGTH . ' characters.'
        );
    }

    //endregion
}
