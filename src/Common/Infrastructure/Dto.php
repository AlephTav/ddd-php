<?php

declare(strict_types=1);

namespace AlephTools\DDD\Common\Infrastructure;

use AlephTools\DDD\Common\Infrastructure\Exceptions\NonExistentPropertyException;
use AlephTools\DDD\Common\Model\Exceptions\InvalidArgumentException;
use ReflectionClass;
use RuntimeException;
use TypeError;

/**
 * The base class for data transfer objects.
 */
abstract class Dto implements Serializable
{
    use AssertionConcern;

    /**
     * Property attributes.
     */
    protected const PROP_READ = 1;
    protected const PROP_WRITE = 2;
    protected const PROP_VALIDATOR = 4;
    protected const PROP_SETTER = 8;
    protected const PROP_GETTER = 16;
    protected const PROP_READ_WRITE = self::PROP_READ | self::PROP_WRITE;
    protected const PROP_READ_SETTER = self::PROP_READ | self::PROP_SETTER;
    protected const PROP_READ_GETTER = self::PROP_READ | self::PROP_GETTER;
    protected const PROP_READ_VALIDATOR = self::PROP_READ | self::PROP_VALIDATOR;
    protected const PROP_READ_SETTER_VALIDATOR = self::PROP_READ_SETTER | self::PROP_VALIDATOR;
    protected const PROP_READ_GETTER_VALIDATOR = self::PROP_READ_GETTER | self::PROP_VALIDATOR;
    protected const PROP_READ_SETTER_GETTER_VALIDATOR = self::PROP_READ_SETTER_VALIDATOR | self::PROP_GETTER;
    protected const PROP_READ_WRITE_VALIDATOR = self::PROP_READ_WRITE | self::PROP_VALIDATOR;
    protected const PROP_READ_WRITE_SETTER = self::PROP_READ_WRITE | self::PROP_SETTER;
    protected const PROP_READ_WRITE_GETTER = self::PROP_READ_WRITE | self::PROP_GETTER;
    protected const PROP_READ_WRITE_GETTER_VALIDATOR = self::PROP_READ_WRITE_GETTER | self::PROP_VALIDATOR;
    protected const PROP_READ_WRITE_SETTER_VALIDATOR = self::PROP_READ_WRITE_SETTER | self::PROP_VALIDATOR;
    protected const PROP_READ_WRITE_SETTER_GETTER_VALIDATOR = self::PROP_READ_WRITE_SETTER_VALIDATOR | self::PROP_GETTER;

    /**
     * Offsets in $properties
     */
    private const PROP_OFFSET_TYPE = 0;
    private const PROP_OFFSET_SETTER = 1;
    private const PROP_OFFSET_GETTER = 2;
    private const PROP_OFFSET_VALIDATOR = 3;

    /**
     * The property definitions.
     *
     * @psalm-var array<class-string<static>,array<string,array{int,string|null,string|null,string|null}>>
     */
    private static array $properties = [];

    /**
     * The class reflector.
     *
     * @var ReflectionClass[]
     */
    private static array $reflectors = [];

    /**
     * The initialized properties.
     *
     * @var string[]|null
     */
    private ?array $initializedProperties = null;

    /**
     * Returns the definitions of DTO's properties in format:
     * [
     *     'property_name' => property_attributes,
     *     ...
     * ]
     *
     * @psalm-return array<string,int>
     */
    protected static function getPropertyDefinitions(): ?array
    {
        return null;
    }

    /**
     * Constructor.
     *
     * @param array<string,mixed> $properties
     * @param bool $strict  Determines whether to throw exception for non-existing properties (TRUE).
     * @param bool $dynamic Determines whether to store information about initialized properties.
     */
    public function __construct(array $properties, bool $strict, bool $dynamic)
    {
        $this->init();
        $this->assignPropertiesAndValidate(
            array_merge($this->getDefaultPropertyValues(), $properties),
            $strict
        );
        if ($dynamic) {
            $this->extractInitializedProperties($properties, $strict);
        }
    }

    /**
     * Sets default values for DTO for complex non-nullable properties
     *
     * @return array<string,mixed>
     */
    protected function getDefaultPropertyValues(): array
    {
        return [];
    }

    /**
     * @param array<string,mixed> $properties
     */
    private function extractInitializedProperties(array $properties, bool $strict): void
    {
        if ($strict) {
            $this->initializedProperties = array_keys($properties);
        } else {
            $allProperties = $this->properties();
            $this->initializedProperties = [];
            foreach ($properties as $property => $_) {
                if (array_key_exists($property, $allProperties)) {
                    $this->initializedProperties[] = $property;
                }
            }
        }
    }

