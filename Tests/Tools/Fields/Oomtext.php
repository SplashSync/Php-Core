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
class Oomtext
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
    public static function validate($Data)
    {
        return Oomvarchar::validate($Data);
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
    public static function fake($Settings)
    {
        $fake = array();
        foreach ($Settings["Langs"] as $lang) {
            $fake[$lang] = Ootext::fake($Settings);
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
    public static function compare($Source, $Target)
    {
        //====================================================================//
        //  If Raw Text received, Not Array ==> Raw text Compare
        if (!is_array($Source) && !is_a($Target, "ArrayObject")
                && !is_array($Target) && !is_a($Target, "ArrayObject")) {
            return ($Source === $Target)?true:false;
        }
        //====================================================================//
        //  Verify Available Languages Count
        if (count($Source) !== count($Target)) {
            return false;
        }
        //====================================================================//
        //  Verify Each Languages Are Similar Strings
        foreach ($Source as $key => $value) {
            if ($Target[$key] !== $value) {
                return false;
            }
        }
        return true;
    }
}
