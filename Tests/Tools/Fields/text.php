<?php

namespace Splash\Tests\Tools\Fields;

/**
 * @abstract    Text Field : Long Text Data Block
 */
class text
{

    //==============================================================================
    //      Structural Data  
    //==============================================================================

    protected $FORMAT        =   'Text';
    static    $IS_SCALAR     =   True;

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
        return preg_replace('/[^A-Za-z\-]/', '', base64_encode(mt_rand(1000,mt_getrandmax ()/10)));
    }
    
}
