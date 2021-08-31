<?php

declare(strict_types=1);

namespace AlephTools\DDD\Common\Model;

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
     * @return static
     */
    public static function parse(?string $fullName, string $format = 'fl')
    {
        if ($fullName === null || $fullName === '') {
            return new static();
        }

        $n = 0;
        $parts = explode(' ', static::sanitizeName($fullName));
        $format = strtolower($format ?: 'fl');
        $length = strlen($format);
        $firstName = $lastName = $middleName = '';
        while ($parts && $n < $length) {
            $name = array_shift($parts);
            $field = $format[$n];
            if ($field === 'l') {
                $lastName = $name;
            } elseif ($field === 'f') {
                $firstName = $name;
            } elseif ($field === 'm') {
                $middleName = $name;
            }
            ++$n;
        }
        return new static($firstName, $lastName, $middleName);
    }

    /**
     * Available formats:
     * FullName()
     * FullName(string $firstName, string $lastName)
     * FullName(array $properties)
     *
     * @param array<string,mixed>|string|null $firstName
     */
    public function __construct($firstName = null, string $lastName = null, string $middleName = null)
    {
        if (is_array($firstName)) {
            parent::__construct($firstName);
        } else {
            parent::__construct([
                'firstName' => $firstName,
                'lastName' => $lastName,
                'middleName' => $middleName,
            ]);
        }
    }

    /**
     * Returns the full name in the given format.
     * The available formats: f, l, m, fl, lf, flm, fml, lfm, lmf, mfl, mlf, where
     * "f" - the first name, "l" - the last name and "m" - the middle name.
     *
     */
    public function asFormattedName(string $format = 'fl'): string
    {
        $name = [];
        $format = strtolower($format);
        for ($length = strlen($format), $i = 0; $i < $length; ++$i) {
            $field = $format[$i];
            if ($field === 'l') {
                $name[] = $this->lastName;
            } elseif ($field === 'f') {
                $name[] = $this->firstName;
            } elseif ($field === 'm') {
                $name[] = $this->middleName;
            }
        }
        return trim(implode(' ', $name));
    }

    //region Setters and Validators

    protected function setFirstName(?string $firstName): void
    {
        $this->firstName = static::sanitizeName($firstName);
    }

    protected function setLastName(?string $lastName): void
    {
        $this->lastName = static::sanitizeName($lastName);
    }

    protected function setMiddleName(?string $middleName): void
    {
        $this->middleName = static::sanitizeName($middleName);
    }

    protected function validateFirstName(): void
    {
        $this->assertArgumentMaxLength(
            $this->firstName,
            (int)static::FIRST_NAME_MAX_LENGTH,
            'First name must be at most ' . (string)static::FIRST_NAME_MAX_LENGTH . ' characters.'
        );
    }

    protected function validateLastName(): void
    {
        $this->assertArgumentMaxLength(
            $this->lastName,
            (int)static::LAST_NAME_MAX_LENGTH,
            'Last name must be at most ' . (string)static::LAST_NAME_MAX_LENGTH . ' characters.'
        );
    }

    protected function validateMiddleName(): void
    {
        $this->assertArgumentMaxLength(
            $this->middleName,
            (int)static::MIDDLE_NAME_MAX_LENGTH,
            'Middle name must be at most ' . (string)static::MIDDLE_NAME_MAX_LENGTH . ' characters.'
        );
    }

    //endregion

    protected static function sanitizeName(?string $name): string
    {
        return $name === null ? '' : preg_replace('/[[:cntrl:]]/', '', trim($name));
    }
}
