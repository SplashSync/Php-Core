<?php

namespace Splash\Tests\Tools\Fields;

/**
 * @abstract    Phone Field : Define a Contact Phone Number
 */
class Oophone extends Oovarchar
{
    //==============================================================================
    //      Structural Data
    //==============================================================================

    protected $FORMAT         =   'Phone';
    public static $IS_SCALAR      =   true;
    
    
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
        //==============================================================================
        //      Verify Data is not Empty
        if (empty($Data)) {
            return true;
        }

        //==============================================================================
        //      Verify Data is a String
        if (!is_string($Data)) {
            return "Phone Number Field Data is not a String.";
        }
        
        //==============================================================================
        //      Verify Data is a Phone Number
        if (preg_match('/^[+0-9. ()-]*$/', $Data)) {
            return true;
        }

        return "Field Data is not a Phone Number.";
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
        return preg_replace('/^[+0-9. ()-]*$/', '', mt_rand(12345678, 123456789));
    }
}
