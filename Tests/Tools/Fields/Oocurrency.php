<?php

namespace Splash\Tests\Tools\Fields;

/**
 * @abstract    Currency Field : ISO Currency Code
 *
 * @example     USD, EUR.
 *
 * @see ISO 4217 : http://www.iso.org/iso/home/standards/currency_codes.htm
 */
class Oocurrency extends Oovarchar
{
    //==============================================================================
    //      Structural Data
    //==============================================================================

    const FORMAT        =   'Currency';
    
    public static $FakeData   =   array("EUR", "USD", "INR");
    
    //==============================================================================
    //      DATA VALIDATION
    //==============================================================================

    /**
     * Verify given Raw Data is Valid
     *
     * @param   string $Data
     *
     * @return bool     True if OK, Error String if KO
     */
    public static function validate($Data)
    {
        if (!empty($Data) && !is_string($Data)) {
            return "Field  Data is not a String.";
        }
        return true;
    }
    
    //==============================================================================
    //      FAKE DATA GENERATOR
    //==============================================================================

    /**
     * Generate Fake Raw Field Data for Debugger Simulations
     *
     * @param      array   $Settings   User Defined Faker Settings
     *
     * @return string
     */
    public static function fake($Settings)
    {
        return static::$FakeData[ (mt_rand(0, count(static::$FakeData) - 1)) ];
    }
}
