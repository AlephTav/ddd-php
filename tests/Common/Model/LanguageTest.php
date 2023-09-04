<?php

declare(strict_types=1);

namespace Tests\AlephTools\DDD\Common\Model;

use AlephTools\DDD\Common\Model\Language;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class LanguageTest extends TestCase
{
    public function testLocale(): void
    {
        $language = Language::RU();

        self::assertSame('ru_RU', $language->getLocale());
    }

    public function testName(): void
    {
        $language = Language::RU();
        self::assertSame('русский', $language->getName());
    }
}
