<?php

namespace AlephTools\DDD\Tests\Common\Infrastructure;

use PHPUnit\Framework\TestCase;
use AlephTools\DDD\Common\Infrastructure\Sanitizer;

class SanitizerTest extends TestCase
{
    /**
     * @dataProvider emailDataProvider
     * @param mixed $email
     * @param string $sanitizedEmail
     */
    public function testSanitizeEmail($email, string $sanitizedEmail): void
    {
        $this->assertSame($sanitizedEmail, Sanitizer::sanitizeEmail($email));
    }

    public function emailDataProvider(): array
    {
        return [
            [null, ''],
            ['', ''],
            ['some@email.com', 'some@email.com'],
            ['  a@a.com', 'a@a.com'],
            ['a@a.com    ', 'a@a.com'],
            [' a@a.com ', 'a@a.com']
        ];
    }

    /**
     * @dataProvider zipDataProvider
     * @param mixed $zip
     * @param string $sanitizeZip
     */
    public function testSanitizeZip($zip, string $sanitizeZip): void
    {
        $this->assertSame($sanitizeZip, Sanitizer::sanitizeZip($zip));
    }

    public function zipDataProvider(): array
    {
        return [
            [null, ''],
            ['', ''],
            ['345452', '345452'],
            [' 454-22-34  ', '454-22-34'],
            ["впукп \n we#$#\t333-22  цуакп \n", '333-22']
        ];
    }

    /**
     * @dataProvider nameDataProvider
     * @param mixed $name
     * @param string $sanitizedName
     */
    public function testSanitizeName($name, string $sanitizedName): void
    {
        $this->assertSame($sanitizedName, Sanitizer::sanitizeName($name));
    }

    public function nameDataProvider(): array
    {
        return [
            [null, ''],
            ['', ''],
            ['test', 'test'],
            ['bla bla', 'bla bla'],
            [' test    ', 'test'],
            [" \v первое \tимя \n\r ", "первое имя"],
            ["\t my{} \n\rsuпер \vname! ", 'my{} suпер name!']
        ];
    }

    /**
     * @dataProvider phoneDataProvider
     * @param $phone
     * @param string $sanitizedPhone
     */
    public function testSanitizePhone($phone, string $sanitizedPhone): void
    {
        $this->assertSame($sanitizedPhone, Sanitizer::sanitizePhone($phone));
    }

    public function phoneDataProvider(): array
    {
        return [
            [null, ''],
            ['', ''],
            ['79653458792', '79653458792'],
            ['8 555 454-22-34  ', '85554542234'],
            [' #$#D555 444 &&67--90   ', '75554446790'],
            ["вп345укп \n we#$#\t333-22  цуа11кп \n", '73453332211']
        ];
    }
}