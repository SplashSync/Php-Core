<?php

namespace Splash\Tests\Tools\Fields;

/**
 * @abstract    Object ID Field : price definition Array 
 */
class objectid
{
    //==============================================================================
    //      Structural Data  
    //==============================================================================

    protected $FORMAT        =   'ObjectId';
    
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
        //      Verify Data is Not Empty 
        if (is_null($Data) || empty($Data) || ($Data === "0") ) {    
            return True;
        }
        
        //==============================================================================
        //      Verify Data is a string
        if ( !empty($Data) && !is_string($Data) ) {
            return "Field  Data is not a String.";
        }
        
        //==============================================================================
        //      Verify Data is an Id Field 
        if (self::isIdField($Data) ) {    
            return True;
        }
        
        return "Field Data is not an Object Id String.";
    }
    
    //==============================================================================
    //      FAKE DATA GENERATOR  
    //==============================================================================   

    /**
     * Generate Fake Raw Field Data for Debugger Simulations
     *
     * @param      string  $ObjectType      Pointed Object Type Name
     * @param      array   $Settings        User Defined Faker Settings
     * 
     * @return mixed   
     */
    static public function fake($ObjectType, $Settings)
    {
        //====================================================================//
        // Verify Enough Parameters to Generate an Object Id
        if ( empty($ObjectType) 
                || !array_key_exists($ObjectType,$Settings["Objects"]) 
                || empty($Settings["Objects"][$ObjectType]) ) {
            return Null;
        } 
        $ObjectsList    =   $Settings["Objects"][$ObjectType];
        
        //====================================================================//
        // Select an Object in Given List
        $index          = (int) mt_rand(0,count($ObjectsList) - 1);
        
        //====================================================================//
        // Generate Object Id String
        return self::encodeIdField($ObjectsList[$index],$ObjectType);
    }     
    
    //==============================================================================
    //      DATA COMPARATOR (OPTIONNAL)  
    //==============================================================================   
    
    /**
     * Compare Two Data Block to See if similar (Update Required)
     * 
     * !important : Target Data is always validated before compare
     * 
     * @param   mixed   $Source     Original Data Block
     * @param   mixed   $Target     New Data Block
     *
     * @return  bool                TRUE if both Data Block Are Similar
     */
    public static function compare($Source,$Target) {
//dump($Source);
//dump($Target);
        //====================================================================//
        // Both Objects Ids Are Empty
        if ( empty($Source) && empty($Target) ) {
            return True;
        }
        //====================================================================//
        // Both Objects Ids Are Similar
        if ( $Source ==  $Target ) {
            return True;
        }
        return False;
    }     
    
}


