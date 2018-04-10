<?php

namespace Splash\Tests\Tools\Fields;

/**
 * @abstract    Contry Field : ISO Country Code (ISO 3166-1 alpha-2)
 *
 * @example     US, FR, DE
 *
 * @see         ISO 3166 : http://www.iso.org/iso/home/standards/country_codes.htm
 */
class Oocountry extends Oovarchar
{
    //==============================================================================
    //      Structural Data
    //==============================================================================

    protected $FORMAT        =   'Country';
    
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
        return (mt_rand()%2)?"FR":"US";
    }
}
