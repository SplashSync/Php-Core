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
        if ( !empty($Data) && !is_string($Data) ) {
            return "Field  Data is not a String.";
        }
        return True;
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
    static public function fake($Settings)
    {
        return preg_replace('/[^A-Za-z\-]/', '', base64_encode(mt_rand(900,mt_getrandmax ()/10)));
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
        //  Both Texts Are Empty
        if ( empty($Source) && empty($Target) ) {
            return True;
        } 
        //====================================================================//
        //  Raw text Compare
        return ( $Source === $Target )?True:False;
    }
    
    
}
