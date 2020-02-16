<?php

namespace AlephTools\DDD\Common\Infrastructure;

use TypeError;
use ReflectionClass;
use RuntimeException;
use AlephTools\DDD\Common\Model\Exceptions\InvalidArgumentException;
use AlephTools\DDD\Common\Infrastructure\Exceptions\NonExistentPropertyException;

/**
 * The base class for data transfer objects.
 */
abstract class Dto implements Serializable
{
    use AssertionConcern;

    /**
     * Offsets in $properties.
     */
    private const PROP_TYPE = 0;
    private const PROP_VALIDATOR = 1;
    private const PROP_SETTER = 2;
    private const PROP_GETTER = 3;

    /**
     * Property types.
     */
    private const PROP_TYPE_READ = 0;
    private const PROP_TYPE_WRITE = 1;
    private const PROP_TYPE_READ_WRITE = 2;

    /**
     * The property cache.
     *
     * @var array
     */
    private static $properties;

    /**
     * Constructor.
     *
     * @param array $properties
     * @param bool $strict Determines whether to throw exception for non-existing properties (TRUE).
     */
    public function __construct(array $properties = [], bool $strict = true)
    {
        $this->init();
        $this->assignPropertiesAndValidate($properties, $strict);
    }

    /**
     * We need restore the reflection object after serialization
     * to eliminate this bug https://bugs.php.net/bug.php?id=30324
     *
     * @return void
     */
    public function __wakeup()
    {
        $this->init();
    }

    private function init(): void
    {
        $this->extractProperties();
    }

    private function reflector()
    {
        return new ReflectionClass($this);
    }

    /**
     * Converts this object to an associative array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $result = [];
        foreach ($this->properties() as $property => $info) {
            $result[$property] = $this->extractPropertyValue($property, $info);
        }
        return $result;
    }

    /**
     * Converts this object to a nested associative array.
     *
     * @return array
     */
    public function toNestedArray(): array
    {
        $result = [];
        foreach ($this->properties() as $attribute => $info) {
            $value = $this->extractPropertyValue($attribute, $info);
            if ($value instanceof self) {
                $result[$attribute] = $value->toNestedArray();
            } else {
                $result[$attribute] = $value;
            }
        }
        return $result;
    }

    private function extractPropertyValue(string $property, array $info)
    {
        $getter = $info[self::PROP_GETTER];
        if ($getter === null) {
            return $this->propertyValue($property);
        }
        return $this->invokeGetter($getter);
    }

    /**
     * Converts this object to JSON.
     *
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    /**
     * Returns data which should be serialized to JSON.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Converts this object to a string.
     *
     * @return string
     */
    public function toString(): string
    {
        return print_r($this, true);
    }

    /**
     * Converts this object to a string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * Returns the property value.
     *
     * @param string $property
     * @return mixed
     */
    public function __get(string $property)
    {
        $this->checkPropertyExistence($property);

        $info = $this->properties()[$property];
        if ($info[self::PROP_TYPE] === self::PROP_TYPE_WRITE) {
            throw new RuntimeException("Property \"$property\" is write only.");
        }

        $getter = $info[self::PROP_GETTER];
        if ($getter === null) {
            return $this->propertyValue($property);
        }
        $method = $this->reflector()->getMethod($getter);
        if ($method->isPublic() || $method->isProtected() && $this->isCalledFromSameClass()) {
            return $this->{$getter}();
        }
        throw new RuntimeException("Property \"$property\" does not have accessible getter.");
    }

    /**
     * Sets the property value.
     *
     * @param string $property
     * @param mixed $value
     * @return void
     */
    public function __set(string $property, $value): void
    {
        $this->checkPropertyExistence($property);

        $info = $this->properties()[$property];
        if ($info[self::PROP_TYPE] === self::PROP_TYPE_READ) {
            throw new RuntimeException("Property \"$property\" is read only.");
        }

        $setter = $info[self::PROP_SETTER];
        if ($setter === null) {
            $this->assignValueToProperty($property, $value);
        } else {
            $method = $this->reflector()->getMethod($setter);
            if ($method->isPublic() || $method->isProtected() && $this->isCalledFromSameClass()) {
                $this->invokeWithTypeErrorProcessing(function () use ($setter, $value) {
                    $this->{$setter}($value);
                });
            } else {
                throw new RuntimeException("Property \"$property\" does not have accessible setter.");
            }
        }
    }

    /**
     * Returns TRUE if the given property is not NULL.
     *
     * @param string $property
     * @return bool
     */
    public function __isset(string $property): bool
    {
        return $this->__get($property) !== null;
    }

    /**
     * Sets the given property value to NULL.
     *
     * @param string $property
     * @return void
     */
    public function __unset(string $property): void
    {
        $this->__set($property, null);
    }

    /**
     * Returns the properties information.
     *
     * @return array
     */
    private function properties(): array
    {
        return self::$properties[static::class];
    }

    /**
     * Assigns values to properties.
     *
     * @param array $properties
     * @param bool $strict Determines whether to throw exception for non-existing properties (TRUE).
     * @return void
     */
    protected function assignProperties(array $properties, bool $strict = true): void
    {
        foreach ($properties as $property => $value) {
            $this->assignProperty($property, $value, $strict);
        }
    }

