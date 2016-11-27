<?php


namespace Splash\Tests\Tools\Fields;

/**
 * @abstract    Url Field : Full Link, generic URI
 * @see http://www.faqs.org/rfcs/rfc2396.html
 */
class url {

    //==============================================================================
    //      Structural Data  
    //==============================================================================

    protected   $FORMAT         =   'Url';
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
    static public function validate($Data) {
        //==============================================================================
        //      Verify Data is not Empty
        if (empty($Data)) {
            return True;
        }

        //==============================================================================
        //      Verify Data is a String
        if (!empty($Data) && !is_string($Data)) {
            return "Field  Data is not a String.";
        }

        //==============================================================================
        //      Verify Data is an Email Address
        if (!filter_var($Data, FILTER_VALIDATE_URL) !== False) {
            return "Field Data is not a Valid Url";
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
    static public function fake() {
        $domain = preg_replace('/[^A-Za-z\-]/', '', strtolower(base64_encode(mt_rand(100, 1000))));
        return "www." . $domain . ".splashsync.com";
    }

}
