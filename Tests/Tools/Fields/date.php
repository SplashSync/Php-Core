<?php

namespace Splash\Tests\Tools\Fields;

/**
 * @abstract    Date Field : Date as Text (Format Y-m-d)  
 * 
 * @example     2016-12-25
 */
class date
{
    //==============================================================================
    //      Structural Data  
    //==============================================================================

    protected   $FORMAT         =   'Date';
    static      $IS_SCALAR      =   True;
    
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
    static public function validate($Data)
    {
        //==============================================================================
        //      Verify Data is not Empty
        if ( empty($Data) ) {
            return True;
        }

        //==============================================================================
        //      Verify Data is a DateTime Type
        if ( \DateTime::createFromFormat(SPL_T_DATECAST, $Data) !== False ) {
            return True;
        }

        return "Field Data is not a Date with right Format (" . SPL_T_DATECAST . ").";
    }  
    
    //==============================================================================
    //      FAKE DATA GENERATOR  
    //==============================================================================   

    /**
     * Generate Fake Raw Field Data for Debugger Simulations
     *
     * @return mixed   
     */
    static public function fake()
    {
        //==============================================================================
        //      Generate a random DateTime
        $date = new \DateTime("now");
        $date->modify( '-' . mt_rand(1,10) . ' months' );
        //==============================================================================
        //      Return DateTime is Right Format
        return $date->format(SPL_T_DATECAST);
    }    
    
}
