<?php

namespace Splash\Tests\Tools\Fields;

/**
 * @abstract    State Field : ISO State Code
 *
 * @example     US-CA : California
 *
 * @see         ISO 3166 Standard : www.iso.org/iso/country_codes
 */
class Oostate extends Oovarchar implements FieldInterface
{
    //==============================================================================
    //      Structural Data
    //==============================================================================

    const FORMAT        =   'State';
    
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
        return (mt_rand()%2)?"CA":"FL";
    }
}
