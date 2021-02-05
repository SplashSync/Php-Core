<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2021 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Tests\Tools\Fields;

use ArrayObject;

/**
 * Double Field : Float Value as Text
 */
class Oodouble implements FieldInterface
{
    //==============================================================================
    //      Structural Data
    //==============================================================================

    /** @var string */
    protected $FORMAT = 'Double';

    //==============================================================================
    //      DATA VALIDATION
    //==============================================================================

    /**
     * {@inheritdoc}
     */
    public static function validate($data)
    {
        //==============================================================================
        //      Verify Data is an Array
        if (is_array($data) || ($data instanceof ArrayObject)) {
            return "Field Data is not Double or Float Value.";
        }
        //==============================================================================
        //      Verify Data is a Double or Zero
        if (is_double($data) || (0 == $data)) {
            return true;
        }
        //==============================================================================
        //      Verify Data is a Double as String
        if (is_string($data) && (is_double(floatval($data)))) {
            return true;
        }

        return "Field Data is not Double or Float Value.";
    }

    //==============================================================================
    //      FAKE DATA GENERATOR
    //==============================================================================

    /**
     * {@inheritdoc}
     */
    public static function fake($settings)
    {
        return (double) mt_rand(1, 1000) / 10;
    }

    //==============================================================================
    //      DATA COMPARATOR (OPTIONNAL)
    //==============================================================================

    /**
     * {@inheritdoc}
     */
    public static function compare($source, $target, $settings)
    {
        //====================================================================//
        // Compare Float Values
        if (abs(round($source, $settings["DoublesPrecision"]) - round($target, $settings["DoublesPrecision"])) > 1E-6) {
            return false;
        }

        return true;
    }
}
