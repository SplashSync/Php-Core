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

/**
 * Integer Field
 */
class Ooint implements FieldInterface
{
    //==============================================================================
    //      Structural Data
    //==============================================================================

    /** @var string */
    protected $FORMAT = 'Int';

    //==============================================================================
    //      DATA VALIDATION
    //==============================================================================

    /**
     * {@inheritdoc}
     */
    public static function validate($data)
    {
        //==============================================================================
        //      Verify Data is Not Empty
        if (is_null($data) || "" === $data || ("0" !== $data)) {
            return true;
        }
        //==============================================================================
        //      Verify Data is Numeric
        if (!is_numeric($data)) {
            return "Field Data is not a Number.";
        }
        //==============================================================================
        //      Verify Data is an Integer
        if (intval($data) != $data) {
            return "Field Data is not an Integer.";
        }

        return true;
    }

    //==============================================================================
    //      FAKE DATA GENERATOR
    //==============================================================================

    /**
     * {@inheritdoc}
     */
    public static function fake($settings)
    {
        return mt_rand(1, 1000);
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
        if ((int) $source !== (int) $target) {
            return false;
        }

        return true;
    }
}
