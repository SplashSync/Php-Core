<?php

namespace Splash\Tests\Tools;

use Splash\Client\Splash;
use Splash\Server\SplashServer;

if ( !defined("SPLASH_DEBUG") ) {
    define("SPLASH_DEBUG" , True);
} 

/**
 * @abstract    Splash Test Tools - Objects Test Case Base Class
 *
 * @author SplashSync <contact@splashsync.com>
 */
class ObjectsCase extends BaseCase {

    const       CLASS_PREFIX        =   'Splash\Tests\Tools\Fields\\';
    
    /**
     *      @abstract   Perform generic Server Side Action
     *  
     *      @return     mixed            
     */
    protected function GenericAction($Service, $Action, $Description, array $Parameters = array(True))   
    {
        
        //====================================================================//
        //   Prepare Request Data
        Splash::Ws()->AddTask( $Action, $Parameters , $Description );
        Splash::Ws()->Call_Init( $Service );
        Splash::Ws()->Call_AddTasks();
        
        //====================================================================//
        //   Encode Request Data
        $Request =  Splash::Ws()->Pack( Splash::Ws()->getOutputBuffer() );
        
        //====================================================================//
        //   Execute Action From Splash Server to Module  
        $Response   =   SplashServer::$Service(Splash::Configuration()->WsIdentifier, $Request);
        
        //====================================================================//
        //   Check Response 
        $Data       =   $this->CheckResponse( $Response ); 
//var_dump($Data);        
        
        //====================================================================//
        //   Extract Task Result 
        if (is_a($Data->tasks, "ArrayObject")) {
            $Task = array_shift($Data->tasks->getArrayCopy());
        } elseif (is_array($Data->tasks)) {
            $Task = array_shift($Data->tasks);
        }
//var_dump($Task);        

        return $Task["data"];
    }
    
    //==============================================================================
    //      VALIDATION FUNCTIONS
    //==============================================================================       

   /**
    *   @abstract   Verify this parameter is a valid sync data type  
    *   @param      string      $In         Data Type Name String     
    *   @return     int         $result     0 if KO, Field Full Class Name if OK
    */
    public static function isValidType($In) 
    {        
        //====================================================================//
        // Safety Check 
        if (empty($In)) {
            return False;
        }
        //====================================================================//
        // Detects Lists Fields
        //====================================================================//
        $list = self::isListField($In);
        if ( $list != False ) {
            $In = $list["fieldname"]; 
        }
        //====================================================================//
        // Detects Id Fields
        //====================================================================//
        $id = self::isIdField($In);
        if ( $id != False ) {
            $In = "objectid";
        }        
        
        //====================================================================//
        // Verify Single Data Type is Valid
        //====================================================================//

        //====================================================================//
        // Build Class Full Name        
        $ClassName = self::CLASS_PREFIX .  $In;
        
        //====================================================================//
        // Build New Entity 
        if (class_exists( $ClassName )) {
            return  $ClassName;
        }

        return false;
    }  

   /**
    *   @abstract   Check if this id is a list identifier & return decoded array if ok  
    *   @param      string  $In         Data Type Name String     
    *   @return     int     $result     0 if KO, 1 if OK
    */
    public static function isListField($In) 
    {        
        //====================================================================//
        // Safety Check 
        if (empty($In)) {
            return False;
        }
        //====================================================================//
        // Detects Lists
        $list = explode ( LISTSPLIT , $In );
        if (is_array($list) && (count($list)==2) ) {
            //====================================================================//
            // If List Detected, Prepare Field List Information Array
            return array("fieldname" => $list[0],"listname" => $list[1]);
        }
        return False;
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
    
    //====================================================================//
    //   Data Provider Functions  
    //====================================================================//
    
    public function ObjectTypesProvider()
    {
        $Result = array();
        
        foreach (Splash::Objects() as $ObjectType) {
            $Result[] = array($ObjectType);            
        }
        
        return $Result;
    }
    
    public function testDummy()
    {
        $this->assertTrue(True);
    }

    
}
