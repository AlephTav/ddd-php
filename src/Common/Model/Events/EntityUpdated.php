<?php

namespace AlephTools\DDD\Common\Model\Events;

use AlephTools\DDD\Common\Model\Identity\AbstractId;

/**
 * @property-read array $oldProperties The entity previous property values.
 * @property-read array $newProperties The entity new property values.
 */
class EntityUpdated extends EntityLifeCycleChanged
{
    private $oldProperties;
    private $newProperties;

    public function __construct(string $entity, ?AbstractId $id, array $oldProperties, array $newProperties)
    {
        parent::__construct($entity, $id);
        $this->oldProperties = $oldProperties;
        $this->newProperties = $newProperties;
    }

    public function merge(EntityUpdated $event): EntityUpdated
    {
        $this->assertStateTrue(
            $event->entity === $this->entity && $this->id->equals($event->id),
            'You can only merge entity updated events for the same entities with the same identifier.'
        );

        $oldProperties = $this->mergeOldProperties($this->oldProperties, $event->oldProperties);
        $newProperties = $this->mergeNewProperties($oldProperties, $this->newProperties, $event->newProperties);

        return new EntityUpdated(
            $this->entity,
            $this->id,
            $oldProperties,
            $newProperties
        );
    }

    private function mergeOldProperties(array $oldProperties1, array $oldProperties2): array
    {
        $properties = $oldProperties1;
        foreach ($oldProperties2 as $property => $value) {
            if (!key_exists($property, $properties)) {
                $properties[$property] = $value;
            } elseif (is_array($value) && is_array($properties[$property])) {
                $properties[$property] = $this->mergeOldProperties($properties[$property], $value);
            }
        }
        return $properties;
    }

    private function mergeNewProperties(array $oldProperties, array $newProperties1, array $newProperties2): array
    {
        $properties = $newProperties1;

        foreach ($newProperties2 as $property => $value) {
            $oldValue = $oldProperties[$property] ?? null;
            if (key_exists($property, $properties)) {
                if (is_array($value) && is_array($properties[$property])) {
                    $properties[$property] = $this->mergeNewProperties(
                        is_array($oldValue) ? $oldValue : [],
                        $properties[$property],
                        $value
                    );
                } elseif ($oldValue !== $value) {
                    $properties[$property] = $value;
                } else {
                    unset($properties[$property]);
                }
            } elseif ($oldValue !== $value) {
                $properties[$property] = $value;
            }
        }

        return $properties;
    }
}