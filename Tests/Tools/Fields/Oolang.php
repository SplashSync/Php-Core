<?php

namespace Splash\Tests\Tools\Fields;

/**
 * @abstract    Language Field : ISO Language Code
 *
 * @example     en_US, fr_FR, fr_BE
 *
 * @see         ISO 639-1 : http://www.iso.org/iso/language_codes
 */
class Oolang extends Oovarchar
{
    //==============================================================================
    //      Structural Data
    //==============================================================================

    const FORMAT        =   'Lang';
    
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
     * @return mixed
     */
    public static function fake($settings)
    {
        //==============================================================================
        //      Use Formater Settings
        if (!empty($settings["Langs"])) {
            //==============================================================================
            //      Select Random Language
            $index = rand(0, count($settings["Langs"]) -1);
            //==============================================================================
            //      Return Language Code
            return $settings["Langs"][$index];
        }
        
        //==============================================================================
        //      Return Language Code
        return (mt_rand()%2)?"en_US":"fr_FR";
    }
}
