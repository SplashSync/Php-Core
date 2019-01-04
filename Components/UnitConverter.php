<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2018 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace   Splash\Components;

/**
 * Splash Generic Unit Converter
 */
class UnitConverter
{
    //====================================================================//
    // MASS UNITS FACTORS (From KG to ...)
    //====================================================================//
    const MASS_MICROGRAM = 1E+9;
    const MASS_MILLIGRAM = 1E+6;
    const MASS_GRAM = 1E+3;
    const MASS_OUNCE = 35.273962;
    const MASS_KG = 1;
    const MASS_KILOGRAM = 1;
    const MASS_LIVRE = 2.20462262185;
    const MASS_TONNE = 1E-3;

    //====================================================================//
    // LENGTH UNITS FACTORS (From Meter to ...)
    //====================================================================//
    const LENGTH_MM = 1E3;
    const LENGTH_MILIMETER = 1E3;
    const LENGTH_CM = 1E2;
    const LENGTH_CENTIMETER = 1E2;
    const LENGTH_M = 1;
    const LENGTH_METER = 1;
    const LENGTH_KM = 1E-3;
    const LENGTH_FOOT = 3.280839895;
    const LENGTH_INCH = 39.370078740;

    //====================================================================//
    // SURFACE UNITS FACTORS (From M2 to ...)
    //====================================================================//
    const AREA_MM2 = 1E6;
    const AREA_CM2 = 1E4;
    const AREA_M2 = 1;
    const AREA_KM2 = 1E-6;
    const AREA_FOOT2 = 10.763910417;
    const AREA_INCH2 = 1550.003100006;
    
    //====================================================================//
    // VOLUME UNITS FACTORS (From M3 to ...)
    //====================================================================//
    const VOLUME_MM3 = 1E9;
    const VOLUME_MILILITER = 1E6;
    const VOLUME_CM3 = 1E6;
    const VOLUME_LITER = 1E4;
    const VOLUME_M3 = 1;
    const VOLUME_KM3 = 1E-9;
    const VOLUME_FOOT3 = 35.314666721;
    const VOLUME_INCH3 = 61023.744094732;
    
    /**
     * Convert Weight form KiloGram to Target Unit
     *
     * @param float $weight         Weight Value
     * @param float $toFactor     Target Unit Factor
     *
     * @return  float    Weight Value in Target unit
     */
    public static function convertWeight($weight, $toFactor)
    {
        return self::convert($weight, self::MASS_KG, $toFactor);
    }

    /**
     * Convert Weight form All Units to KiloGram
     *
     * @param float $weight         Weight Raw Value
     * @param float $fromFactor     Source Unit Factor
     *
     * @return  float    Weight Value in Kilogram
     */
    public static function normalizeWeight($weight, $fromFactor)
    {
        return self::convert($weight, $fromFactor, self::MASS_KG);
    }
    
    //====================================================================//
    // LENGTH UNITS CONVERTION
    //====================================================================//
    
    /**
     * Convert Length form Meter to Target Unit
     *
     * @param float $length     Length Value
     * @param float $toFactor   Target Unit Factor
     *
     * @return  float    Length Value in Target unit
     */
    public static function convertLength($length, $toFactor)
    {
        return self::convert($length, self::LENGTH_M, $toFactor);
    }

    /**
     * Convert Length form All Units to Meter
     *
     * @param float $length         Length Raw Value
     * @param float $fromFactor     Source Unit Factor
     *
     * @return  float    Length Value in Meter
     */
    public static function normalizeLength($length, $fromFactor)
    {
        return self::convert($length, $fromFactor, self::LENGTH_M);
    }
    
    //====================================================================//
    // SURFACE UNITS CONVERTION
    //====================================================================//
    
    /**
     * Convert Surface form Square Meter to Target Unit
     *
     * @param float $surface     Surface Value
     * @param float $toFactor   Target Unit Factor
     *
     * @return  float    Surface Value in Target unit
     */
    public static function convertSurface($surface, $toFactor)
    {
        return self::convert($surface, self::AREA_M2, $toFactor);
    }

    /**
     * Convert Surface form All Units to Square Meter
     *
     * @param float $surface        Surface Raw Value
     * @param float $fromFactor     Source Unit Factor
     *
     * @return  float    Surface Value in Square Meter
     */
    public static function normalizeSurface($surface, $fromFactor)
    {
        return self::convert($surface, $fromFactor, self::AREA_M2);
    }
    
    //====================================================================//
    // VOLUME UNITS CONVERTION
    //====================================================================//
    
    /**
     * Convert Volume form Cube Meter to Target Unit
     *
     * @param float $volume     Volume Value
     * @param float $toFactor   Target Unit Factor
     *
     * @return  float    Volume Value in Target unit
     */
    public static function convertVolume($volume, $toFactor)
    {
        return self::convert($volume, self::VOLUME_M3, $toFactor);
    }

    /**
     * Convert Volume form All Units to Cube Meter
     *
     * @param float $volume        Volume Raw Value
     * @param float $fromFactor     Source Unit Factor
     *
     * @return  float    Volume Value in Cube Meter
     */
    public static function normalizeVolume($volume, $fromFactor)
    {
        return self::convert($volume, $fromFactor, self::VOLUME_M3);
    }
    
    //====================================================================//
    // CORE UNIT CONVERTOR
    //====================================================================//
    
    /**
     * Convert any Kind of Value from a unit to another.
     *
     * @param float $value          Value to Convert
     * @param float $fromFactor     Source Unit Factor
     * @param float $toFactor       Target Unit Factor
     *
     * @return float
     */
    private static function convert($value, $fromFactor, $toFactor)
    {
        //====================================================================//
        // Convert Source to Base Unit then Apply Target Factor
//        if (extension_loaded("bcmath")) {
//            return (float) bcdiv( bcmul($value, $toFactor, 9), $fromFactor, 9);
//        }        
//        
        return (float) ($value * ($toFactor / $fromFactor));
    }
}
