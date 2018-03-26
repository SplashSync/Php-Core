<?php
namespace Splash\Tests\Tools\Fields;

/**
 * @abstract    Multilangual Text Field : Multilangual Long Text Array 
 * 
//====================================================================//
// Sample :
// $data["name"]["iso_code"]            =>      Value    
// Where name is field name and code is a valid SPL_T_LANG Iso Language Code         
//====================================================================//
 * 
 */
class oomtext
{
    //==============================================================================
    //      Structural Data  
    //==============================================================================

    protected $FORMAT        =   'MText';
    
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
        //      Verify Data is an Array 
        if ( !is_array($Data) && !is_a( $Data , "ArrayObject") ) {
            return "Field Data is not an Array.";
        }

        //==============================================================================
        //      Verify each Ligne is a String 
        foreach ($Data as $key => $value) {
            
            if ( empty($key) || !is_string($key) ) {
                return "Multi-Language Key must be a non empty String.";
            }            
            
            if ( !empty($value) && !is_string($value) && !is_numeric($value) ) {
                return "Multi-Language Data is not a String.";
            }    
            
        }
        
        return True;
    }
    
    //==============================================================================
    //      FAKE DATA GENERATOR  
    //==============================================================================   

    /**
     * Generate Fake Raw Field Data for Debugger Simulations
     *
     *  @param      array   $Settings   User Defined Faker Settings
     * 
     * @return mixed   
     */
    static public function fake($Settings)
    {
        $fake = array();
        foreach($Settings["Langs"] as $lang) {
            $fake[$lang] = ootext::fake($Settings);
        }
        return $fake;
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
        //  If Raw Text received, Not Array ==> Raw text Compare
        if ( !is_array($Source) && !is_a($Target,"ArrayObject") && !is_array($Target) && !is_a($Target,"ArrayObject") )  {
            return ( $Source === $Target )?True:False;
        } 
        //====================================================================//
        //  Verify Available Languages Count
        if ( count($Source) !== count($Target) ) {
            return False;
        } 
        //====================================================================//
        //  Verify Each Languages Are Similar Strings
        foreach ($Source as $key => $value) {
            if ( $Target[$key] !== $value ) {
                return False;
            }            
        }
        return True;
    }
    
}
