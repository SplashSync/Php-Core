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
 * State Field : ISO State Code
 *
 * @example     US-CA : California
 *
 * @see         ISO 3166 Standard : www.iso.org/iso/country_codes
 */
class OoState extends OoVarchar implements FieldInterface
{
    //==============================================================================
    //      Structural Data
    //==============================================================================

    /**
     * @var string
     */
    const FORMAT = 'State';

    //==============================================================================
    //      FAKE DATA GENERATOR
    //==============================================================================

    /**
     * {@inheritdoc}
     */
    public static function fake(array $settings)
    {
        return (mt_rand() % 2)?"CA":"FL";
    }
}
