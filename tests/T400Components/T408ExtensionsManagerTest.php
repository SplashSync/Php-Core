<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Core\Tests\T400Components;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Splash\Core\Components\ExtensionsManager;
use Splash\Core\Components\FilesLoader;

/**
 * Test Extensions Manager detection of a class file.
 *
 * The loader builds paths from the configured folder while PHP returns
 * resolved paths: both must be compared as files, not as strings.
 */
class T408ExtensionsManagerTest extends TestCase
{
    /**
     * A class is detected when the path is exactly the one PHP knows.
     */
    public function testClassFileWithExactPath(): void
    {
        Assert::assertSame(
            self::class,
            TestableExtensionsManager::checkClassFile(self::class, __FILE__)
        );
    }

    /**
     * A class is detected when the path is not normalized.
     *
     * @dataProvider unresolvedPathsProvider
     */
    public function testClassFileWithUnresolvedPath(string $fullPath): void
    {
        Assert::assertNotSame(__FILE__, $fullPath);
        Assert::assertSame(
            self::class,
            TestableExtensionsManager::checkClassFile(self::class, $fullPath)
        );
    }

    /**
     * A class is not detected from another file, nor when the class is unknown.
     */
    public function testClassFileRejected(): void
    {
        $otherFile = __DIR__."/T405StaticEventsManagerTest.php";
        $missingFile = __DIR__."/".basename(__DIR__).".php";

        Assert::assertNull(TestableExtensionsManager::checkClassFile(self::class, $missingFile));
        Assert::assertNull(TestableExtensionsManager::checkClassFile(self::class, $otherFile));
        Assert::assertNull(TestableExtensionsManager::checkClassFile("Splash\\Local\\Objects\\Unknown", __FILE__));
    }

    /**
     * Files loader paths point to the listed files.
     */
    public function testFilesLoaderPathsAreResolvable(): void
    {
        $files = FilesLoader::load(__DIR__."/", "php", 0);

        Assert::assertArrayHasKey(pathinfo(__FILE__, PATHINFO_FILENAME), $files);
        foreach ($files as $filename => $fullPath) {
            Assert::assertSame(realpath($fullPath), $fullPath, $filename);
        }
    }

    /**
     * Paths pointing to this file without being its resolved path.
     *
     * @return array<string, array{0: string}>
     */
    public function unresolvedPathsProvider(): array
    {
        $name = basename(__FILE__);
        $dir = basename(__DIR__);

        return array(
            "parent segment" => array(__DIR__."/../".$dir."/".$name),
            "current segment" => array(__DIR__."/./".$name),
            "double separator" => array(__DIR__."//".$name),
            "trailing separator in folder" => array(rtrim(__DIR__, "/")."/"."/".$name),
        );
    }
}

/**
 * Expose class file detection for tests.
 */
class TestableExtensionsManager extends ExtensionsManager
{
    /**
     * Check if a Class is Defined in a File.
     *
     * @return null|class-string
     */
    public static function checkClassFile(string $className, string $fullPath): ?string
    {
        return self::isClassFile($className, $fullPath);
    }
}
