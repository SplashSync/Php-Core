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
 * DateTime Field : Date & Time as Text (Format Y-m-d G:i:s)
 *
 * @example     2016-12-25 12:25:30
 */
class OoDatetime extends OoVarchar implements FieldInterface
{
    //==============================================================================
    //      Structural Data
    //==============================================================================

    /**
     * @var string
     */
    const FORMAT = 'DateTime';

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
        //      Verify Data is a Scalar
        if (!is_scalar($data)) {
            return "Field Data is not a DateTime with right Format (".SPL_T_DATETIMECAST.").";
        }
        //==============================================================================
        //      Verify Data is a DateTime Type
        if (false !== \DateTime::createFromFormat(SPL_T_DATETIMECAST, (string) $data)) {
            return null;
        }

        return "Field Data is not a DateTime with right Format (".SPL_T_DATETIMECAST.").";
    }

    //==============================================================================
    //      FAKE DATA GENERATOR
    //==============================================================================

    /**
     * {@inheritdoc}
     */
    public static function fake(array $settings)
    {
        //==============================================================================
        //      Generate a random DateTime
        $date = new \DateTime("now");
        $date->modify('-'.mt_rand(1, 10).' months');
        $date->modify('-'.mt_rand(1, 60).' minutes');
        $date->modify('-'.mt_rand(1, 60).' seconds');
        //==============================================================================
        //      Return DateTime is Right Format
        return $date->format(SPL_T_DATETIMECAST);
    }
}
