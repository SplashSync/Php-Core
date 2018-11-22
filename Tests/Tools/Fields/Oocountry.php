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
     * @param   string $data
     *
     * @return true|string
     */
    public static function validate($data)
    {
        if (!empty($data) && !is_string($data)) {
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
     * @param      array   $settings   User Defined Faker Settings
     *
     * @return string
     */
    public static function fake($settings)
    {
        return (mt_rand()%2)?"FR":"US";
    }
}
