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

namespace Splash\Core\Tests\T100Core\T130Configurators;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Splash\Core\Models\AbstractConfigurator;

/**
 * Class T132DescriptionConfiguratorTest.
 *
 * This test class verifies the functionality of the overrideDescription method in the AbstractConfigurator:
 * - Ensures the method returns the original description when no configuration is provided.
 * - Confirms that unsecured keys in configuration are ignored during processing.
 * - Validates that keys not originally present in the description are excluded from the result.
 */
class T132DescriptionConfiguratorTest extends TestCase
{
    /**
     * Test that overrideDescription returns the original description when configuration is empty.
     */
    public function testOverrideDescriptionEmptyConfiguration(): void
    {
        $mock = $this->getMockForAbstractClass(AbstractConfigurator::class);

        $mock->expects($this->once())
            ->method('getConfiguration')
            ->willReturn(array());

        $description = array(
            "key1" => "value1",
            "key2" => "value2"
        );

        $result = $mock->overrideDescription("objectType", $description);

        Assert::assertSame($description, $result);
    }

    /**
     * Test that overrideDescription ignores unsecured description keys.
     */
    public function testOverrideDescriptionWithUnsecureKeys(): void
    {
        $mock = $this->getMockForAbstractClass(AbstractConfigurator::class);

        $mock->expects($this->any())
            ->method('getConfiguration')
            ->willReturn(array(
                "objectType" => array(
                    "fields" => "shouldBeIgnored",
                    "type" => "shouldBeIgnored",
                    "key1" => "overriddenValue1"
                )
            ));

        $description = array(
            "key1" => "value1",
            "key2" => "value2"
        );

        $result = $mock->overrideDescription("objectType", $description);

        Assert::assertArrayHasKey("key1", $result);
        Assert::assertSame("overriddenValue1", $result["key1"]);
        Assert::assertArrayHasKey("key2", $result);
        Assert::assertSame($description["key2"], $result["key2"]);
        Assert::assertArrayNotHasKey("fields", $result);
        Assert::assertArrayNotHasKey("type", $result);
    }

    /**
     * Test overrideDescription skips keys that don't exist in the original description.
     */
    public function testOverrideDescriptionKeyNotInDescription(): void
    {
        $mock = $this->getMockForAbstractClass(AbstractConfigurator::class);

        $mock->expects($this->any())
            ->method('getConfiguration')
            ->willReturn(array(
                "objectType" => array(
                    "keyDoesNotExist" => "value"
                )
            ));

        $description = array(
            "key1" => "value1",
        );

        $result = $mock->overrideDescription("objectType", $description);

        Assert::assertArrayNotHasKey("keyDoesNotExist", $result);
        Assert::assertSame($description, $result);
    }
}
