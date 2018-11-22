<?php

namespace Splash\Tests\Tools\Fields;

/**
 * @abstract    Varchar Field : Basic text
 */
class Oovarchar
{

    //==============================================================================
    //      Structural Data
    //==============================================================================

    protected $FORMAT           =   'Varchar';

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
            return "Field Data is not a String.";
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
     * @return string
     */
    public static function fake($settings)
    {
        //==============================================================================
        //      generate Random String
        $data   =   preg_replace('/[^A-Za-z\-]/', '', base64_encode(mt_rand(900, mt_getrandmax())));
        $data  .=   preg_replace('/[^A-Za-z\-]/', '', base64_encode(mt_rand(900, mt_getrandmax())));
        
        //==============================================================================
        //      Apply Constraints
        self::applyLengthConstrains($settings, $data);
        self::applyCaseConstrains($settings, $data);
       
        return $data;
    }
    
    /**
     * @abstract    Apply Case Constrains
     * @param   array       $settings   User Defined Faker Settings
     * @param   string      $data
     * @return  void
     */
    private static function applyLengthConstrains($settings, &$data)
    {
        //==============================================================================
        //      Apply Min Length Constraint
        if (isset($settings["minLength"]) && is_numeric($settings["minLength"])) {
            while (strlen($data) < $settings["minLength"]) {
                $data  .=   preg_replace('/[^A-Za-z\-]/', '', base64_encode(mt_rand(900, mt_getrandmax())));
            }
        }
        //==============================================================================
        //      Apply Max Length Constraint
        if (isset($settings["maxLength"]) && is_numeric($settings["maxLength"])) {
            $data   =   substr($data, 0, $settings["maxLength"]);
        }
    }

    /**
     * @abstract    Apply Case Constrains
     * @param   array       $settings   User Defined Faker Settings
     * @param   string      $data
     * @return  void
     */
    private static function applyCaseConstrains($settings, &$data)
    {
        //==============================================================================
        //      Apply Case Constraint
        if (isset($settings["isLowerCase"]) && !empty($settings["isLowerCase"])) {
            $data   = strtolower($data);
        }
        if (isset($settings["isUpperCase"]) && !empty($settings["isUpperCase"])) {
            $data   = strtoupper($data);
        }
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
