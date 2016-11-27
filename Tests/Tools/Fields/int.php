<?php

namespace Splash\Tests\Tools\Fields;

/**
 * @abstract    Integer Field
 */
class int
{
    //==============================================================================
    //      Structural Data  
    //==============================================================================

    protected $FORMAT           =   'Int';
    static    $IS_SCALAR        =   True;
    
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
        //      Verify Data is Numeric 
        if ( !is_numeric($Data) ) {
            return "Field Data is not a Number.";
        }
        
        //==============================================================================
        //      Verify Data is an Integer 
        if ( !is_numeric($Data) || !is_integer($Data) ) {
            return "Field Data is not an Integer.";
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
        return mt_rand(1,1000);
    }
    
}
