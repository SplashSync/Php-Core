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

namespace Splash\Tests\Tools\Fields;

/**
 * Double Field : Float Value as Text
 */
class OoDouble implements FieldInterface
{
    //==============================================================================
    //      Structural Data
    //==============================================================================

    /**
     * @var string
     */
    const FORMAT = 'Double';

    //==============================================================================
    //      DATA VALIDATION
    //==============================================================================

    /**
     * {@inheritdoc}
     */
    public static function validate($data): ?string
    {
        //==============================================================================
        // Verify Data is an Array
        if (is_array($data)) {
            return "Field Data is not Double or Float Value.";
        }
        //==============================================================================
        //      Verify Data is a Double or Zero
        if (is_double($data) || (0 == $data)) {
            return null;
        }
        //==============================================================================
        //      Verify Data is a Double as String
        if (is_string($data) && (is_double(floatval($data)))) {
            return null;
        }

        return "Field Data is not Double or Float Value.";
    }

    //==============================================================================
    //      FAKE DATA GENERATOR
    //==============================================================================

    /**
     * {@inheritdoc}
     */
    public static function fake(array $settings)
    {
        return (double) mt_rand(1, 1000) / 10;
    }

    //==============================================================================
    //      DATA COMPARATOR (OPTIONAL)
    //==============================================================================

    /**
     * {@inheritdoc}
     */
    public static function compare($source, $target, array $settings): bool
    {
        //====================================================================//
        //  Both Are Scalar
        if (!is_scalar($source) || !is_scalar($target)) {
            return false;
        }
        //====================================================================//
        // Compare Float Values
        if (abs(round((float) $source, $settings["DoublesPrecision"])
                - round((float) $target, $settings["DoublesPrecision"])) > 1E-6) {
            return false;
        }

        return true;
    }
}
