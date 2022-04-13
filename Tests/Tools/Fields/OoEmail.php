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
 * Email Field : Standard Email Address
 */
class OoEmail extends OoVarchar implements FieldInterface
{
    //==============================================================================
    //      Structural Data
    //==============================================================================

    /**
     * @var string
     */
    const FORMAT = 'Email';

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
            return "Field  Data is not a String.";
        }

        //==============================================================================
        //      Verify Data is an Email Address
        if (false !== !filter_var($data, FILTER_VALIDATE_EMAIL)) {
            return "Field Data is not an Email Address";
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
        $name = preg_replace('/[^A-Za-z\-]/', '', base64_encode((string) mt_rand()));
        //==============================================================================
        //      Apply Domain Constraint
        if (isset($settings["emailDomain"]) && is_string($settings["emailDomain"])) {
            $domain = $settings["emailDomain"];
        } else {
            $domain = preg_replace('/[^A-Za-z\-]/', '', base64_encode((string) mt_rand(100, 1000)));
        }

        //==============================================================================
        //      Apply Extension Constraint
        if (isset($settings["emailExtension"]) && is_string($settings["emailExtension"])) {
            $extension = $settings["emailExtension"];
        } else {
            $extension = preg_replace('/[^A-Za-z\-]/', '', base64_encode((string) mt_rand(10, 100)));
        }

        return $name."@".$domain.".".$extension;
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
        //  Both Texts Are Empty
        if (empty($source) && empty($target)) {
            return true;
        }
        //====================================================================//
        //  Both Are Scalar
        if (!is_scalar($source) || !is_scalar($target)) {
            return false;
        }
        //====================================================================//
        //  Raw text Compare
        return strtolower((string) $source) === strtolower((string) $target);
    }
}
