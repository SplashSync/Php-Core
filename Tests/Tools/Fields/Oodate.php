<?php

namespace Splash\Tests\Tools\Fields;

/**
 * @abstract    Date Field : Date as Text (Format Y-m-d)
 *
 * @example     2016-12-25
 */
class Oodate extends Oovarchar
{
    //==============================================================================
    //      Structural Data
    //==============================================================================

    protected $FORMAT           =   'Date';
    
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
        if (\DateTime::createFromFormat(SPL_T_DATECAST, $Data) !== false) {
            return true;
        }

        return "Field Data is not a Date with right Format (" . SPL_T_DATECAST . ").";
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
        $date->modify('-' . mt_rand(1, 24) . ' months' );
        $date->modify('-' . mt_rand(1, 30) . ' days' );
        //==============================================================================
        //      Return DateTime is Right Format
        return $date->format(SPL_T_DATECAST);
    }
}
