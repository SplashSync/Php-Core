<?php

namespace Splash\Tests\Tools\Fields;

/**
 * @abstract    Bool Field : Basic Boolean
 */
class Oobool implements FieldInterface
{
    
    //==============================================================================
    //      Structural Data
    //==============================================================================

    protected $FORMAT        =   'Bool';

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
        //      Verify Data is a Bool Type
        if (is_bool($data)) {
            return true;
        }

        //==============================================================================
        //      Verify Data is an Int as Bool
        if (is_scalar($data)) {
            if (($data === "0")|| ($data === "1")) {
                return true;
            }
        }
        if (is_int($data)) {
            if (($data === 0) || ($data === 1)) {
                return true;
            }
        }
        
        return "Field Data is not a Boolean.";
    }
    
    //==============================================================================
    //      FAKE DATA GENERATOR
    //==============================================================================

    /**
     * {@inheritdoc}
     */
    public static function fake($settings)
    {
        return (mt_rand()%2)?true:false;
    }
    
    /**
     * {@inheritdoc}
     */
    public static function compare($source, $target, $settings)
    {
        //====================================================================//
        //  Raw text Compare
        return ($source == $target)?true:false;
    }
}
