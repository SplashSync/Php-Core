<?php

namespace Splash\Tests\Tools\Fields;

/**
 * @abstract    Email Field : Standard Email Address
 */
class Ooemail extends Oovarchar
{
    //==============================================================================
    //      Structural Data
    //==============================================================================

    protected $FORMAT         =   'Email';
    
    //==============================================================================
    //      DATA VALIDATION
    //==============================================================================

    /**
     * Verify given Raw Data is Valid
     *
     * @param   string $data
     *
     * @return true|string
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
        //      Verify Data is an Email Address
        if (!filter_var($data, FILTER_VALIDATE_EMAIL) !== false) {
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
     * @param      array   $settings   User Defined Faker Settings
     *
     * @return string
     */
    public static function fake($settings)
    {
        $name   = preg_replace('/[^A-Za-z\-]/', '', base64_encode(mt_rand()));
        //==============================================================================
        //      Apply Domain Constraint
        if (isset($settings["emailDomain"]) && is_string($settings["emailDomain"])) {
            $domain     = $settings["emailDomain"];
        } else {
            $domain     = preg_replace('/[^A-Za-z\-]/', '', base64_encode(mt_rand(100, 1000)));
        }
        
        //==============================================================================
        //      Apply Extension Constraint
        if (isset($settings["emailExtension"]) && is_string($settings["emailExtension"])) {
            $extension  = $settings["emailExtension"];
        } else {
            $extension  = preg_replace('/[^A-Za-z\-]/', '', base64_encode(mt_rand(10, 100)));
        }
                
        return $name . "@" . $domain . "." . $extension;
    }
     
    //==============================================================================
    //      DATA COMPARATOR (OPTIONNAL)
    //==============================================================================
    
    /**
     * Compare Two Data Block to See if similar (Update Required)
     *
     * !important : Target Data is always validated before compare
     *
     * @param   mixed   $source     Original Data Block
     * @param   mixed   $target     New Data Block
     *
     * @return  bool                TRUE if both Data Block Are Similar
     */
    public static function compare($source, $target)
    {
        //====================================================================//
        //  Both Texts Are Empty
        if (empty($source) && empty($target)) {
            return true;
        }
        //====================================================================//
        //  Raw text Compare
        return (strtolower($source) === strtolower($target))?true:false;
    }
}
