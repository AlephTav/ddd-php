<?php

declare(strict_types=1);

namespace AlephTools\DDD\Common\Infrastructure;

interface Identifiable
{
    /**
     * Converts this object to an identity.
     *
     * @return mixed
     */
    public function toIdentity();
}
