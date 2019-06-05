<?php

namespace AlephTools\DDD\Common\Model\Events;

use AlephTools\DDD\Common\Model\Identity\AbstractId;

/**
 * @property-read AbstractId $id The entity identifier.
 * @property-read string $entity The entity class.
 */
class EntityDeleted extends DomainEvent
{
    private $id;
    private $entity;

    public function __construct(string $entity, AbstractId $id)
    {
        parent::__construct([
            'id' => $id,
            'entity' => $entity
        ]);
    }
}
