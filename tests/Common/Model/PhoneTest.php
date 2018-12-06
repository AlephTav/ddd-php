<?php

namespace AlephTools\DDD\Tests\Common\Model;

use AlephTools\DDD\Common\Model\Exceptions\InvalidArgumentException;
use AlephTools\DDD\Common\Model\Phone;
use PHPUnit\Framework\TestCase;

class PhoneTest extends TestCase
{
    public function testCreationWithNoArguments(): void
    {
        $phone = new Phone();

        $this->assertSame('', $phone->number);
    }

    public function testCreationWithArguments(): void
    {
        $phone1 = new Phone('15554443322');
        $phone2 = new Phone(['number' => '79655554433']);

        $this->assertSame('15554443322', $phone1->number);
        $this->assertSame('79655554433', $phone2->number);
    }

    public function testNumberTooLong(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Phone number must be at most ' . Phone::NUMBER_MAX_LENGTH . ' characters.');

        new Phone(str_repeat('1', Phone::NUMBER_MAX_LENGTH + 1));
    }

    /**
     * @dataProvider phoneDataProvider
     * @param mixed $phone
     * @param string $sanitizedPhone
     */
    public function testPhoneSanitization($phone, $sanitizedPhone): void
    {
        $phone = new Phone($phone);

        $this->assertSame($sanitizedPhone, $phone->number);
    }

    public function phoneDataProvider(): array
    {
        return [
            [null, ''],
            ['', ''],
            ['15551112233', '15551112233'],
            ['+7(965)345-23-56', '79653452356'],
            [" 965 345\n2356", '79653452356'],
            [" 81\r\n2 3 4 5\n\t6789\v\r0 ", '81234567890']
        ];
    }
}