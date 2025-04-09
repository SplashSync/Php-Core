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

namespace Splash\Core\Helpers\System;

use Splash\Core\Client\Splash;
use Splash\Core\Dictionary\SplDefinition;

/**
 * Perform Common System Checks
 */
class SystemChecker
{
    /**
     * Verify PHP Version is Compatible.
     */
    public static function isValidPHPVersion(string $version = null, bool $silent = false): bool
    {
        //====================================================================//
        // Use System Configuration by Default
        $version ??= SplDefinition::MIN_PHP_VERSION;
        //====================================================================//
        // Compare Versions
        if (version_compare(PHP_VERSION, $version) < 0) {
            if ($silent) {
                return false;
            }

            return Splash::log()->err(sprintf(
                'PHP : Your PHP version is too low to use Splash (%s). PHP >%s is Required.',
                PHP_VERSION,
                $version
            ));
        }
        if ($silent) {
            return true;
        }

        return Splash::log()->msg(
            'PHP : Your PHP version is compatible with Splash ('.PHP_VERSION.')'
        );
    }

    /**
     * Verify PHP Required are Installed & Active
     *
     * @param string[] $extensions
     */
    public static function isValidPHPExtensions(array $extensions = null, bool $silent = false): bool
    {
        static $isValid;

        //====================================================================//
        // Use Cache
        if (is_null($extensions) && isset($isValid)) {
            return $isValid;
        }
        //====================================================================//
        // Use System Configuration by Default
        $required = $extensions ?? SplDefinition::MIN_PHP_EXT;
        //====================================================================//
        // Verify Extensions
        foreach ($required as $extension) {
            if (!extension_loaded($extension)) {
                $isValid = is_null($extensions) ? null: false;
                if ($silent) {
                    return false;
                }

                return Splash::log()->err(sprintf(
                    'PHP : %s PHP Extension is required to use Splash PHP Module.',
                    $extension
                ));
            }
        }
        $isValid = is_null($extensions) ? null: true;
        if ($silent) {
            return true;
        }

        return Splash::log()->msg(
            'PHP : Required PHP Extension are installed ('.implode(', ', $required).')'
        );
    }
}
