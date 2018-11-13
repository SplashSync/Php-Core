<?php

namespace Splash\Tests\Tools\Fields;

/**
 * @abstract    Bool Field : Basic Boolean
 */
class Oobool
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
        //      Verify Data is a Bool Type
        if (is_bool($data)) {
            return true;
        }

        //==============================================================================
        //      Verify Data is an Int as Bool
        if (($data === 0) || ($data === 1) || ($data === "0")|| ($data === "1")) {
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
     * @param   mixed   $source     Original Data Block
     * @param   mixed   $target     New Data Block
     *
     * @return  bool                TRUE if both Data Block Are Similar
     */
    public static function compare($source, $target)
    {
        //====================================================================//
        //  Raw text Compare
        return ($source == $target)?true:false;
    }
}
