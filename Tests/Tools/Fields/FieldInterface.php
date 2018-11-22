<?php

namespace Splash\Tests\Tools\Fields;

use ArrayObject;

/**
 * @abstract    Bool Field : Basic Boolean
 */
interface FieldInterface
{
    //==============================================================================
    //      DATA VALIDATION
    //==============================================================================

    /**
     * Verify given Raw Data is Valid
     *
     * @param  null|bool|int|string|Array|ArrayObject $data
     *
     * @return true|string
     */
    public static function validate($data);
    
    //==============================================================================
    //      FAKE DATA GENERATOR
    //==============================================================================

    /**
     * Generate Fake Raw Field Data for Debugger Simulations
     *
     * @param array   $settings   User Defined Faker Settings
     *
     * @return mixed
     */
    public static function fake($settings);
    
    /**
     * Compare Two Data Block to See if similar (Update Required)
     *
     * !important : Target Data is always validated before compare
     *
     * @param   mixed   $source     Original Data Block
     * @param   mixed   $target     New Data Block
     * @param   array   $settings   User Defined Faker Settings
     *
     * @return  bool
     */
    public static function compare($source, $target, $settings);
    
}
