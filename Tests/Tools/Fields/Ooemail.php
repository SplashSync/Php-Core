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
        $Name   = preg_replace('/[^A-Za-z\-]/', '', base64_encode(mt_rand()));
        //==============================================================================
        //      Apply Domain Constraint
        if (isset($Settings["emailDomain"]) && is_string($Settings["emailDomain"])) {
            $Domain     = $Settings["emailDomain"];
        } else {
            $Domain     = preg_replace('/[^A-Za-z\-]/', '', base64_encode(mt_rand(100, 1000)));
        }
        
        //==============================================================================
        //      Apply Extension Constraint
        if (isset($Settings["emailExtension"]) && is_string($Settings["emailExtension"])) {
            $Extension  = $Settings["emailExtension"];
        } else {
            $Extension  = preg_replace('/[^A-Za-z\-]/', '', base64_encode(mt_rand(10, 100)));
        }
                
        return $Name . "@" . $Domain . "." . $Extension;
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
