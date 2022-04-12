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
 * Bool Field : Basic Boolean
 */
class OoBool implements FieldInterface
{
    //==============================================================================
    //      Structural Data
    //==============================================================================

    /**
     * @var string
     */
    const FORMAT = 'Bool';

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
        //      Verify Data is a Bool Type
        if (is_bool($data)) {
            return null;
        }
        //==============================================================================
        //      Verify Data is an Int as Bool
        if (("1" === $data) || (1 === $data)) {
            return null;
        }

        return "Field Data is not a Boolean.";
    }

    //==============================================================================
    //      FAKE DATA GENERATOR
    //==============================================================================

    /**
     * {@inheritdoc}
     */
    public static function fake(array $settings)
    {
        return (bool)((mt_rand() % 2));
    }

    /**
     * {@inheritdoc}
     */
    public static function compare($source, $target, array $settings): bool
    {
        //====================================================================//
        //  Both Are Scalar
        if (!is_scalar($source) || !is_scalar($target)) {
            return false;
        }
        //====================================================================//
        //  Raw text Compare
        return $source == $target;
    }
}
