<?php

namespace AlephTools\DDD\Tests\Common\Infrastructure;

use AlephTools\DDD\Common\Infrastructure\Dto;
use AlephTools\DDD\Common\Model\Gender;
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

/**
 * @property mixed $prop1
 * @property mixed $prop2
 */
class HashableDtoTestObject extends Dto
{
    private $prop1;
    private $prop2;

    public function equals($other): bool
    {
        return true;
    }
}

class HashTest extends TestCase
{
    /**
     * @dataProvider hashData
     * @param $value
     * @param string $algorithm
     * @param bool $rawOutput
     * @param string $expectedHash
     * @return void
     */
    public function testHash($value, string $algorithm, bool $rawOutput, string $expectedHash): void
    {
        $this->assertSame(Hash::of($value, $algorithm, $rawOutput), $expectedHash);

        $this->assertEquals(hash('md5', 'foo', true), Hash::of('foo', 'md5', true));
        $this->assertEquals(hash('md5', 'foo', false), Hash::of('foo', 'md5', false));
    }

    public function hashData(): array
    {
        return [
            // Scalars
            [
                10,
                'md5',
                true,
                hash('md5', 10, true)
            ],
            [
                5.76,
                'sha256',
                false,
                hash('sha256', 5.76, false)
            ],
            [
                true,
                'sha1',
                true,
                hash('sha1', true, true)
            ],
            [
                'foo',
                'md5',
                false,
                hash('md5', 'foo', false)
            ],
            // Arrays
            [
                [0 => 'foo', '10' => 1.34, 1 => true],
                'md5',
                false,
                md5(
                    'k' . md5(0) . 'v' . md5('foo') .
                    'k' . md5('10') . 'v' . md5(1.34) .
                    'k' . md5(1) . 'v' . md5(true)
                )
            ],
            // Objects
            [
                new \stdClass(),
                'sha1',
                false,
                sha1(serialize(new \stdClass()))
            ],
            [
                new HashableTestObject(),
                'md5',
                true,
                'some hash'
            ],
            [
                Gender::FEMALE(),
                'crc32',
                false,
                hash('crc32', Gender::FEMALE(), false)
            ],
            [
                $date = new \DateTime(),
                'sha512',
                true,
                hash('sha512', $date->format('U.u'), true)
            ],
            [
                $dto = new HashableDtoTestObject([
                    'prop1' => 1,
                    'prop2' => 'abc'
                ]),
                'md5',
                false,
                md5(
                    'k' . md5(0) . 'v' . md5(get_class($dto)) .
                    'k' . md5(1) . 'v' . md5(
                        'k' . md5('prop1') . 'v' . md5(1) .
                        'k' . md5('prop2') . 'v' . md5('abc')
                    )
                )
            ],
            [
                $iterator = function() {
                    $n = 3;
                    while ($n > 0) {
                        yield $n;
                        --$n;
                    }
                },
                'md5',
                false,
                md5(
                    'k' . md5(0) . 'v' . md5(3) .
                    'k' . md5(1) . 'v' . md5(2) .
                    'k' . md5(2) . 'v' . md5(1)
                )
            ],
            [
                $closure = function() {
                    return 'test';
                },
                'md5',
                false,
                md5('test')
            ],
            // Resources
            [
                $resource = STDIN,
                'md5',
                false,
                md5(get_resource_type($resource) . (int)$resource)
            ]
        ];
    }
}
