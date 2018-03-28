<?php

namespace Splash\Tests\Tools\Fields;

/**
 * @abstract    DateTime Field : Date & Time as Text (Format Y-m-d G:i:s)
 *
 * @example     2016-12-25 12:25:30
 */
class oodatetime extends oovarchar
{
    //==============================================================================
    //      Structural Data
    //==============================================================================

    const FORMAT        =   'DateTime';
    
    //==============================================================================
    //      DATA VALIDATION
    //==============================================================================

    /**
     * Verify given Raw Data is Valid
     *
     * @param   string $Data
     *
     * @return bool     True if OK, Error String if KO
     */
    public static function validate($Data)
    {
        //==============================================================================
        //      Verify Data is not Empty
        if (empty($Data)) {
            return true;
        }

        //==============================================================================
        //      Verify Data is a DateTime Type
        if (\DateTime::createFromFormat(SPL_T_DATETIMECAST, $Data) !== false) {
            return true;
        }

        return "Field Data is not a DateTime with right Format (" . SPL_T_DATETIMECAST . ").";
    }
    
    //==============================================================================
    //      FAKE DATA GENERATOR
    //==============================================================================

    /**
     * Generate Fake Raw Field Data for Debugger Simulations
     *
     * @param      array   $Settings   User Defined Faker Settings
     *
     * @return string
     */
    public static function fake($Settings)
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
