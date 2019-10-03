<?php

namespace AlephTools\DDD\Tests\Common\Infrastructure\SqlBuilder;

use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\AbstractExpression;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\QueryExecutor;
use PHPUnit\Framework\MockObject\MockObject;

trait QueryTestAware
{
    private $queryExecutionResult;

    /**
     * @throws \ReflectionException
     */
    public function setUp(): void
    {
        $property = (new \ReflectionClass(AbstractExpression::class))->getProperty('parameterIndex');
        $property->setAccessible(true);
        $property->setValue(0);
    }

    private function getMockQueryExecutor(string $method = null): MockObject
    {
        $executor = $this->getMockBuilder(QueryExecutor::class)
            ->setMethods(['rows', 'row', 'column', 'scalar', 'insert', 'execute'])
            ->getMock();

        if ($method) {
            $executor->method($method)
                ->willReturnCallback(function (string $sql, array $params, string $sequence = null) use ($method) {
                    if ($method === 'execute') {
                        $this->queryExecutionResult = [$sql, $params];
                        return 1;
                    }
                    if ($method === 'insert') {
                        $this->queryExecutionResult = [$sql, $params, $sequence];
                        return 1;
                    }
                    return [$sql, $params];
                });
        }

        return $executor;
    }
}
