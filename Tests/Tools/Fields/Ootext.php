<?php

namespace Splash\Tests\Tools\Fields;

/**
 * @abstract    Text Field : Long Text Data Block
 */
class Ootext
{

    //==============================================================================
    //      Structural Data
    //==============================================================================

    protected $FORMAT        =   'Text';

    //==============================================================================
    //      DATA VALIDATION
    //==============================================================================

    /**
     * Verify given Raw Data is Valid
     *
     * @param   string $data
     *
     * @return true|string
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
     * Generate Fake Raw Field Data for Debugger Simulations
     *
     * @param      array   $settings   User Defined Faker Settings
     *
     * @return mixed
     */
    public static function fake($settings)
    {
        return Oovarchar::fake($settings);
    }
    
    //==============================================================================
    //      DATA COMPARATOR (OPTIONNAL)
    //==============================================================================
    
    /**
     * Compare Two Data Block to See if similar (Update Required)
     *
     * !important : Target Data is always validated before compare
     *
     * @param   mixed   $source     Original Data Block
     * @param   mixed   $target     New Data Block
     *
     * @return  bool                TRUE if both Data Block Are Similar
     */
    public static function compare($source, $target)
    {
        //====================================================================//
        //  Both Texts Are Empty
        if (empty($source) && empty($target)) {
            return true;
        }
        //====================================================================//
        //  Raw text Compare
        return ($source === $target)?true:false;
    }
}
