<?php

namespace Splash\Tests\Tools\Fields;

/**
 * @abstract    Email Field : Standard Email Address
 */
class ooemail extends oovarchar
{
    //==============================================================================
    //      Structural Data  
    //==============================================================================

    protected   $FORMAT         =   'Email';
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
        if ( !empty($Data) && !is_string($Data) ) {
            return "Field  Data is not a String.";
        }

        //==============================================================================
        //      Verify Data is an Email Address
        if (!filter_var($Data, FILTER_VALIDATE_EMAIL) !== False)   {   
            return "Field Data is not an Email Address";
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
        $name   = preg_replace('/[^A-Za-z\-]/', '', base64_encode(mt_rand()));
        $domain = preg_replace('/[^A-Za-z\-]/', '', base64_encode(mt_rand(100,1000)));
//        $ext    = preg_replace('/[^A-Za-z\-]/', '', str_pad( base64_encode(mt_rand(100,1000)) , 3) );
        $ext    = preg_replace('/[^A-Za-z\-]/', '', base64_encode(mt_rand(10,100)));
        return $name . "@" . $domain . "." . $ext;        
    }    
     
}
