<?php

declare(strict_types=1);

namespace WeDevelop\IconManager\Tests\Unit\Lang;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

class TranslationParityTest extends TestCase
{
    private const string LANG_DIR = __DIR__ . '/../../../lang';

    /**
     * @return iterable<string, array{string}>
     */
    public static function translations(): iterable
    {
        foreach (glob(self::LANG_DIR . '/*.yml') ?: [] as $path) {
            $locale = basename($path, '.yml');
            if ($locale !== 'en') {
                yield $locale => [$locale];
            }
        }
    }

    #[DataProvider('translations')]
    public function testTranslationCoversExactlyTheEnglishKeys(string $locale): void
    {
        $this->assertSame($this->keysOf('en'), $this->keysOf($locale));
    }

    /**
     * @return list<string>
     */
    private function keysOf(string $locale): array
    {
        /** @var array<string, array<string, array<string, mixed>>> $parsed */
        $parsed = Yaml::parseFile(self::LANG_DIR . "/{$locale}.yml");
        $this->assertArrayHasKey($locale, $parsed);

        $keys = [];
        foreach ($parsed[$locale] as $class => $entities) {
            foreach (array_keys($entities) as $entity) {
                $keys[] = "{$class}.{$entity}";
            }
        }
        sort($keys);

        return $keys;
    }
}
