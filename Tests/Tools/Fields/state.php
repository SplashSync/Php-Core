<?php

namespace Splash\Tests\Tools\Fields;

/**
 * @abstract    State Field : ISO State Code 
 * 
 * @example     US-CA : California
 * 
 * @see         ISO 3166 Standard : www.iso.org/iso/country_codes
 */
class state
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
     * @param   string $Data
     * 
     * @return bool     True if OK, Error String if KO
     */
    static public function validate($Data)
    {
        if ( !empty($Data) && !is_string($Data) ) {
            return "Field  Data is not a String.";
        }
        
        return True;
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
        return (mt_rand()%2)?"CA":"FL";
    }    
    
}
