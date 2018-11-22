<?php

namespace Splash\Tests\Tools\Fields;

/**
 * @abstract    Date Field : Date as Text (Format Y-m-d)
 *
 * @example     2016-12-25
 */
class Oodate extends Oovarchar implements FieldInterface
{
    //==============================================================================
    //      Structural Data
    //==============================================================================

    protected $FORMAT           =   'Date';
    
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
            return "Field Data is not a Date with right Format (" . SPL_T_DATECAST . ").";
        }
        //==============================================================================
        //      Verify Data is a DateTime Type
        if (\DateTime::createFromFormat(SPL_T_DATECAST, (string) $data) !== false) {
            return true;
        }

        return "Field Data is not a Date with right Format (" . SPL_T_DATECAST . ").";
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
        $date->modify('-' . mt_rand(1, 24) . ' months');
        $date->modify('-' . mt_rand(1, 30) . ' days');
        //==============================================================================
        //      Return DateTime is Right Format
        return $date->format(SPL_T_DATECAST);
    }
}
