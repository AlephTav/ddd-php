<?php

namespace AlephTools\DDD\Tests\Common\Model;

use AlephTools\DDD\Common\Model\Language;
use PHPUnit\Framework\TestCase;

class LanguageTest extends TestCase
{
    public function testLocale(): void
    {
        $language = Language::RU();

        $this->assertSame('ru_RU', $language->getLocale());
    }
}