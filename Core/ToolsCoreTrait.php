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

namespace Splash\Core;

trait ToolsCoreTrait
{
    /**
     * Secured reading of Constants
     *
     * @param string $name
     *
     * @return null|string
     */
    public static function constant(string $name): ?string
    {
        if (!defined($name)) {
            return null;
        }
        $const = constant($name);
        if (!is_scalar($const)) {
            return null;
        }

        return (string) $const;
    }

    /**
     * Secured reading of Constants
     *
     * @param string $name
     *
     * @return null|string
     */
    public static function env(string $name): ?string
    {
        return self::input($name, INPUT_ENV);
    }

    /**
     * Secured reading of Server SuperGlobals
     *
     * @param string $name
     * @param int    $type
     *
     * @return null|string
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function input(string $name, int $type = INPUT_SERVER): ?string
    {
        //====================================================================//
        // Standard Safe Reading
        $result = filter_input($type, $name);
        if (is_scalar($result)) {
            return (string) $result;
        }
        //====================================================================//
        // Fallback Reading
        switch ($type) {
            case INPUT_SERVER:
                $value = $_SERVER[$name] ?? null;

                break;
            case INPUT_GET:
                $value = $_GET[$name] ?? null;

                break;
            case INPUT_ENV:
                $value = $_ENV[$name] ?? null;

                break;
            default:
                return null;
        }

        return is_scalar($value) ? (string) $value : null;
    }

    /**
     * Secured counting of Mixed Values
     *
     * @param mixed $value
     *
     * @return int
     */
    public static function count($value): int
    {
        if (is_null($value)) {
            return 0;
        }
        if (is_scalar($value) || is_object($value)) {
            return 1;
        }

        return is_countable($value) ? count($value) : 1;
    }
}
