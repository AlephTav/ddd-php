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
    }

    public function hashData(): array
    {
        $resourceHash = function($resource) {
            $hash = new \stdClass();
            $hash->resource = get_resource_type($resource);
            $hash->value = (int)$resource;
            return serialize($hash);
        };
        return [
            // Scalars
            [
                10,
                'md5',
                true,
                hash('md5', serialize(10), true)
            ],
            [
                5.76,
                'sha256',
                false,
                hash('sha256', serialize(5.76), false)
            ],
            [
                true,
                'sha1',
                true,
                hash('sha1', serialize(true), true)
            ],
            [
                'foo',
                'md5',
                false,
                hash('md5', serialize('foo'), false)
            ],
            // Arrays
            [
                $arr = [0 => 'foo', '10' => 1.34, 1 => true],
                'md5',
                false,
                md5(serialize($arr))
            ],
            [
                $arr = [new \stdClass(), [true], $resource = STDIN],
                'md5',
                true,
                hash(
                    'md5',
                    serialize([
                        hash('md5', serialize(new \stdClass()), true),
                        hash('md5', serialize([true]), true),
                        hash('md5', $resourceHash(STDIN), true)
                    ]),
                    true
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
                $enum = Gender::FEMALE(),
                'crc32',
                false,
                hash('crc32', serialize($enum), false)
            ],
            [
                $date = new \DateTime(),
                'sha512',
                true,
                hash('sha512', serialize($date), true)
            ],
            [
                $dto = new HashableDtoTestObject([
                    'prop1' => 1,
                    'prop2' => 'abc'
                ]),
                'md5',
                false,
                md5(serialize($dto))
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
                md5(serialize([3, 2, 1]))
            ],
            [
                $closure = function() {
                    return 'test';
                },
                'md5',
                false,
                md5(serialize('test'))
            ],
            // Resources
            [
                STDIN,
                'md5',
                false,
                md5($resourceHash(STDIN))
            ]
        ];
    }
}
