<?php

namespace Splash\Tests\Tools\Fields;

use ArrayObject;

/**
 * @abstract    Double Field : Float Value as Text
 */
class Oodouble implements FieldInterface
{
    //==============================================================================
    //      Structural Data
    //==============================================================================

    protected $FORMAT        =   'Double';
    
    //==============================================================================
    //      DATA VALIDATION
    //==============================================================================

    /**
     * {@inheritdoc}
     */
    public static function validate($data)
    {
        //==============================================================================
        //      Verify Data is an Array
        if (is_array($data) || ($data instanceof ArrayObject)) {
            return "Field Data is not Double or Float Value.";
        }
        //==============================================================================
        //      Verify Data is a Double or Zero
        if (is_double($data) || ($data == 0)) {
            return true;
        }
        //==============================================================================
        //      Verify Data is a Double as String
        if (is_string($data) && (is_double(floatval($data)))) {
            return true;
        }
        return "Field Data is not Double or Float Value.";
    }
    
    //==============================================================================
    //      FAKE DATA GENERATOR
    //==============================================================================

    /**
     * {@inheritdoc}
     */
    public static function fake($settings)
    {
        return (double) mt_rand(1, 1000) / 10;
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
        // Compare Float Values
        if (abs(round($source, $settings["DoublesPrecision"]) - round($target, $settings["DoublesPrecision"])) > 1E-6) {
            return false;
        }
        return true;
    }
}
