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
     * @param   string $data
     *
     * @return bool     True if OK, Error String if KO
     */
    public static function validate($data)
    {
        //==============================================================================
        //      Verify Data is not Empty
        if (empty($data)) {
            return true;
        }

        //==============================================================================
        //      Verify Data is a String
        if (!empty($data) && !is_string($data)) {
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
     * @param      array   $settings   User Defined Faker Settings
     *
     * @return string
     */
    public static function fake($settings)
    {
        $domain =   preg_replace('/[^A-Za-z\-]/', '', strtolower(base64_encode(mt_rand(100, 1000))));
        $prefix =   !empty($settings["Url_Prefix"]) ? $settings["Url_Prefix"] : null;
        $sufix  =   !empty($settings["Url_Sufix"])   ? $settings["Url_Sufix"] : ".splashsync.com";
        
        return $prefix . $domain . $sufix;
    }
}
