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

// phpcs:disable PSR1.Classes.ClassDeclaration

namespace Splash\Tests\Tools;

use PHPUnit\Framework\TestCase      as BaseTestCase;
use Splash\Bundle\Tests\WebTestCase;
use Splash\Client\Splash;
use Throwable;

/**
 * Ensure Server Mode is Active
 */
if (!defined("SPLASH_SERVER_MODE")) {
    define("SPLASH_SERVER_MODE", true);
}

/**
 * Check if Php Bundle is installed, if so, use Symfony Web Test Case as base Test Case
 */
if (!class_exists("Splash\\Bundle\\Tests\\WebTestCase")) {
    abstract class TestCase extends BaseTestCase
    {
        /**
         * {@inheritDoc}
         */
        public static function setUpBeforeClass(): void
        {
            parent::setUpBeforeClass();

            /**
             * Ensure Splash definitions are Loaded
             */
            if (!defined("SPL_PROTOCOL")) {
                include dirname(__DIR__, 2)."/inc/Splash.Inc.php";
            }
        }

        /**
         * @param Throwable $exception
         *
         * @throws Throwable
         *
         * @return void
         */
        public function onNotSuccessfulTest(Throwable $exception): void
        {
            //====================================================================//
            // Do not display log on Skipped Tests
            if (is_a($exception, "PHPUnit\\Framework\\SkippedTestError")) {
                throw $exception;
            }
            //====================================================================//
            // Remove Debug From Splash Logs
            Splash::log()->deb = array();
            //====================================================================//
            // OutPut Splash Logs
            fwrite(STDOUT, Splash::log()->getConsoleLog());

            //====================================================================//
            // OutPut Phpunit Exception
            throw $exception;
        }
    }

    /**
     * Base PhpUnit Test Class for Splash Modules Tests
     * May be overridden for Using Splash Core Test in Specific Environments
     */
} else {
    abstract class TestCase extends WebTestCase
    {
        /**
         * {@inheritDoc}
         */
        public static function setUpBeforeClass(): void
        {
            /** @phpstan-ignore-next-line  */
            parent::setUpBeforeClass();

            /**
             * Ensure Splash definitions are Loaded
             */
            if (!defined("SPL_PROTOCOL")) {
                include dirname(__DIR__, 2)."/inc/Splash.Inc.php";
            }
        }
    }
}