    /**
     * We need restore the reflection object after serialization
     * to eliminate this bug https://bugs.php.net/bug.php?id=30324
     *
     */
    public function __wakeup(): void
    {
        $this->init();
    }

    private function init(): void
    {
        self::extractProperties();
    }

    private static function reflector(): ?ReflectionClass
    {
        return self::$reflectors[static::class] ?? null;
    }

    /**
     * Converts this object to an associative array.
     *
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        if ($this->initializedProperties === null) {
            return $this->toPropertyArray();
        }
        return $this->toInitializedPropertyArray();
    }

    /**
     * @return array<string,mixed>
     */
    private function toPropertyArray(): array
    {
        $result = [];
        foreach ($this->properties() as $property => $info) {
            $result[$property] = $this->extractPropertyValue($property, $info);
        }
        return $result;
    }

    /**
     * @return array<string,mixed>
     */
    private function toInitializedPropertyArray(): array
    {
        $result = [];
        $properties = $this->properties();
        /** @var string[] $initializedProperties */
        $initializedProperties = $this->initializedProperties;
        foreach ($initializedProperties as $property) {
            $result[$property] = $this->extractPropertyValue($property, $properties[$property]);
        }
        return $result;
    }

    /**
     * Converts this object to a nested associative array.
     *
     * @return array<string,mixed>
     */
    public function toNestedArray(): array
    {
        if ($this->initializedProperties === null) {
            return $this->toNestedPropertyArray();
        }
        return $this->toInitializedNestedPropertyArray();
    }

    /**
     * @return array<string,mixed>
     */
    private function toNestedPropertyArray(): array
    {
        $result = [];
        foreach ($this->properties() as $property => $info) {
            $value = $this->extractPropertyValue($property, $info);
            if ($value instanceof self) {
                $result[$property] = $value->toNestedArray();
            } else {
                $result[$property] = $value;
            }
        }
        return $result;
    }

    /**
     * @return array<string,mixed>
     */
    private function toInitializedNestedPropertyArray(): array
    {
        $result = [];
        $properties = $this->properties();
        /** @var string[] $initializedProperties */
        $initializedProperties = $this->initializedProperties;
        foreach ($initializedProperties as $property) {
            $value = $this->extractPropertyValue($property, $properties[$property]);
            if ($value instanceof self) {
                $result[$property] = $value->toNestedArray();
            } else {
                $result[$property] = $value;
            }
        }
        return $result;
    }

    /**
     * @param array{int,string|null,string|null,string|null} $info
     */
    private function extractPropertyValue(string $property, array $info): mixed
    {
        $getter = $info[self::PROP_OFFSET_GETTER];
        if ($getter === null) {
            return $this->propertyValue($property);
        }
        return $this->invokeGetter($getter);
    }

    /**
     * Converts this object to JSON.
     *
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    /**
     * Returns data which should be serialized to JSON.
     *
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Converts this object to a string.
     *
     */
    public function toString(): string
    {
        return print_r($this, true);
    }

