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

namespace Splash\Tests\Tools\Traits;

/**
 * Objects Faker Settings Trait
 */
trait SettingsTrait
{
    /**
     * Formater Fake Field Generator Options
     *
     * @var array
     */
    protected $settings = array(
        //==============================================================================
        //  List generation
        'ListItems' => 2,               // Number of Items to Add in Lists

        //==============================================================================
        //  Double & Prices Fields
        "DoublesPrecision" => 6,              // Default Doubles Compare Precision (Number of Digits)

        //==============================================================================
        //  Currency Fields
        "Currency" => "EUR",          // Default Currency

        //==============================================================================
        //  Phone Fields
        "PhoneISO" => true,             // Use ISO Formatted Phone Numbers
        "PhoneDigits" => 8,             // Number of Digits for Phone Numbers

        //==============================================================================
        //  Price Fields
        "VAT" => 20,              // Default Vat Rate
        "PriceBase" => "HT",            // Default Price base
        "PricesPrecision" => 6,               // Default Prices Compare Precision (Number of Digits)

        //==============================================================================
        //  Url Generator Parameters
        "Url_Prefix" => "",               // Add a prefix to generated Url (i.e: http://)
        "Url_Sufix" => ".splashsync.com",// Add a sufix to generated Url

        //==============================================================================
        //  Multilanguage Fields
        "Default_Lang" => "en_US",         // Default Language for Testing
        "Langs" => array(          // Available Languages for Multilang Fields
            "en_US",
            "fr_FR",
            "fr_BE",
            "fr_CA",
        ),

        //==============================================================================
        //  Country Fields
        "Country" => array(          // Defaults State Iso Codes
            "US",
            "FR",
            "BE",
            "CA",
        ),

        //==============================================================================
        //  State Fields
        "States" => array(          // Defaults State Iso Codes
            "CA",
            "FL"
        ),

        //==============================================================================
        //  Files Fields
        "Files" => array(          // Defaults Raw Files
            "fake-file1.pdf",
            "fake-file2.pdf",
            "fake-file3.pdf",
            "fake-file4.pdf",
        ),

        //==============================================================================
        //  Images Fields
        "Images" => array(          // Defaults Image Files
            "fake-image1.jpg",
            "fake-image2.jpg",
            "fake-image3.jpg",
            "fake-image4.jpg",
        ),

        //            //==============================================================================
        //            //  Objects Id Fields
        //            //  Default is An Empty List To be completed by User Before Generation
        //            "Objects"                   =>  array(),
    );
}
