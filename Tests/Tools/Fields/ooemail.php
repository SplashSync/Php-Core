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

    protected $FORMAT         =   'Email';
    public static $IS_SCALAR      =   true;
    
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
        //      Verify Data is an Email Address
        if (!filter_var($Data, FILTER_VALIDATE_EMAIL) !== false) {
            return "Field Data is not an Email Address";
        }
        
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
        $name   = preg_replace('/[^A-Za-z\-]/', '', base64_encode(mt_rand()));
        $domain = preg_replace('/[^A-Za-z\-]/', '', base64_encode(mt_rand(100, 1000)));
//        $ext    = preg_replace('/[^A-Za-z\-]/', '', str_pad( base64_encode(mt_rand(100,1000)) , 3) );
        $ext    = preg_replace('/[^A-Za-z\-]/', '', base64_encode(mt_rand(10, 100)));
        return $name . "@" . $domain . "." . $ext;
    }
     
    //==============================================================================
    //      DATA COMPARATOR (OPTIONNAL)
    //==============================================================================
    
    /**
     * Compare Two Data Block to See if similar (Update Required)
     *
     * !important : Target Data is always validated before compare
     *
     * @param   mixed   $Source     Original Data Block
     * @param   mixed   $Target     New Data Block
     *
     * @return  bool                TRUE if both Data Block Are Similar
     */
    public static function compare($Source, $Target)
    {
        //====================================================================//
        //  Both Texts Are Empty
        if (empty($Source) && empty($Target)) {
            return true;
        }
        //====================================================================//
        //  Raw text Compare
        return (strtolower($Source) === strtolower($Target))?true:false;
    }
}
