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
use Splash\Core\Configurator\StaticConfigurator;

/**
 * Core Test Suite - Test & Verifications for Object Configurator Disable Feature
 */
class T131DisableConfiguratorTest extends TestCase
{
    /**
     * Test the isDisabled method for a case where configuration is empty
     */
    public function testIsDisabledWithEmptyConfiguration(): void
    {
        $configurator = new StaticConfigurator();
        $configurator->setObjectConfiguration("TestObject", array());
        Assert::assertFalse($configurator->isDisabled("TestObject", false));
        Assert::assertTrue($configurator->isDisabled("TestObject", true));
    }

    /**
     * Test the isDisabled method for a case where configuration returns explicit disabled value
     */
    public function testIsDisabledWithConfigurationValue(): void
    {
        $configurator = new StaticConfigurator();
        $configurator->setObjectConfiguration("TestObject", array(
            "disabled" => true
        ));
        Assert::assertTrue($configurator->isDisabled("TestObject", false));
        Assert::assertTrue($configurator->isDisabled("TestObject", true));

        $configurator->setObjectConfiguration("TestObject", array(
            "disabled" => false
        ));
        Assert::assertFalse($configurator->isDisabled("TestObject", false));
        Assert::assertFalse($configurator->isDisabled("TestObject", true));
    }
}
