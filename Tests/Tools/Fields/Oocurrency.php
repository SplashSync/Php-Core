<?php

namespace Splash\Tests\Tools\Fields;

/**
 * @abstract    Currency Field : ISO Currency Code
 *
 * @example     USD, EUR.
 *
 * @see ISO 4217 : http://www.iso.org/iso/home/standards/currency_codes.htm
 */
class Oocurrency extends Oovarchar implements FieldInterface
{
    //==============================================================================
    //      Structural Data
    //==============================================================================

    const FORMAT        =   'Currency';
    
    public static $fakeData   =   array("EUR", "USD", "INR");
    
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
        return static::$fakeData[ (mt_rand(0, count(static::$fakeData) - 1)) ];
    }
}
