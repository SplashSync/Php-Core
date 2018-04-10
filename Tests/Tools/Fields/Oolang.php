<?php

namespace Splash\Tests\Tools\Fields;

/**
 * @abstract    Language Field : ISO Language Code
 *
 * @example     en_US, fr_FR, fr_BE
 *
 * @see         ISO 639-1 : http://www.iso.org/iso/language_codes
 */
class Oolang
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
     * @param   string $Data
     *
     * @return bool     True if OK, Error String if KO
     */
    public static function validate($Data)
    {
        if (!empty($Data) && !is_string($Data)) {
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
    public static function fake($Settings)
    {
        //==============================================================================
        //      Use Formater Settings
        if (!empty($Settings["Langs"])) {
            //==============================================================================
            //      Select Random Language
            $index = rand(0, count($Settings["Langs"]) -1);
            //==============================================================================
            //      Return Language Code
            return $Settings["Langs"][$index];
        }
        
        //==============================================================================
        //      Return Language Code
        return (mt_rand()%2)?"en_US":"fr_FR";
    }
}
