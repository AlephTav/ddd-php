<?php

namespace AlephTools\DDD\Common\Model\Events;

use AlephTools\DDD\Common\Infrastructure\DomainEvent;
use AlephTools\DDD\Common\Model\Identity\AbstractId;

/**
 * @property-read AbstractId $id The entity identifier.
 * @property-read string $entity The entity class.
 * @property-read array $oldProperties The entity previous property values.
 * @property-read array $newProperties The entity new property values.
 */
class EntityUpdated extends DomainEvent
{
    private $id;
    private $entity;
    private $oldProperties;
    private $newProperties;

    public function __construct(string $entity, AbstractId $id, array $oldProperties, array $newProperties)
    {
        parent::__construct([
            'id' => $id,
            'entity' => $entity,
            'oldProperties' => $oldProperties,
            'newProperties' => $newProperties
        ]);
    }
}