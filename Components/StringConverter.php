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

namespace Splash\Components;

/**
 * Simple String Converter
 */
class StringConverter
{
    /**
     * Convert any string to Canonical Code
     */
    public static function canonicalString(?string $input): ?string
    {
        //====================================================================//
        // Remove any Accent Chars
        if (extension_loaded("iconv")) {
            $input = iconv('UTF-8', 'ASCII//TRANSLIT', (string) $input) ?: $input;
            $input = utf8_encode((string) $input);
        }
        //====================================================================//
        // Replace All Special Chars
        $input = preg_replace('#[^A-Za-z0-9]#', '_', (string) $input);
        //====================================================================//
        // Convert to Lower
        $input = strtolower((string) $input);
        //====================================================================//
        // Safety Check => Empty String
        return $input ?: null;
    }
}
