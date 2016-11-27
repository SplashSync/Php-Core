<?php

namespace Splash\Tests\Tools\Fields;

/**
 * @abstract    Double Field : Float Value as Text  
 */
class double
{
    //==============================================================================
    //      Structural Data  
    //==============================================================================

    protected $FORMAT        =   'Double';
    
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
        //      Verify Data is a Double or Zero 
        if (is_double($Data) || ($Data == 0) ) {
            return True;
        }
        //==============================================================================
        //      Verify Data is a Double as String 
        elseif (is_string($Data) && (is_double(floatval($Data))) ) {
            return True;
        }        
        return "Field Data is not Double or Float Value.";
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
        return (double) mt_rand(1,1000) / 10;
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
        if ( abs( $Source - $Target) > 1E-6 ) {
            return False;
        }
        return True;
    }    
    
 }