    /**
     * Converts this object to a string.
     *
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    public function __get(string $property): mixed
    {
        $this->checkPropertyExistence($property);

        $info = $this->properties()[$property];
        if (!($info[self::PROP_OFFSET_TYPE] & self::PROP_READ)) {
            throw new RuntimeException("Property \"$property\" is not readable.");
        }

        $getter = $info[self::PROP_OFFSET_GETTER];
        if ($getter === null) {
            return $this->propertyValue($property);
        }
        if ($reflector = $this->reflector()) {
            $method = $reflector->getMethod($getter);
            if ($method->isPublic() || $method->isProtected() && $this->isCalledFromSameClass()) {
                return $this->{$getter}();
            }
            throw new RuntimeException("Property \"$property\" does not have accessible getter.");
        }
        return $this->{$getter}();
    }

    public function __set(string $property, mixed $value): void
    {
        $this->checkPropertyExistence($property);

        $info = $this->properties()[$property];
        if (!($info[self::PROP_OFFSET_TYPE] & self::PROP_WRITE)) {
            throw new RuntimeException("Property \"$property\" is not writable.");
        }

        $setter = $info[self::PROP_OFFSET_SETTER];
        if ($setter === null) {
            $this->assignValueToProperty($property, $value);
        } elseif ($reflector = $this->reflector()) {
            $method = $reflector->getMethod($setter);
            if ($method->isPublic() || $method->isProtected() && $this->isCalledFromSameClass()) {
                $this->invokeWithTypeErrorProcessing(function () use ($setter, $value): void {
                    $this->{$setter}($value);
                });
            } else {
                throw new RuntimeException("Property \"$property\" does not have accessible setter.");
            }
        } else {
            $this->{$setter}($value);
        }
    }

    /**
     * Returns true if the caller of this method is called from this class.
     *
     */
    private function isCalledFromSameClass(): bool
    {
        $class = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3)[2]['class'] ?? '';
        return $class === static::class;
    }

    /**
     * Returns TRUE if the given property is not NULL.
     *
     */
    public function __isset(string $property): bool
    {
        return $this->__get($property) !== null;
    }

    /**
     * Sets the given property value to NULL.
     *
     */
    public function __unset(string $property): void
    {
        $this->__set($property, null);
    }

    /**
     * Returns the properties' information.
     *
     * @psalm-return array<string,array{int,string|null,string|null,string|null}>
     */
    private function properties(): array
    {
        return self::$properties[static::class];
    }

    /**
     * Assigns values to properties.
     *
     * @param array<string,mixed> $properties
     * @param bool $strict Determines whether to throw exception for non-existing properties (TRUE).
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
     * @param bool $strict Determines whether to throw exception for non-existing property (TRUE).
     */
    protected function assignProperty(string $property, mixed $value, bool $strict = true): void
    {
        if ($strict) {
            $this->checkPropertyExistence($property);
        } elseif (!isset($this->properties()[$property])) {
            return;
        }

        $setter = $this->properties()[$property][self::PROP_OFFSET_SETTER];
        if ($setter === null) {
            $this->assignValueToProperty($property, $value);
        } else {
            $this->invokeSetter($setter, $value);
        }
    }

    /**
     * Sets properties and validates their values.
     *
     * @param array<string,mixed> $properties
     * @param bool $strict Determines whether to throw exception for non-existing properties (TRUE).
     */
    protected function assignPropertiesAndValidate(array $properties, bool $strict = true): void
    {
        $this->assignProperties($properties, $strict);
        $this->validate();
    }

    /**
     * Validates attribute values.
     *
     */
    protected function validate(): void
    {
        foreach ($this->properties() as $info) {
            if (null !== $validator = $info[self::PROP_OFFSET_VALIDATOR]) {
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
     * @return mixed
     */
    private function propertyValue(string $property)
    {
        if ($reflector = $this->reflector()) {
            $property = $reflector->getProperty($property);
            return $property->getValue($this);
        }
        return $this->{$property};
    }

    /**
     */
    private function assignValueToProperty(string $property, mixed $value): void
    {
        if ($reflector = $this->reflector()) {
            $property = $reflector->getProperty($property);
            $this->invokeWithTypeErrorProcessing(function () use ($property, $value): void {
                $property->setValue($this, $value);
            });
        } else {
            $this->invokeWithTypeErrorProcessing(function () use ($property, $value): void {
                $this->{$property} = $value;
            });
        }
    }

    /**
     * @return mixed
     */
    private function invokeGetter(string $getter)
    {
        if ($reflector = $this->reflector()) {
            $method = $reflector->getMethod($getter);
            return $method->invoke($this);
        }
        return $this->{$getter}();
    }

    private function invokeSetter(string $setter, mixed $value): void
    {
        if ($reflector = $this->reflector()) {
            $method = $reflector->getMethod($setter);
            $this->invokeWithTypeErrorProcessing(function () use ($method, $value): void {
                $method->invoke($this, $value);
            });
        } else {
            $this->invokeWithTypeErrorProcessing(function () use ($setter, $value): void {
                $this->{$setter}($value);
            });
        }
    }

    private function invokeValidator(string $validator): void
    {
        if ($reflector = $this->reflector()) {
            $method = $reflector->getMethod($validator);
            $method->invoke($this);
        } else {
            $this->{$validator}();
        }
    }

    private function invokeWithTypeErrorProcessing(callable $callback): void
    {
        try {
            $callback();
        } catch (TypeError $e) {
            $error = $e->getMessage();
            if (strncmp('Cannot assign', $error, 13) === 0) {
                preg_match('/^Cannot assign ([0-9?a-zA-Z]+).*\$([a-zA-Z_0-9]+)(.+)$/', $error, $matches);
                if ($matches) {
                    $matches[3] = preg_replace('/\?([0-9_a-zA-Z]+)/', '$1 or null', $matches[3]);
                    $error = "Property \"$matches[2]\" must be an instance$matches[3], $matches[1] used.";
                }
            } else {
                preg_match(
                    '/^.*::[a-z_0-9]+([A-Z][a-zA-Z_0-9]+)\(\): Argument #1 \(\$value\)(.+given).*$/',
                    $error,
                    $matches
                );
                if ($matches) {
                    $matches[1] = lcfirst($matches[1]);
                    $matches[2] = preg_replace('/\?([0-9_a-zA-Z]+)/', '$1 or null', $matches[2]);
                    $error = "Property \"$matches[1]\"$matches[2].";
                }
            }
            throw new InvalidArgumentException($error);
        }
    }

    /**
     * Determines properties of a DTO object.
     *
     */
    private static function extractProperties(): void
    {
        if (isset(self::$properties[static::class])) {
            return;
        }

        if (null !== $properties = static::getPropertyDefinitions()) {
            self::extractPropertiesFromUserDefinition($properties);
        } else {
            self::extractPropertiesFromClassDefinition();
        }
    }

    /**
     * @param array<string,int> $definitions
     */
    private static function extractPropertiesFromUserDefinition(array $definitions): void
    {
        $properties = [];
        foreach ($definitions as $propertyName => $definition) {
            if ($definition & self::PROP_SETTER) {
                $setter = self::getSetterName($propertyName);
            } else {
                $setter = null;
            }
            if ($definition & self::PROP_GETTER) {
                $getter = self::getGetterName($propertyName);
            } else {
                $getter = null;
            }
            if ($definition & self::PROP_VALIDATOR) {
                $validator = self::getValidatorName($propertyName);
            } else {
                $validator = null;
            }
            $properties[$propertyName] = [$definition, $setter, $getter, $validator];
        }

        self::$properties[static::class] = $properties;
    }

    private static function extractPropertiesFromClassDefinition(): void
    {
        $properties = [];
        $reflector = new ReflectionClass(static::class);

        if (preg_match_all(
            '/@property(-read|-write|)[^$]+\$([a-zA-Z0-9_]+)/i',
            self::getDocComment($reflector),
            $matches
        )) {
            foreach ($matches[1] as $i => $type) {
                if ($type === '-read') {
                    $definition = self::PROP_READ;
                } elseif ($type === '-write') {
                    $definition = self::PROP_WRITE;
                } else {
                    $definition = self::PROP_READ_WRITE;
                }

                $propertyName = $matches[2][$i];
                if (!self::hasPropertyField($reflector, $propertyName)) {
                    throw new NonExistentPropertyException(
                        "Property \"$propertyName\" is not connected with the appropriate class field."
                    );
                }

                if (self::hasPropertyMethod($reflector, $setter = self::getSetterName($propertyName))) {
                    $definition |= self::PROP_SETTER;
                } else {
                    $setter = null;
                }
                if (self::hasPropertyMethod($reflector, $getter = self::getGetterName($propertyName))) {
                    $definition |= self::PROP_GETTER;
                } else {
                    $getter = null;
                }
                if (self::hasPropertyMethod($reflector, $validator = self::getValidatorName($propertyName))) {
                    $definition |= self::PROP_VALIDATOR;
                } else {
                    $validator = null;
                }

                $properties[$propertyName] = [$definition, $setter, $getter, $validator];
            }
        }

        self::$properties[static::class] = $properties;
        self::$reflectors[static::class] = $reflector;
    }

    private static function hasPropertyField(ReflectionClass $reflector, string $name): bool
    {
        if ($reflector->hasProperty($name)) {
            $property = $reflector->getProperty($name);
            if ($property->isPrivate()) {
                return $property->getDeclaringClass()->getName() === static::class;
            }
            return !$property->isStatic();
        }

        return false;
    }

    private static function hasPropertyMethod(ReflectionClass $reflector, string $name): bool
    {
        if ($reflector->hasMethod($name)) {
            $method = $reflector->getMethod($name);
            if ($method->isPrivate()) {
                return $method->getDeclaringClass()->getName() === static::class;
            }
            return !$method->isStatic();
        }

        return false;
    }

    private static function getDocComment(ReflectionClass $reflector): string
    {
        $comment = '';
        $class = $reflector;
        while ($class) {
            $comment = $class->getDocComment() . $comment;
            $class = $class->getParentClass();
        }
        return $comment;
    }

    private static function getValidatorName(string $attribute): string
    {
        return 'validate' . ucfirst($attribute);
    }

    private static function getGetterName(string $attribute): string
    {
        return 'get' . ucfirst($attribute);
    }

    private static function getSetterName(string $attribute): string
    {
        return 'set' . ucfirst($attribute);
    }
}
