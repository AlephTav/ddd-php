<?php

declare(strict_types=1);

namespace AlephTools\DDD\Common\Model\Events;

use AlephTools\DDD\Common\Model\Identity\AbstractId;

/**
 * @property-read array $properties The entity properties at the creation time.
 */
class EntityCreated extends EntityLifeCycleChanged
{
    private array $properties = [];

    public function __construct(string $entity, ?AbstractId $id, array $properties)
    {
        parent::__construct($entity, $id);
        $this->properties = $properties;
    }
}
