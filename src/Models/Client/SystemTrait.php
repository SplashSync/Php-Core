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

namespace Splash\Core\Models\Client;

/**
 * Framework System information Methods
 */
trait SystemTrait
{
    /**
     * Check if Framework Instanced in Debug Mode
     * Used for PhpUnit Tests
     */
    public static function isDebugMode(): bool
    {
        return (defined('SPLASH_DEBUG') && !empty(SPLASH_DEBUG));
    }

    /**
     * Check if Framework Instanced in CI/CD Mode
     */
    public static function isCiCdMode(): bool
    {
        return !empty(self::input("SPLASH_TRAVIS"))
            || !empty(self::input("SPLASH_CI"))
        ;
    }

    /**
     * Check if Framework Instanced in Travis => CI/CD Mode
     *
     * @deprecated use isCiCdMode instead
     */
    public static function isTravisMode(): bool
    {
        return self::isCiCdMode();
    }

    /**
     * Check if Framework Instanced in Server Mode
     */
    public static function isServerMode(): bool
    {
        return (defined('SPLASH_SERVER_MODE')
            && !empty(SPLASH_SERVER_MODE))
        ;
    }
}
