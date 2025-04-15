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

namespace Splash\Core\Tests\T100Core\T100Framework;

use Exception;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Splash\Core\Client\Splash;
use Splash\Core\Components;
use Splash\Core\Models\BaseClient;

/**
 * Core Test Suite - Test of Splash Micro Framework Class
 */
class T101FrameworkCoreTest extends TestCase
{
    /**
     * Splash Framework Core Class
     */
    public function testFrameworkCoreClass(): void
    {
        Assert::assertInstanceOf(
            BaseClient::class,
            Splash::core(),
            "Splash Core Class is Not from of Right Instance"
        );
    }

    /**
     * Splash Framework Logger
     */
    public function testFrameworkLoggerClass(): void
    {
        Assert::assertInstanceOf(
            Components\Logger::class,
            Splash::log(),
            "Splash Logger Class is Not from of Right Instance"
        );
    }

    /**
     * Splash Framework Webservice Manager
     */
    public function testFrameworkWebserviceClass(): void
    {
        Assert::assertInstanceOf(
            Components\Webservice::class,
            Splash::ws(),
            "Splash Webservice Manager Class is Not from of Right Instance"
        );
    }

    /**
     * Splash Framework Router Manager
     */
    public function testFrameworkRouterClass(): void
    {
        Assert::assertInstanceOf(
            Components\Router::class,
            Splash::router(),
            "Splash Router Manager Class is Not from of Right Instance"
        );
    }

    /**
     * Splash Framework Files Manager
     */
    public function testFrameworkFilesClass(): void
    {
        Assert::assertInstanceOf(
            Components\FileManager::class,
            Splash::file(),
            "Splash Files Manager Class is Not from of Right Instance"
        );
    }

    /**
     * Splash Framework Validator
     */
    public function testFrameworkValidatorClass(): void
    {
        Assert::assertInstanceOf(
            Components\Validator::class,
            Splash::validate(),
            "Splash Validator Class is Not from of Right Instance"
        );
    }

    /**
     * Splash Framework Xml Manager
     */
    public function testFrameworkXmlClass(): void
    {
        Assert::assertInstanceOf(
            Components\XmlManager::class,
            Splash::xml(),
            "Splash Xml Manager Class is Not from of Right Instance"
        );
    }

    /**
     * Splash Framework Translator Manager
     */
    public function testFrameworkTranslatorClass(): void
    {
        Assert::assertInstanceOf(
            Components\Translator::class,
            Splash::translator(),
            "Splash Translator Manager Class is Not from of Right Instance"
        );
    }

    /**
     * Splash Framework Configuration Loading
     */
    public function testFrameworkConfiguration(): void
    {
        Assert::assertInstanceOf(
            \ArrayObject::class,
            Splash::configuration(),
            "Splash Configuration is Not an ArrayObject"
        );
        Assert::assertNotEmpty(
            Splash::configuration(),
            "Splash Configuration is Empty"
        );
    }

    /**
     * Splash Framework Objects Loading
     *
     * @throws Exception
     */
    public function testFrameworkObjects(): void
    {
        Assert::assertIsArray(
            Splash::objects(),
            "Splash Available Objects List is Not an Array"
        );
    }

    /**
     * Splash Framework Widgets Loading
     *
     * @throws Exception
     */
    public function testFrameworkWidgets(): void
    {
        Assert::assertIsArray(
            Splash::widgets(),
            "Splash Available Widgets List is Not an Array"
        );
    }

    /**
     * Splash Framework Widgets Loading
     *
     * @throws Exception
     */
    public function testFrameworkMethods(): void
    {
        Assert::assertNotEmpty(Splash::getName());
        Assert::assertNotEmpty(Splash::getDesc());
        Assert::assertNotEmpty(Splash::getVersion());
    }
}
