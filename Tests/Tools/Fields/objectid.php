<?php

namespace Splash\Tests\Tools\Fields;

use Splash\Client\Splash;

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
        $list = explode ( IDSPLIT , $Data );
        if (is_array($list) && (count($list)==2) ) {
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
        // Get Object List
        $ObjectsList    =   Splash::Object($ObjectType)->ObjectsList();
        
        if( isset($ObjectsList["meta"]) ) {
            unset($ObjectsList["meta"]);
        }
        
        //====================================================================//
        // Select an Object in Given List
        $Item           = $ObjectsList[array_rand($ObjectsList,1)];
        
        if ( isset($Item["id"]) && !empty($Item["id"])) {
            //====================================================================//
            // Generate Object Id String
            return self::encodeIdField($Item["id"],$ObjectType);
        } 
        return Null;        
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
    
    //====================================================================//
    //  OBJECTID FIELDS MANAGEMENT
    //====================================================================//

    /**
     *      @abstract   Encode an Object Identifier Field
     * 
     *      @param      string       $ObjectId             Object Id
     *      @param      string       $ObjectType           Object Type Name
     * 
     *      @return     string
     */
    public static function encodeIdField($ObjectId,$ObjectType)
    {
        //====================================================================//
        // Safety Checks
        if (empty($ObjectType))             {   return Null;     }
        if (empty($ObjectId))               {   return Null;     }
        
        //====================================================================//
        // Create & Return Field Id Data String        
        return $ObjectId  . IDSPLIT . $ObjectType;
    }  
    
    /**
     *      @abstract   Retrieve Id form an Object Identifier Data
     *      @param      string      $ObjectId       OsWs Object Identifier. 
     *      @return     int         $Id             0 if KO or Object Identifier
     */
    public static function decodeIdField($ObjectId)
    {
        //====================================================================//
        // Checks if Given String is an Object Id String
        $Array = self::isIdField($ObjectId);
        
        //====================================================================//
        // Return Object Id
        if ($Array != False)                 
        {   
            return $Array["ObjectId"];     
        }
        
        return   False; 
    }      
    
    /**
     *      @abstract   Identify if field is Object Identifier Data & Decode Field
     * 
     *      @param      string       $In             Id Field String
     * 
     *      @return     array       $result         0 if KO or Exploded Field Array
     */
    public static function isIdField($In)
    {
        //====================================================================//
        // Safety Check 
        if (empty($In)) {
            return False;
        }       
        
        //====================================================================//
        // Detects ObjectId
        $list = explode ( IDSPLIT , $In );
        if (is_array($list) && (count($list)==2) ) {
            //====================================================================//
            // If List Detected, Prepare Field List Information Array
            $Out["ObjectId"]        = $list[0];
            $Out["ObjectType"]      = $list[1];
            return $Out;
        }
        return False;
    }     
    
}


