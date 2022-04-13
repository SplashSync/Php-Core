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
 * Currency Field : ISO Currency Code
 *
 * @example     USD, EUR.
 *
 * @see ISO 4217 : http://www.iso.org/iso/home/standards/currency_codes.htm
 */
class OoCurrency extends OoVarchar implements FieldInterface
{
    //==============================================================================
    //      Structural Data
    //==============================================================================

    /**
     * @var string
     */
    const FORMAT = 'Currency';

    /**
     * @var array
     */
    public static array $fakeData = array("EUR", "USD", "INR");

    //==============================================================================
    //      DATA VALIDATION
    //==============================================================================

    /**
     * {@inheritdoc}
     */
    public static function validate($data): ?string
    {
        if (!empty($data) && !is_string($data)) {
            return "Field  Data is not a String.";
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
        return static::$fakeData[ (mt_rand(0, count(static::$fakeData) - 1)) ];
    }
}
