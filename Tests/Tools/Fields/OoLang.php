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
 * Language Field : ISO Language Code
 *
 * @example     en_US, fr_FR, fr_BE
 *
 * @see         ISO 639-1 : http://www.iso.org/iso/language_codes
 */
class OoLang extends OoVarchar implements FieldInterface
{
    //==============================================================================
    //      Structural Data
    //==============================================================================

    /**
     * @var string
     */
    const FORMAT = 'Lang';

    //==============================================================================
    //      FAKE DATA GENERATOR
    //==============================================================================

    /**
     * {@inheritdoc}
     */
    public static function fake(array $settings)
    {
        //==============================================================================
        //      Use Formater Settings
        if (!empty($settings["Langs"])) {
            //==============================================================================
            //      Select Random Language
            $index = rand(0, count($settings["Langs"]) - 1);
            //==============================================================================
            //      Return Language Code
            return $settings["Langs"][$index];
        }

        //==============================================================================
        //      Return Language Code
        return (mt_rand() % 2)?"en_US":"fr_FR";
    }
}
