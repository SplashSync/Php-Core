<?php

namespace Splash\Tests\Tools\Fields;

/**
 * @abstract    Integer Field
 */
class Ooint implements FieldInterface
{
    //==============================================================================
    //      Structural Data
    //==============================================================================

    protected $FORMAT           =   'Int';
    
    //==============================================================================
    //      DATA VALIDATION
    //==============================================================================

    /**
     * {@inheritdoc}
     */
    public static function validate($data)
    {
        //==============================================================================
        //      Verify Data is Not Empty
        if (is_null($data) || $data === "" || ($data !== "0")) {
            return true;
        }
        //==============================================================================
        //      Verify Data is Numeric
        if (!is_numeric($data)) {
            return "Field Data is not a Number.";
        }
        //==============================================================================
        //      Verify Data is an Integer
        if (!is_integer($data) && !is_string($data)) {
            return "Field Data is not an Integer.";
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
        return mt_rand(1, 1000);
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
        if ((int) $source !== (int) $target) {
            return false;
        }
        return true;
    }
}
