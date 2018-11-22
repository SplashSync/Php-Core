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
     * @param   string $data
     *
     * @return true|string
     */
    public static function validate($data)
    {
        return Oomvarchar::validate($data);
    }
    
    //==============================================================================
    //      FAKE DATA GENERATOR
    //==============================================================================

    /**
     * Generate Fake Raw Field Data for Debugger Simulations
     *
     *  @param      array   $settings   User Defined Faker Settings
     *
     * @return mixed
     */
    public static function fake($settings)
    {
        $fake = array();
        foreach ($settings["Langs"] as $lang) {
            $fake[$lang] = Ootext::fake($settings);
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
     * @param   mixed   $source     Original Data Block
     * @param   mixed   $target     New Data Block
     *
     * @return  bool                TRUE if both Data Block Are Similar
     */
    public static function compare($source, $target)
    {
        //====================================================================//
        //  If Raw Text received, Not Array ==> Raw text Compare
        if (!is_array($source) && !is_a($target, "ArrayObject")
                && !is_array($target) && !is_a($target, "ArrayObject")) {
            return ($source === $target)?true:false;
        }
        //====================================================================//
        //  Verify Available Languages Count
        if (count($source) !== count($target)) {
            return false;
        }
        //====================================================================//
        //  Verify Each Languages Are Similar Strings
        foreach ($source as $key => $value) {
            if ($target[$key] !== $value) {
                return false;
            }
        }
        return true;
    }
}
