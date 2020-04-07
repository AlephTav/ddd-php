<?php

namespace AlephTools\DDD\Tests\Common\Model\Identity;

use AlephTools\DDD\Common\Model\Identity\GlobalId;
use PHPUnit\Framework\TestCase;

class AbstractIdTest extends TestCase
{
    public function testSerialization(): void
    {
        $id = GlobalId::create();
        if ($this->phpVersion() < 704) {
            $serializedId = $id->__serialize();
            $this->assertSame($id->toString(), current($serializedId));
        } else {
            $serializedId = serialize($id);
            $expected = "O:45:\"AlephTools\DDD\Common\Model\Identity\GlobalId\":1:{s:11:\"\0*\0identity\";s:36:\"{$id}\";}";
            $this->assertSame($expected, $serializedId);
        }
    }

    public function testUnserialization(): void
    {
        $id = GlobalId::create();

        if ($this->phpVersion() < 704) {
            $id->__unserialize($id->__serialize());
            $unserializedId = $id;
        } else {
            $unserializedId = unserialize(serialize($id));
        }

        $this->assertTrue($id->equals($unserializedId));
    }

    private function phpVersion(): int
    {
        return PHP_VERSION_ID / 100;
    }
}