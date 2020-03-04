<?php

namespace AlephTools\DDD\Common\Model\Events;

use AlephTools\DDD\Common\Model\Identity\AbstractId;

/**
 * @property-read array $properties The entity properties at the creation time.
 */
class EntityCreated extends EntityLifeCycleChangedEvent
{
    private $properties;

    public function __construct(string $entity, AbstractId $id, array $properties)
    {
        parent::__construct($entity, $id);
        $this->properties = $properties;
    }
}
