<?php

namespace Splash\Tests\Tools\Fields;

/**
 * @abstract    Varchar Field : Basic text
 */
class oovarchar
{

    //==============================================================================
    //      Structural Data
    //==============================================================================

    protected $FORMAT           =   'Varchar';
    public static $IS_SCALAR        =   true;

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
        if (!empty($Data) && !is_string($Data)) {
            return "Field  Data is not a String.";
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
        //==============================================================================
        //      generate Random String
        $Data   =   preg_replace('/[^A-Za-z\-]/', '', base64_encode(mt_rand(900, mt_getrandmax())));
        $Data  .=   preg_replace('/[^A-Za-z\-]/', '', base64_encode(mt_rand(900, mt_getrandmax())));
        
        //==============================================================================
        //      Apply Min Length Constraint
        if (isset($Settings["minLength"]) && is_numeric($Settings["minLength"])) {
            while (strlen($Data) < $Settings["minLength"]) {
                $Data  .=   preg_replace('/[^A-Za-z\-]/', '', base64_encode(mt_rand(900, mt_getrandmax())));
            }
        }
        
        //==============================================================================
        //      Apply Max Length Constraint
        if (isset($Settings["maxLength"]) && is_numeric($Settings["maxLength"])) {
            $Data   =   substr($Data, 0, $Settings["maxLength"]);
        }
        
        return $Data;
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
        return ($Source === $Target)?true:false;
    }
}
