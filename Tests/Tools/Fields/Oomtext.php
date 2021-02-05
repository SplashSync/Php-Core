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
 * Multilangual Text Field : Multilangual Long Text Array
 *
 * //====================================================================//
 * // Sample :
 * // $data["name"]["iso_code"]            =>      Value
 * // Where name is field name and code is a valid SPL_T_LANG Iso Language Code
 * //====================================================================//
 */
class Oomtext implements FieldInterface
{
    //==============================================================================
    //      Structural Data
    //==============================================================================

    /** @var string */
    protected $FORMAT = 'MText';

    //==============================================================================
    //      DATA VALIDATION
    //==============================================================================

    /**
     * {@inheritdoc}
     */
    public static function validate($data)
    {
        return Oomvarchar::validate($data);
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
            $fake[$lang] = Ootext::fake($settings);
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
}
