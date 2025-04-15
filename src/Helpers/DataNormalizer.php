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

namespace Splash\Core\Helpers;

use ArrayObject;

/**
 * Normalize Object Data Block Contents
 */
class DataNormalizer
{
    /**
     * Normalize An Object Data Block (ie: before Compare)
     *
     * @param mixed $input Input Array
     *
     * @return mixed Sorted Array
     */
    public static function normalize(&$input)
    {
        //==============================================================================
        // Convert ArrayObjects To Simple Array
        if ($input instanceof ArrayObject) {
            $input = $input->getArrayCopy();
            //==============================================================================
            // Normalize Contents
            self::normalize($input);
        } elseif (is_array($input)) {
            //==============================================================================
            // Normalize Array Contents
            foreach ($input as &$value) {
                self::normalize($value);
            }
        } elseif (is_bool($input)) {
            //==============================================================================
            // Normalize Bool as Strings
            $input = $input ? '1' : '0';
        } elseif (is_numeric($input)) {
            //==============================================================================
            // Normalize Numbers as Strings
            $input = strval($input);
        }

        return $input;
    }

    /**
     * kSort of An Object Data Block (ie: before Compare)
     *
     * @param array $inputArray Input Array
     *
     * @return array Sorted Array
     */
    public static function sort(array &$inputArray): array
    {
        //==============================================================================
        // Sort All Sub-Contents
        foreach ($inputArray as &$value) {
            if (is_array($value)) {
                self::sort($value);
            }
        }
        //==============================================================================
        // Sort Main Contents
        ksort($inputArray);

        return $inputArray;
    }
}
