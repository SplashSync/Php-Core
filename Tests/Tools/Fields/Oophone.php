<?php

namespace Splash\Tests\Tools\Fields;

/**
 * @abstract    Phone Field : Define a Contact Phone Number
 */
class Oophone extends Oovarchar implements FieldInterface
{
    //==============================================================================
    //      Structural Data
    //==============================================================================

    protected $FORMAT         =   'Phone';
    
    //==============================================================================
    //      DATA VALIDATION
    //==============================================================================

    /**
     * {@inheritdoc}
     */
    public static function validate($data)
    {
        //==============================================================================
        //      Verify Data is not Empty
        if (empty($data)) {
            return true;
        }

        //==============================================================================
        //      Verify Data is a String
        if (!is_string($data)) {
            return "Phone Number Field Data is not a String.";
        }
        
        //==============================================================================
        //      Verify Data is a Phone Number
        if (preg_match('/^[+0-9. ()-]*$/', $data)) {
            return true;
        }

        return "Field Data is not a Phone Number.";
    }
    
    //==============================================================================
    //      FAKE DATA GENERATOR
    //==============================================================================

    /**
     * {@inheritdoc}
     */
    public static function fake($settings)
    {
        return (string) preg_replace('/^[+0-9. ()-]*$/', '', (string) mt_rand(12345678, 123456789));
    }
}
