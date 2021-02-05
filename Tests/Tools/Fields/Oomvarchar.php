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
 * Multilangual Text Field : Multilangual Short Text Array
 *
 * //====================================================================//
 * // Sample :
 * // $data["name"]["iso_code"]            =>      Value
 * // Where name is field name and code is a valid SPL_T_LANG Iso Language Code
 * //====================================================================//
 */
class Oomvarchar implements FieldInterface
{
    //==============================================================================
    //      Structural Data
    //==============================================================================

    /** @var string */
    protected $FORMAT = 'MVarchar';

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
        if (is_null($data) || (is_scalar($data) && ("" === $data))) {
            return true;
        }
        //==============================================================================
        //      Verify Data is an Array
        if (!is_array($data) && !($data instanceof ArrayObject)) {
            return "Field Data is not an Array.";
        }

        //==============================================================================
        //      Verify each Ligne is a String
        foreach ($data as $key => $value) {
            if (!self::validateIsMultilangData($key, $value)) {
                return self::validateIsMultilangData($key, $value);
            }
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
        $fake = array();
        foreach ($settings["Langs"] as $lang) {
            $fake[$lang] = Oovarchar::fake($settings);
        }

        return $fake;
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
        //  If Raw Text received, Not Array ==> Raw text Compare
        if (!is_array($source) && !is_a($target, "ArrayObject")
                && !is_array($target) && !is_a($target, "ArrayObject")) {
            return ($source === $target)?true:false;
        }
        //====================================================================//
        //  Verify Available Languages Count
        if (count($source) !== count($target)) {
            return false;
        }
        //====================================================================//
        //  Verify Each Languages Are Similar Strings
        foreach ($source as $key => $value) {
            if ($target[$key] !== $value) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param mixed $key
     * @param mixed $value
     *
     * @return string|true
     */
    private static function validateIsMultilangData($key, $value)
    {
        if (empty($key) || !is_string($key)) {
            return "Multi-Language Key must be a non empty String.";
        }
        if (!empty($value) && !is_string($value) && !is_numeric($value)) {
            return "Multi-Language Data is not a String.";
        }

        return true;
    }
}
