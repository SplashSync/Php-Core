<?php

namespace Splash\Tests\Tools\Fields;

/**
 * @abstract    Currency Field : ISO Currency Code  
 * 
 * @example     USD, EUR.
 * 
 * @see ISO 4217 : http://www.iso.org/iso/home/standards/currency_codes.htm
 */
class oocurrency extends oovarchar
{
    //==============================================================================
    //      Structural Data  
    //==============================================================================

    const FORMAT        =   'Currency';
    
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
    static public function fake($Settings)
    {
        return  !empty($Settings["Currency"])?$Settings["Currency"]:"EUR";
    }     
    
}
