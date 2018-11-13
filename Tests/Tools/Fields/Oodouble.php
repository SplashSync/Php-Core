<?php

namespace Splash\Tests\Tools\Fields;

/**
 * @abstract    Double Field : Float Value as Text
 */
class Oodouble
{
    //==============================================================================
    //      Structural Data
    //==============================================================================

    protected $FORMAT        =   'Double';
    
    //==============================================================================
    //      DATA VALIDATION
    //==============================================================================

    /**
     * Verify given Raw Data is Valid
     *
     * @param   string $data
     *
     * @return bool     True if OK, Error String if KO
     */
    public static function validate($data)
    {
        //==============================================================================
        //      Verify Data is a Double or Zero
        if (is_double($data) || ($data == 0)) {
            return true;
        //==============================================================================
        //      Verify Data is a Double as String
        } elseif (is_string($data) && (is_double(floatval($data)))) {
            return true;
        }
        return "Field Data is not Double or Float Value.";
    }
    
    //==============================================================================
    //      FAKE DATA GENERATOR
    //==============================================================================

    /**
     * Generate Fake Raw Field Data for Debugger Simulations
     *
     * @return mixed
     */
    public static function fake()
    {
        return (double) mt_rand(1, 1000) / 10;
    }
    
    //==============================================================================
    //      DATA COMPARATOR (OPTIONNAL)
    //==============================================================================
    
    /**
     * Compare Two Data Block to See if similar (Update Required)
     *
     * !important : Target Data is always validated before compare
     *
     * @param       mixed   $source     Original Data Block
     * @param       mixed   $target     New Data Block
     * @param       array   $settings   User Defined Faker Settings
     *
     * @return  bool                TRUE if both Data Block Are Similar
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
