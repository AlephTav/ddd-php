<?php

namespace AlephTools\DDD\Common\Model\Events;

use AlephTools\DDD\Common\Model\Identity\AbstractId;

/**
 * @property-read AbstractId $id The entity identifier.
 * @property-read string $entity The entity class.
 * @property-read array $properties The entity properties at the creation time.
 */
class EntityCreated extends DomainEvent
{
    private $id;
    private $entity;
    private $properties;

    public function __construct(string $entity, AbstractId $id, array $properties)
    {
        parent::__construct([
            'id' => $id,
            'entity' => $entity,
            'properties' => $properties
        ]);
    }
}
