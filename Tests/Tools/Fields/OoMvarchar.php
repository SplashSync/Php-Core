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
 * Multi-langual Text Field : Multi-langual Short Text Array
 *
 * //====================================================================//
 * // Sample :
 * // $data["name"]["iso_code"]            =>      Value
 * // Where name is field name and code is a valid SPL_T_LANG Iso Language Code
 * //====================================================================//
 */
class OoMvarchar implements FieldInterface
{
    //==============================================================================
    //      Structural Data
    //==============================================================================

    /**
     * @var string
     */
    const FORMAT = 'MVarchar';

    //==============================================================================
    //      DATA VALIDATION
    //==============================================================================

    /**
     * {@inheritdoc}
     */
    public static function validate($data): ?string
    {
        //==============================================================================
        //      Verify Data is Not Empty
        if (is_null($data) || ("" === $data)) {
            return null;
        }
        //==============================================================================
        //      Verify Data is an Array
        if (!is_array($data)) {
            return "Field Data is not an Array.";
        }

        //==============================================================================
        //      Verify each Ligne is a String
        foreach ($data as $key => $value) {
            if (!self::validateIsMultiLangData($key, $value)) {
                return self::validateIsMultiLangData($key, $value);
            }
        }

        return null;
    }

    //==============================================================================
    //      FAKE DATA GENERATOR
    //==============================================================================

    /**
     * {@inheritdoc}
     */
    public static function fake(array $settings)
    {
        $fake = array();
        foreach ($settings["Langs"] as $lang) {
            $fake[$lang] = OoVarchar::fake($settings);
        }

        return $fake;
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
        //  If Raw Text received, Not Array ==> Raw text Compare
        if (!is_array($source) && !is_array($target)) {
            return $source === $target;
        }
        //====================================================================//
        //  Mixed Types received ==> Different
        if (!is_array($source) || !is_array($target)) {
            return false;
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
     * @return null|string
     */
    private static function validateIsMultiLangData($key, $value): ?string
    {
        if (empty($key) || !is_string($key)) {
            return "Multi-Language Key must be a non empty String.";
        }
        if (!empty($value) && !is_string($value) && !is_numeric($value)) {
            return "Multi-Language Data is not a String.";
        }

        return null;
    }
}
