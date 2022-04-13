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
 * Phone Field : Define a Contact Phone Number
 */
class OoPhone extends OoVarchar implements FieldInterface
{
    //==============================================================================
    //      Structural Data
    //==============================================================================

    /**
     * @var string
     */
    const FORMAT = 'Phone';

    //==============================================================================
    //      DATA VALIDATION
    //==============================================================================

    /**
     * {@inheritdoc}
     */
    public static function validate($data): ?string
    {
        //==============================================================================
        //      Verify Data is not Empty
        if (empty($data)) {
            return null;
        }

        //==============================================================================
        //      Verify Data is a String
        if (!is_string($data)) {
            return "Phone Number Field Data is not a String.";
        }

        //==============================================================================
        //      Verify Data is a Phone Number
        if (preg_match('/^[+0-9. ()-]*$/', $data)) {
            return null;
        }

        return "Field Data is not a Phone Number.";
    }

    //==============================================================================
    //      FAKE DATA GENERATOR
    //==============================================================================

    /**
     * {@inheritdoc}
     */
    public static function fake(array $settings)
    {
        $phoneNumber = "";
        //==============================================================================
        // Build ISO Prefix
        if ($settings["PhoneISO"] ?? true) {
            $phoneNumber .= "+".rand(10, 999);
        }
        //==============================================================================
        // Generate Random Phone Number
        for ($i = 0; $i < ($settings["PhoneDigits"] ?? 8); ++$i) {
            $phoneNumber .= rand(0, 9);
        }

        return $phoneNumber;
    }
}
