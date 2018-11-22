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
class Oomtext implements FieldInterface
{
    //==============================================================================
    //      Structural Data
    //==============================================================================

    protected $FORMAT        =   'MText';
    
    //==============================================================================
    //      DATA VALIDATION
    //==============================================================================

    /**
     * {@inheritdoc}
     */
    public static function validate($data)
    {
        return Oomvarchar::validate($data);
    }
    
    //==============================================================================
    //      FAKE DATA GENERATOR
    //==============================================================================

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public static function compare($source, $target, $settings)
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
