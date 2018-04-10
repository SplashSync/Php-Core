<?php

namespace Splash\Tests\Tools\Fields;

/**
 * @abstract    Text Field : Long Text Data Block
 */
class Ootext
{

    //==============================================================================
    //      Structural Data
    //==============================================================================

    protected $FORMAT        =   'Text';
    public static $IS_SCALAR     =   true;

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
     * @return mixed
     */
    public static function fake($Settings)
    {
        return Oovarchar::fake($Settings);
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
