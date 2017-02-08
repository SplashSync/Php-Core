<?php

namespace Splash\Tests\Tools\Fields;

/**
 * @abstract    Integer Field
 */
class ooint
{
    //==============================================================================
    //      Structural Data  
    //==============================================================================

    protected $FORMAT           =   'Int';
    static    $IS_SCALAR        =   True;
    
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
    static public function validate($Data)
    {
        //==============================================================================
        //      Verify Data is Not Empty
        if ( is_null($Data) || $Data === "" || ($Data !== "0") ) {
            return True;
        }
        //==============================================================================
        //      Verify Data is Numeric 
        if ( !is_numeric($Data) ) {
            return "Field Data is not a Number.";
        }
        //==============================================================================
        //      Verify Data is an Integer 
        if ( !is_integer($Data) && !is_string($Data) ) {
            return "Field Data is not an Integer.";
        }
        return True;
    }
    
    //==============================================================================
    //      FAKE DATA GENERATOR  
    //==============================================================================   

    /**
     * Generate Fake Raw Field Data for Debugger Simulations
     *
     * @return mixed   
     */
    static public function fake()
    {
        return mt_rand(1,1000);
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
    public static function compare($Source,$Target) {
        //====================================================================//
        // Compare Float Values
        if ( (int) $Source !== (int) $Target ) {
            return False;
        }
        return True;
    }    
    
    
}
