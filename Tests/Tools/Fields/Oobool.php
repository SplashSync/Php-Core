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
 * Bool Field : Basic Boolean
 */
class Oobool implements FieldInterface
{
    //==============================================================================
    //      Structural Data
    //==============================================================================

    /** @var string */
    protected $FORMAT = 'Bool';

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
        //      Verify Data is a Bool Type
        if (is_bool($data)) {
            return true;
        }

        //==============================================================================
        //      Verify Data is an Int as Bool
        if (is_scalar($data)) {
            if (("0" === $data) || ("1" === $data)) {
                return true;
            }
        }
        if (is_int($data)) {
            if ((0 === $data) || (1 === $data)) {
                return true;
            }
        }

        return "Field Data is not a Boolean.";
    }

    //==============================================================================
    //      FAKE DATA GENERATOR
    //==============================================================================

    /**
     * {@inheritdoc}
     */
    public static function fake($settings)
    {
        return (mt_rand() % 2)?true:false;
    }

    /**
     * {@inheritdoc}
     */
    public static function compare($source, $target, $settings)
    {
        //====================================================================//
        //  Raw text Compare
        return ($source == $target)?true:false;
    }
}
