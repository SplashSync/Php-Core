<?php

namespace Splash\Tests\Tools\Fields;

/**
 * @abstract    Bool Field : Basic Boolean
 */
class oobool
{
    
    //==============================================================================
    //      Structural Data
    //==============================================================================

    protected $FORMAT        =   'Bool';

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
        //      Verify Data is a Bool Type
        if (is_bool($Data)) {
            return true;
        }

        //==============================================================================
        //      Verify Data is an Int as Bool
        if (($Data === 0) || ($Data === 1) || ($Data === "0")|| ($Data === "1")) {
            return true;
        }
        
        return "Field Data is not a Boolean.";
    }
    
    //==============================================================================
    //      FAKE DATA GENERATOR
    //==============================================================================

    /**
     * Generate Fake Raw Field Data for Debugger Simulations
     *
     * @return mixed
     */
    public static function fake()
    {
        return (mt_rand()%2)?true:false;
    }
    
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
        //  Raw text Compare
        return ($Source == $Target)?true:false;
    }
}
