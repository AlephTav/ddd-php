<?php

namespace AlephTools\DDD\Tests\Common\Infrastructure\SqlBuilder;

use AlephTools\DDD\Common\Infrastructure\SqlBuilder\UpdateQuery;
use PHPUnit\Framework\TestCase;

class UpdateQueryTest extends TestCase
{
    use QueryTestAware;

    //region TABLE

    public function testTableName(): void
    {
        $q = (new UpdateQuery())
            ->table('tb');

        $this->assertSame('UPDATE tb', $q->toSql());
        $this->assertSame([], $q->getParams());
    }

    public function testTableNameWithAlias(): void
    {
        $q = (new UpdateQuery())
            ->table('tb', 't1');

        $this->assertSame('UPDATE tb t1', $q->toSql());
        $this->assertSame([], $q->getParams());
    }

    public function testFromRawExpression(): void
    {
        $q = (new UpdateQuery())
            ->table(UpdateQuery::raw('my_table AS t'));

        $this->assertSame('UPDATE my_table AS t', $q->toSql());
        $this->assertSame([], $q->getParams());
    }

    //endregion
}