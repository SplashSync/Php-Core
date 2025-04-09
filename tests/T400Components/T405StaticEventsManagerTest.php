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
use Splash\Core\Components\StaticEventsManager;

/**
 * Test class for StaticEventsManager's register method.
 */
class T405StaticEventsManagerTest extends TestCase
{
    /**
     * Test registering multiple callbacks with the same priority ensures execution order is maintained.
     */
    public function testRegisterSamePriorityCallbacks(): void
    {
        $context = TestContext::class;

        $callbackA = function ($ctx) {
            $ctx->value[] = "A";
        };
        $callbackB = function ($ctx) {
            $ctx->value[] = "B";
        };

        // Clear callbacks before starting
        StaticEventsManager::clearCallbacks();

        // Register two callbacks with the same priority
        StaticEventsManager::registerCallback($context, $callbackA, 5);
        StaticEventsManager::registerCallback($context, $callbackB, 5);

        $contextObject = new TestContext();
        StaticEventsManager::execute($contextObject);

        // Assert that callbacks are called in the order they were registered
        Assert::assertEquals(array("A", "B"), $contextObject->value);
    }

    /**
     * Test registering callbacks with higher and lower priorities ensures correct execution order.
     */
    public function testRegisterDifferentPriorityCallbacks(): void
    {
        $context = TestContext::class;

        $callbackLow = function ($ctx) {
            $ctx->value[] = "low";
        };
        $callbackHigh = function ($ctx) {
            $ctx->value[] = "high";
        };

        // Clear callbacks before starting
        StaticEventsManager::clearCallbacks();

        // Register one low and one high priority callback
        StaticEventsManager::registerCallback($context, $callbackLow, 1);
        StaticEventsManager::registerCallback($context, $callbackHigh, 10);

        $contextObject = new TestContext();
        StaticEventsManager::execute($contextObject);

        // Assert that higher priority callback executes first
        Assert::assertEquals(array("high", "low"), $contextObject->value);
    }

    /**
     * Test registering an object method as a callback.
     */
    public function testRegisterObjectMethodCallback(): void
    {
        $context = TestContext::class;

        $testObject = new class() {
            public function updateContext(TestContext $ctx): void
            {
                $ctx->value = "object_method_called";
            }
        };

        // Clear callbacks before starting
        StaticEventsManager::clearCallbacks();

        // Register an object method as a callback
        StaticEventsManager::register($context, array($testObject, 'updateContext'), 5);

        $contextObject = new TestContext();
        StaticEventsManager::execute($contextObject);

        // Assert that callback was executed correctly
        Assert::assertEquals("object_method_called", $contextObject->value);
    }
    /**
     * Test that a valid callable can be registered and prioritized for a valid context.
     */
    public function testRegisterValidCallback(): void
    {
        // Define a valid callback and context
        $context = TestContext::class;
        $callback = function ($ctx) {
            $ctx->value = "updated";
        };

        // Clear callbacks before starting to avoid interference
        StaticEventsManager::clearCallbacks();

        // Register the callback
        StaticEventsManager::registerCallback($context, $callback, 10);

        // Assert that callback was registered by executing it
        $contextObject = new TestContext();
        StaticEventsManager::execute($contextObject);
        Assert::assertEquals("updated", $contextObject->value);
    }

    /**
     * Test that multiple callbacks are sorted by priority during execution.
     */
    public function testRegisterWithPrioritySorting(): void
    {
        $context = TestContext::class;

        $callbackLowPriority = function ($ctx) {
            $ctx->value[] = "low";
        };
        $callbackHighPriority = function ($ctx) {
            $ctx->value[] = "high";
        };

        // Clear callbacks before starting
        StaticEventsManager::clearCallbacks();

        // Register two callbacks with differing priorities
        StaticEventsManager::registerCallback($context, $callbackLowPriority, 5);
        StaticEventsManager::registerCallback($context, $callbackHighPriority, 10);

        $contextObject = new TestContext();
        StaticEventsManager::execute($contextObject);

        // Assert that high priority callback executed before low priority callback
        Assert::assertEquals(array("high", "low"), $contextObject->value);
    }

    /**
     * Test that clearing callbacks removes previous registrations.
     */
    public function testClearCallbacks(): void
    {
        $context = TestContext::class;

        $callback = function ($ctx) {
            $ctx->value = "updated";
        };

        // Register the callback
        StaticEventsManager::registerCallback($context, $callback, 10);

        // Clear all callbacks
        StaticEventsManager::clearCallbacks();

        $contextObject = new TestContext();
        StaticEventsManager::execute($contextObject);

        // Assert that no callbacks are executed after clearing
        Assert::assertEquals(null, $contextObject->value);
    }
}

/**
 * Dummy context class used for testing.
 */
class TestContext
{
    /**
     * @var array|string
     */
    public $value;
}
