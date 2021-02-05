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
 * Text Field : Long Text Data Block
 */
class Ootext implements FieldInterface
{
    //==============================================================================
    //      Structural Data
    //==============================================================================

    /** @var string */
    protected $FORMAT = 'Text';

    //==============================================================================
    //      DATA VALIDATION
    //==============================================================================

    /**
     * {@inheritdoc}
     */
    public static function validate($data)
    {
        if (!empty($data) && !is_string($data)) {
            return "Field  Data is not a String.";
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
        return Oovarchar::fake($settings);
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
        //  Both Texts Are Empty
        if (empty($source) && empty($target)) {
            return true;
        }
        //====================================================================//
        //  Raw text Compare
        return ($source === $target)?true:false;
    }
}
