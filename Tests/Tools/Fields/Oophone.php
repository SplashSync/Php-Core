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
 * Phone Field : Define a Contact Phone Number
 */
class Oophone extends Oovarchar implements FieldInterface
{
    //==============================================================================
    //      Structural Data
    //==============================================================================

    /** @var string */
    protected $FORMAT = 'Phone';

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
        if (!is_string($data)) {
            return "Phone Number Field Data is not a String.";
        }

        //==============================================================================
        //      Verify Data is a Phone Number
        if (preg_match('/^[+0-9. ()-]*$/', $data)) {
            return true;
        }

        return "Field Data is not a Phone Number.";
    }

    //==============================================================================
    //      FAKE DATA GENERATOR
    //==============================================================================

    /**
     * {@inheritdoc}
     */
    public static function fake($settings)
    {
        return (string) preg_replace('/^[+0-9. ()-]*$/', '', (string) mt_rand(12345678, 123456789));
    }
}
