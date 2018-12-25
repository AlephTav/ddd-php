<?php

namespace AlephTools\DDD\Tests\Common\Infrastructure;

use PHPUnit\Framework\TestCase;
use AlephTools\DDD\Common\Infrastructure\Hash;
use AlephTools\DDD\Common\Infrastructure\Hashable;

class HashableTestObject implements Hashable
{
    public function equals($other): bool
    {
        return true;
    }

    public function hash(): string
    {
        return 'some hash';
    }
}

class HashTest extends TestCase
{
    public function testHashScalar(): void
    {
        $this->assertEquals(hash('md5', 10, true), Hash::of(10, 'md5', true));
        $this->assertEquals(hash('md5', 'foo', true), Hash::of('foo', 'md5', true));
        $this->assertEquals(hash('sha256', true, true), Hash::of(true, 'sha256', true));
        $this->assertEquals(hash('md5', 'foo', false), Hash::of('foo', 'md5', false));
    }

    public function testHashArray(): void
    {
        $arr = [
            0 => 'foo',
            '10' => 1.34,
            1 => true
        ];

        $hash = md5(md5(0) . md5('foo') . md5('10') . md5(1.34) . md5(1) . md5(true));
        $this->assertEquals($hash, Hash::of($arr, 'md5', false));
    }

    public function testHashObject(): void
    {
        $obj = new \stdClass();

        $hash = sha1(spl_object_hash($obj));
        $this->assertEquals($hash, Hash::of($obj, 'sha1', false));
    }

    public function testHashHashableObject(): void
    {
        $obj = new HashableTestObject();

        $this->assertEquals('some hash', Hash::of($obj));
    }
}
