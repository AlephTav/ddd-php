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

    public function __construct(string $entity, AbstractId $id, array $oldProperties, array $newProperties)
    {
        parent::__construct($entity, $id);
        $this->oldProperties = $oldProperties;
        $this->newProperties = $newProperties;
    }
}