    /**
     * Assigns value to a property.
     *
     * @param string $property
     * @param mixed $value
     * @param bool $strict Determines whether to throw exception for non-existing property (TRUE).
     * @return void
     */
    protected function assignProperty(string $property, $value, bool $strict = true): void
    {
        if ($strict) {
            $this->checkPropertyExistence($property);
        } else {
            if (!isset($this->properties()[$property])) {
                return;
            }
        }

        $setter = $this->properties()[$property][self::PROP_SETTER];
        if ($setter === null) {
            $this->assignValueToProperty($property, $value);
        } else {
            $this->invokeSetter($setter, $value);
        }
    }

    /**
     * Sets properties and validates their values.
     *
     * @param array $properties
     * @param bool $strict Determines whether to throw exception for non-existing properties (TRUE).
     * @return void
     */
    protected function assignPropertiesAndValidate(array $properties, bool $strict = true): void
    {
        $this->assignProperties($properties, $strict);
        $this->validate();
    }

    /**
     * Validates attribute values.
     *
     * @return void
     */
    protected function validate(): void
    {
        foreach ($this->properties() as $attribute => $info) {
            if (null !== $validator = $info[self::PROP_VALIDATOR]) {
                $this->invokeValidator($validator);
            }
        }
    }

    private function checkPropertyExistence(string $property): void
    {
        if (!isset($this->properties()[$property])) {
            throw new NonExistentPropertyException("Property \"$property\" does not exist.");
        }
    }

    /**
     * Returns true if the caller of this method is called from this class.
     *
     * @return bool
     */
    private function isCalledFromSameClass(): bool
    {
        $class = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3)[2]['class'] ?? '';
        return $class === static::class;
    }

    private function propertyValue(string $property)
    {
        $property = $this->reflector()->getProperty($property);
        $property->setAccessible(true);
        return $property->getValue($this);
    }

    private function assignValueToProperty(string $property, $value)
    {
        $property = $this->reflector()->getProperty($property);
        $property->setAccessible(true);
        $property->setValue($this, $value);
    }

    private function invokeGetter(string $getter)
    {
        $method = $this->reflector()->getMethod($getter);
        $method->setAccessible(true);
        return $method->invoke($this);
    }

    private function invokeSetter(string $setter, $value): void
    {
        $method = $this->reflector()->getMethod($setter);
        $method->setAccessible(true);
        $this->invokeWithTypeErrorProcessing(function () use ($method, $value) {
            $method->invoke($this, $value);
        });
    }

    private function invokeValidator(string $validator): void
    {
        $method = $this->reflector()->getMethod($validator);
        $method->setAccessible(true);
        $method->invoke($this);
    }

    private function invokeWithTypeErrorProcessing(callable $callback)
    {
        try {
            $callback();
        } catch (TypeError $e) {
            $error = $e->getMessage();
            preg_match('/^.*::[a-z_0-9]+([A-Z][a-zA-Z_0-9]+)\(\)(.+given).*$/', $error, $matches);
            if ($matches) {
                $error = 'Property "' . lcfirst($matches[1]) . '"' . $matches[2] . '.';
            }
            throw new InvalidArgumentException($error);
        }
    }

    /**
     * Determines properties of a DTO object.
     *
     * @return void
     */
    private function extractProperties(): void
    {
        if (isset(self::$properties[static::class])) {
            return;
        }

        $properties = [];

        if (preg_match_all(
            '/@property(-read|-write|)[^$]+\$([a-zA-Z0-9_]+)/i',
            $this->getDocComment(),
            $matches
        )) {
            foreach ($matches[1] as $i => $type) {
                if ($type === '-read') {
                    $type = self::PROP_TYPE_READ;
                } else {
                    if ($type === '-write') {
                        $type = self::PROP_TYPE_WRITE;
                    } else {
                        $type = self::PROP_TYPE_READ_WRITE;
                    }
                }

                $propertyName = $matches[2][$i];
                if (!$this->hasPropertyField($propertyName)) {
                    throw new NonExistentPropertyException(
                        "Property \"$propertyName\" is not connected with the appropriate class field."
                    );
                }

                if (!$this->hasPropertyMethod($setter = $this->getSetterName($propertyName))) {
                    $setter = null;
                }
                if (!$this->hasPropertyMethod($getter = $this->getGetterName($propertyName))) {
                    $getter = null;
                }
                if (!$this->hasPropertyMethod($validator = $this->getValidatorName($propertyName))) {
                    $validator = null;
                }

                $properties[$propertyName] = [$type, $validator, $setter, $getter];
            }
        }

        self::$properties[static::class] = $properties;
    }

    private function hasPropertyField(string $name): bool
    {
        if ($this->reflector()->hasProperty($name)) {
            $property = $this->reflector()->getProperty($name);
            if ($property->isPrivate()) {
                return $property->getDeclaringClass()->getName() === static::class;
            }
            return !$property->isStatic();
        }

        return false;
    }

    private function hasPropertyMethod(string $name): bool
    {
        if ($this->reflector()->hasMethod($name)) {
            $method = $this->reflector()->getMethod($name);
            if ($method->isPrivate()) {
                return $method->getDeclaringClass()->getName() === static::class;
            }
            return !$method->isStatic();
        }

        return false;
    }

    private function getDocComment(): string
    {
        $comment = '';
        $class = $this->reflector();
        while ($class) {
            $comment = $class->getDocComment() . $comment;
            $class = $class->getParentClass();
        }
        return $comment;
    }

    private function getValidatorName(string $attribute): string
    {
        return 'validate' . ucfirst($attribute);
    }

    private function getGetterName(string $attribute): string
    {
        return 'get' . ucfirst($attribute);
    }

    private function getSetterName(string $attribute): string
    {
        return 'set' . ucfirst($attribute);
    }
}
