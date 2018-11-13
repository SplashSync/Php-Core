<?php

namespace Splash\Tests\Tools\Fields;

/**
 * @abstract    State Field : ISO State Code
 *
 * @example     US-CA : California
 *
 * @see         ISO 3166 Standard : www.iso.org/iso/country_codes
 */
class Oostate extends Oovarchar
{
    //==============================================================================
    //      Structural Data
    //==============================================================================

    const FORMAT        =   'State';
    
    //==============================================================================
    //      DATA VALIDATION
    //==============================================================================
    
    /**
     * Verify given Raw Data is Valid
     *
     * @param   string $data
     *
     * @return bool     True if OK, Error String if KO
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
        return (mt_rand()%2)?"CA":"FL";
    }
}
