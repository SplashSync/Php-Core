<?php

namespace Splash\Tests\Tools\Fields;

/**
 * @abstract    Multilangual Text Field : Multilangual Short Text Array
 *
//====================================================================//
// Sample :
// $data["name"]["iso_code"]            =>      Value
// Where name is field name and code is a valid SPL_T_LANG Iso Language Code
//====================================================================//
 *
 */
class Oomvarchar
{
    //==============================================================================
    //      Structural Data
    //==============================================================================

    protected $FORMAT        =   'MVarchar';
    
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
        //      Verify Data is Not Empty
        if (is_null($data) || $data === "") {
            return true;
        }
        //==============================================================================
        //      Verify Data is an Array
        if (!is_array($data) && !is_a($data, "ArrayObject")) {
            return "Field Data is not an Array.";
        }

        //==============================================================================
        //      Verify each Ligne is a String
        foreach ($data as $key => $value) {
            if (!self::validateIsMultilangData($key, $value)) {
                return self::validateIsMultilangData($key, $value);
            }
        }
        
        return true;
    }
    
    private static function validateIsMultilangData($key, $value)
    {
        if (empty($key) || !is_string($key)) {
            return "Multi-Language Key must be a non empty String.";
        }
        if (!empty($value) && !is_string($value) && !is_numeric($value)) {
            return "Multi-Language Data is not a String.";
        }
        return true;
    }
    
    //==============================================================================
    //      FAKE DATA GENERATOR
    //==============================================================================

    /**
     * Generate Fake Raw Field Data for Debugger Simulations
     *
     * @param      array   $settings   User Defined Faker Settings
     *
     * @return mixed
     */
    public static function fake($settings)
    {
        $fake = array();
        foreach ($settings["Langs"] as $lang) {
            $fake[$lang] = Oovarchar::fake($settings);
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
