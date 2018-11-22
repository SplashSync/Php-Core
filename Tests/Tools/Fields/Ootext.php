<?php

namespace Splash\Tests\Tools\Fields;

/**
 * @abstract    Text Field : Long Text Data Block
 */
class Ootext implements FieldInterface
{

    //==============================================================================
    //      Structural Data
    //==============================================================================

    protected $FORMAT        =   'Text';

    //==============================================================================
    //      DATA VALIDATION
    //==============================================================================

    /**
     * {@inheritdoc}
     */
    public static function validate($data)
    {
        if (!empty($data) && !is_string($data)) {
            return "Field  Data is not a String.";
        }
        
        return true;
    }
    
    //==============================================================================
    //      FAKE DATA GENERATOR
    //==============================================================================

    /**
     * {@inheritdoc}
     */
    public static function fake($settings)
    {
        return Oovarchar::fake($settings);
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
        //  Both Texts Are Empty
        if (empty($source) && empty($target)) {
            return true;
        }
        //====================================================================//
        //  Raw text Compare
        return ($source === $target)?true:false;
    }
}
