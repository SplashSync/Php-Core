<?php

namespace Splash\Tests\Tools\Fields;

/**
 * @abstract    Phone Field : Define a Contact Phone Number
 */
class phone
{
    //==============================================================================
    //      Structural Data  
    //==============================================================================

    protected   $FORMAT         =   'Phone';
    static      $IS_SCALAR      =   True;
    
    
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
    static public function validate($Data)
    {
        //==============================================================================
        //      Verify Data is not Empty
        if ( empty($Data) ) {
            return True;
        }

        //==============================================================================
        //      Verify Data is a String
        if ( !is_string($Data) ) {
            return "Phone Number Field Data is not a String.";
        }
        
        //==============================================================================
        //      Verify Data is a Phone Number
        if ( preg_match('/^[+0-9. ()-]*$/', $Data) ) {
            return True;
        }

        return "Field Data is not a Phone Number.";
    }   
    
    //==============================================================================
    //      FAKE DATA GENERATOR  
    //==============================================================================   

    /**
     * Generate Fake Raw Field Data for Debugger Simulations
     *
     * @return mixed   
     */
    static public function fake()
    {
        return preg_replace('/^[+0-9. ()-]*$/', '', mt_rand(12345678,123456789));
    }    
}
