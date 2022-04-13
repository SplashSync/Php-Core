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
 * Text Field : Long Text Data Block
 */
class OoText implements FieldInterface
{
    //==============================================================================
    //      Structural Data
    //==============================================================================

    /**
     * @var string
     */
    const FORMAT = 'Text';

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
        return OoVarchar::fake($settings);
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
        return $source === $target;
    }
}
