<?php

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