<?php

namespace Splash\Tests\Tools\Fields;

/**
 * @abstract    Integer Field
 */
class Ooint
{
    //==============================================================================
    //      Structural Data
    //==============================================================================

    protected $FORMAT           =   'Int';
    
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
        //      Verify Data is Not Empty
        if (is_null($data) || $data === "" || ($data !== "0")) {
            return true;
        }
        //==============================================================================
        //      Verify Data is Numeric
        if (!is_numeric($data)) {
            return "Field Data is not a Number.";
        }
        //==============================================================================
        //      Verify Data is an Integer
        if (!is_integer($data) && !is_string($data)) {
            return "Field Data is not an Integer.";
        }
        return true;
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
        return mt_rand(1, 1000);
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
        // Compare Float Values
        if ((int) $source !== (int) $target) {
            return false;
        }
        return true;
    }
}
