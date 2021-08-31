<?php

declare(strict_types=1);

namespace AlephTools\DDD\Common\Infrastructure;

use JsonSerializable;

interface Serializable extends JsonSerializable
{
    public function toArray(): array;

    public function toJson(): string;

    public function toString(): string;

    public function __toString(): string;
}
