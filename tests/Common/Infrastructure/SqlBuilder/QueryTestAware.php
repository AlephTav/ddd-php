<?php

namespace AlephTools\DDD\Tests\Common\Infrastructure\SqlBuilder;

use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\AbstractExpression;

trait QueryTestAware
{
    /**
     * @throws \ReflectionException
     */
    public function setUp()
    {
        $property = (new \ReflectionClass(AbstractExpression::class))->getProperty('parameterIndex');
        $property->setAccessible(true);
        $property->setValue(0);
    }
}