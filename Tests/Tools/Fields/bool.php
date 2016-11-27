<?php

namespace Splash\Tests\Tools\Fields;

/**
 * @abstract    Bool Field : Basic Boolean
 */
class bool
{
    
    //==============================================================================
    //      Structural Data  
    //==============================================================================

    protected $FORMAT        =   'Bool';

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
        //      Verify Data is a Bool Type
        if (is_bool($Data) ) {
            return True;
        }

        //==============================================================================
        //      Verify Data is an Int as Bool
        if ( ( $Data === 0 ) || ( $Data === 1 ) || ( $Data === "0" )|| ( $Data === "1" ) ) {
            return True;
        }
        
        return "Field Data is not a Boolean.";
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
        return (mt_rand()%2)?True:False;
    }
    
}
