<?php


namespace Splash\Tests\Tools\Fields;

/**
 * @abstract    Url Field : Full Link, generic URI
 * @see http://www.faqs.org/rfcs/rfc2396.html
 */
class Oourl extends Oovarchar
{

    //==============================================================================
    //      Structural Data
    //==============================================================================

    protected $FORMAT         =   'Url';

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
        if (!empty($Data) && !is_string($Data)) {
            return "Field  Data is not a String.";
        }

        //==============================================================================
        //      Verify Data is a Valid URI
//        if (!filter_var($Data, FILTER_VALIDATE_URL) !== False) {
//            return "Field Data is not a Valid Url";
//        }

        return true;
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
        $Domain =   preg_replace('/[^A-Za-z\-]/', '', strtolower(base64_encode(mt_rand(100, 1000))));
        $Prefix =   !empty($Settings["Url_Prefix"]) ? $Settings["Url_Prefix"] : null;
        $Sufix  =   !empty($Settings["Url_Sufix"])   ? $Settings["Url_Sufix"] : ".splashsync.com";
        
        return $Prefix . $Domain . $Sufix;
    }
}
