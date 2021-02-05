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
 * Url Field : Full Link, generic URI
 *
 * @see http://www.faqs.org/rfcs/rfc2396.html
 */
class Oourl extends Oovarchar implements FieldInterface
{
    //==============================================================================
    //      Structural Data
    //==============================================================================

    /** @var string */
    protected $FORMAT = 'Url';

    //==============================================================================
    //      DATA VALIDATION
    //==============================================================================

    /**
     * {@inheritdoc}
     */
    public static function validate($data)
    {
        //==============================================================================
        //      Verify Data is not Empty
        if (empty($data)) {
            return true;
        }

        //==============================================================================
        //      Verify Data is a String
        if (!empty($data) && !is_string($data)) {
            return "Field  Data is not a String.";
        }

        //==============================================================================
        //      Verify Data is a Valid URI
//        if (!filter_var($Data, FILTER_VALIDATE_URL) !== False) {
//            return "Field Data is not a Valid Url";
//        }

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
        $domain = preg_replace('/[^A-Za-z\-]/', '', strtolower(base64_encode((string) mt_rand(100, 1000))));
        $prefix = !empty($settings["Url_Prefix"]) ? $settings["Url_Prefix"] : null;
        $sufix = !empty($settings["Url_Sufix"])   ? $settings["Url_Sufix"] : ".splashsync.com";

        return $prefix.$domain.$sufix;
    }
}
