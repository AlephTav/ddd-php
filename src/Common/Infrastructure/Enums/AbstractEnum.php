<?php

namespace AlephTools\DDD\Common\Infrastructure\Enums;

use JsonSerializable;
use UnexpectedValueException;
use BadMethodCallException;
use ReflectionException;
use ReflectionClass;

/**
 * Base class of all enum types.
 */
abstract class AbstractEnum implements JsonSerializable
{
    /**
     * The constants' cache.
     *
     * @var array
     */
    private static array $constants = [];

    /**
     * The enum instances.
     *
     * @var array
     */
    private static array $instances = [];

    /**
     * The name of an enum constant associated with the given enum instance.
     *
     * @var string
     */
    protected string $constant = '';

    /**
     * Returns the class's constants.
     *
     * @return array
     * @throws ReflectionException
     */
    final public static function getConstants(): array
    {
        $class = static::class;
        if (isset(self::$constants[$class])) {
            return self::$constants[$class];
        }
        return self::$constants[$class] = (new ReflectionClass($class))->getConstants();
    }

    /**
     * Returns the available constant names.
     *
     * @return array
     * @throws ReflectionException
     */
    final public static function getConstantNames(): array
    {
        return array_keys(self::getConstants());
    }

    /**
     * Clears the enum cache.
     */
    final public static function clear(): void
    {
        self::$instances = [];
    }

    /**
     * Checks if the given constant name is in the enum type.
     *
     * @param string $name
     * @return bool
     * @throws ReflectionException
     */
    public static function isValidConstantName(string $name): bool
    {
        return array_key_exists($name, self::getConstants());
    }

    /**
     * Checks if the given value is in the enum type.
     *
     * @param mixed $value
     * @param bool $strict Determines whether to search for identical elements.
     * @return bool
     * @throws ReflectionException
     */
    public static function isValidConstantValue($value, $strict = false): bool
    {
        return in_array($value, self::getConstants(), $strict);
    }

    /**
     * Validates the constant name.
     *
     * @param string $name The constant name.
     * @return void
     * @throws UnexpectedValueException
     * @throws ReflectionException
     */
    public static function validate($name): void
    {
        if (!static::isValidConstantName($name)) {
            $enum = (new ReflectionClass(static::class))->getShortName();
            throw new UnexpectedValueException(
                "Constant \"$enum::$name\" does not exist. Valid values are " .
                implode(', ', static::getConstantNames()) . '.'
            );
        }
    }

    /**
     * Returns value by constant name.
     *
     * @param string $name The constant name.
     * @return mixed
     * @throws UnexpectedValueException
     * @throws ReflectionException
     */
    public static function getConstantValue(string $name)
    {
        static::validate($name);
        return self::getConstants()[$name];
    }

    /**
     * Creates an enum instance that associated with the given enum constant name.
     *
     * @param string $name The constant name.
     * @param array $arguments
     * @return static
     * @throws UnexpectedValueException
     * @throws BadMethodCallException
     * @throws ReflectionException
     */
    final public static function __callStatic(string $name, array $arguments = [])
    {
        $value = static::getConstantValue($name);
        $value = is_array($value) ? $value : [$value];
        $instance = self::getInstance($name, $value);
        $instance->constant = $name;
        if ($arguments) {
            $method = 'get' . ucfirst(array_shift($arguments));
            if (method_exists($instance, $method)) {
                return $instance->{$method}(...$arguments);
            }
            throw new BadMethodCallException("Method $method does not exist.");
        }
        return $instance;
    }

    /**
     * Compares two enum instance.
     * Returns TRUE if two objects have the same enum value (even though they refer different object instances).
     *
     * @param mixed $enum
     * @return bool
     */
    public function is($enum): bool
    {
        if ($enum instanceof static) {
            return $this->constant === $enum->getConstantName();
        }

        return $enum === $this->constant;
    }

    /**
     * Returns the name of the constant that associated with the current enum instance.
     *
     * @return string
     */
    public function getConstantName(): string
    {
        return $this->constant;
    }

    /**
     * Converts the enum instance to a string.
     *
     * @return string
     */
    public function toString(): string
    {
        return $this->__toString();
    }

    /**
     * Converts the enum instance to a string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getConstantName();
    }

    /**
     * Returns data which should be serialized to JSON.
     *
     * @return string
     */
    public function jsonSerialize(): string
    {
        return $this->getConstantName();
    }

    /**
     * Creates the enum instance.
     *
     * @param string $constant
     * @param array $value
     * @return static
     */
    private static function getInstance(string $constant, array $value)
    {
        $class = static::class;
        if (isset(self::$instances[$class][$constant])) {
            return self::$instances[$class][$constant];
        }
        return self::$instances[$class][$constant] = new $class(...$value);
    }

    /**
     * Restore the cache reference after deserialization.
     */
    public function __wakeup()
    {
        if (empty(self::$instances[static::class][$this->constant])) {
            self::$instances[static::class][$this->constant] = $this;
        }
    }

    /**
     * Forbids the implicit creation of enum instances without own constructors.
     */
    private function __construct() {}
}
