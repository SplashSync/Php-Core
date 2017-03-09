<?php
namespace Splash\Tests\WsObjects;

use Splash\Tests\Tools\ObjectsCase;
use Splash\Client\Splash;
use ArrayObject;

/**
 * @abstract    Objects Test Suite - Objects List Reading Verifications
 *
 * @author SplashSync <contact@splashsync.com>
 */
class O03ListTest extends ObjectsCase {
    
    
    /**
     * @dataProvider ObjectTypesProvider
     */
    public function testFromModule($ObjectType)
    {
        //====================================================================//
        //   Execute Action Directly on Module  
        $Data = Splash::Object($ObjectType)->ObjectsList();
        //====================================================================//
        //   Module May Return an Array (ArrayObject created by WebService) 
        if (is_array($Data)) {
            $Data   =   new ArrayObject($Data);            
        } 
        //====================================================================//
        //   Verify Response
        $this->VerifyResponse($Data, $ObjectType);
        
    }
    
    /**
     * @dataProvider ObjectTypesProvider
     */
    public function testFromObjectsService($ObjectType)
    {
        //====================================================================//
        //   Execute Action From Splash Server to Module  
        $Data = $this->GenericAction(SPL_S_OBJECTS, SPL_F_LIST, __METHOD__, [ "id" => Null, "type" => $ObjectType]);
        
        //====================================================================//
        //   Verify Response
        $this->VerifyResponse($Data, $ObjectType);
        
    }

    public function testFromObjectsServiceErrors()
    {
        //====================================================================//
        //      Request definition without Sending ObjectType  
        $this->GenericErrorAction(SPL_S_OBJECTS, SPL_F_LIST, __METHOD__);
        
    }
    
    public function VerifyResponse($Data, $ObjectType)
    {
        //====================================================================//
        //   Verify Response
        $this->assertNotEmpty( $Data                        , "Objects List is Empty");
        $this->assertInstanceOf( "ArrayObject" , $Data      , "Objects List is Not an ArrayObject");
        
        $this->VerifyMetaInformations($Data, $ObjectType);
        $this->VerifyAvailableFields($Data, $ObjectType);
        
        
        
//        //====================================================================//
//        // CHECK ITEMS
//        foreach ($Data as $ObjectType) {
//            $this->assertNotEmpty( $ObjectType              , "Objects Type is Empty");
//            $this->assertInternalType("string", $ObjectType , "Objects Type is Not an String. (Given" . print_r($ObjectType , True) . ")");
//        }        
    }
    

    public function VerifyAvailableFields($Data, $ObjectType)
    {
        //====================================================================//
        // Verify Fields are Available
        $Fields = Splash::Object($ObjectType)->Fields();
        if ( is_null( $Fields ) ) {
            return False;
        }        

//        //====================================================================//
//        // Verify List Datas
//        $Object = reset($Data);
        
        //====================================================================//
        // Verify List Data Items
        foreach ( $Data as $Item ) {
            
            //====================================================================//
            // Verify Object Id field is available
            $this->assertArrayHasKey( "id",     $Item,          $ObjectType . " List => Object Identifier (id) is not defined in List.");
            $this->assertInternalType( "scalar" , $Item["id"],  $ObjectType . " List => Object Identifier (id) is not String convertible.");

             
            //====================================================================//
            // Verify all "inlist" fields are available
            foreach ($Fields as $Field) {
                if ( isset($Field['inlist']) && !empty($Field['inlist']) ) {
                    $this->assertArrayHasKey( $Field["id"],     $Item,      $ObjectType . " List => Object Field (" . $Field["name"]. ") is marked as 'inlist' but not found in List.");
                    $this->assertInternalType( "scalar" , $Item["id"],      $ObjectType . " List => Object Field (" . $Field["name"]. ") is not String convertible.");
                }
            }
            
            
        }
        
        return True;
    }

    public function VerifyMetaInformations($Data, $ObjectType)
    {
        //====================================================================//
        // Verify List Meta Are Available
        $this->assertArrayHasKey( "meta",   $Data,      $ObjectType . " List => Meta Informations are not defined");
        $Meta   =   $Data["meta"];
        
        //====================================================================//
        // Verify List Meta Format
        $this->assertArrayInternalType($Meta,   "current",  "numeric",       $ObjectType . " List => Current Object Count not an Integer");
        $this->assertArrayInternalType($Meta,   "total",    "numeric",       $ObjectType . " List => Total Object Count not an Integer");
        
        //====================================================================//
        // Verify List Meta Informations
        unset($Data["meta"]);
        $this->assertEquals(
                $Meta["current"],   
                count($Data),    
                $ObjectType . " List => Current Object Count is different from Given Meta['current'] count.");
    
    }

    

    
    
}
