<?php

namespace Splash\Tests\Tools\Fields;

use ArrayObject;

/**
 * @abstract    DateTime Field : Date & Time as Text (Format Y-m-d G:i:s)
 *
 * @example     2016-12-25 12:25:30
 */
class Oodatetime extends Oovarchar implements FieldInterface
{
    //==============================================================================
    //      Structural Data
    //==============================================================================

    const FORMAT        =   'DateTime';
    
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
        //      Verify Data is a Scalar
        if (!is_scalar($data)) {
            return "Field Data is not a DateTime with right Format (" . SPL_T_DATETIMECAST . ").";
        }    
        //==============================================================================
        //      Verify Data is a DateTime Type
        if (\DateTime::createFromFormat(SPL_T_DATETIMECAST,(string) $data) !== false) {
            return true;
        }

        return "Field Data is not a DateTime with right Format (" . SPL_T_DATETIMECAST . ").";
    }
    
    //==============================================================================
    //      FAKE DATA GENERATOR
    //==============================================================================

    /**
     * {@inheritdoc}
     */
    public static function fake($settings)
    {
        //==============================================================================
        //      Generate a random DateTime
        $date = new \DateTime("now");
        $date->modify('-' . mt_rand(1, 10) . ' months');
        $date->modify('-' . mt_rand(1, 60) . ' minutes');
        $date->modify('-' . mt_rand(1, 60) . ' seconds');
        //==============================================================================
        //      Return DateTime is Right Format
        return $date->format(SPL_T_DATETIMECAST);
    }
}
