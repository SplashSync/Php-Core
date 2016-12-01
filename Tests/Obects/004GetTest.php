<?php

use Splash\Tests\Tools\ObjectsCase;
use Splash\Client\Splash;
//use ArrayObject;

/**
 * @abstract    Objects Test Suite - Object Reading Verifications
 *
 * @author SplashSync <contact@splashsync.com>
 */
class O04getTest extends ObjectsCase {
    

    /**
     * @var array
     */    
    private $ObjectList     = array(); 

    /**
     * @var array
     */    
    private $ObjectCount    = array(); 

    /**
     * @dataProvider ObjectFieldsProvider
     */
    public function testSingleFieldFromModule($ObjectType, $Field)
    {
        
        //====================================================================//
        //   Get next Available Object ID from Module  
        $ObjectId = $this->getNextObjectId($ObjectType);

        //====================================================================//
        //   Get Readable Object Fields List  
        $Fields = $this->reduceFieldList(Splash::Object($ObjectType)->Fields(), True, False);
        
        //====================================================================//
        //   Execute Action Directly on Module  
        $Data = Splash::Object($ObjectType)->Get($ObjectId, $Fields);
        
        //====================================================================//
        //   Module May Return an Array (ArrayObject created by WebService) 
        if (is_array($Data)) {
            $Data   =   new ArrayObject($Data);            
        } 
        
        //====================================================================//
        //   Verify Response
        $this->VerifyResponse($Data,array($Field),$ObjectId);
        
    }
    
    /**
     * @dataProvider ObjectTypesProvider
     */
    public function testAllFieldsFromModule($ObjectType)
    {
        
        //====================================================================//
        //   Get next Available Object ID from Module  
        $ObjectId = $this->getNextObjectId($ObjectType);

        //====================================================================//
        //   Get Readable Object Fields List  
        $Fields = $this->reduceFieldList(Splash::Object($ObjectType)->Fields(), True, False);
        
        //====================================================================//
        //   Execute Action Directly on Module  
        $Data = Splash::Object($ObjectType)->Get($ObjectId, $Fields);
        
        //====================================================================//
        //   Module May Return an Array (ArrayObject created by WebService) 
        if (is_array($Data)) {
            $Data   =   new ArrayObject($Data);            
        } 
        
        //====================================================================//
        //   Verify Response
        $this->VerifyResponse($Data,Splash::Object($ObjectType)->Fields(),$ObjectId);
        
    }
    
    /**
     * @dataProvider ObjectTypesProvider
     */
    public function testFromObjectsService($ObjectType)
    {
        //====================================================================//
        //   Get next Available Object ID from Module  
        $ObjectId = $this->getNextObjectId($ObjectType);
        
        //====================================================================//
        //   Get Readable Object Fields List  
        $Fields = $this->reduceFieldList(Splash::Object($ObjectType)->Fields(), True, False);
        
        //====================================================================//
        //   Execute Action From Splash Server to Module  
        $Data = $this->GenericAction(SPL_S_OBJECTS, SPL_F_GET, __METHOD__, [ "type" => $ObjectType, "id" => $ObjectId, "fields" => $Fields]);
        
        //====================================================================//
        //   Verify Response
        $this->VerifyResponse($Data,Splash::Object($ObjectType)->Fields(),$ObjectId);
        
    }

    /**
     * @dataProvider ObjectTypesProvider
     */
    public function testFromObjectsServiceErrors($ObjectType)
    {
        //====================================================================//
        //      Request definition without Sending ObjectType  
        $this->GenericErrorAction(SPL_S_OBJECTS, SPL_F_FIELDS, __METHOD__);
        //====================================================================//
        //      Request definition without Sending ObjectID  
        $this->GenericErrorAction(SPL_S_OBJECTS, SPL_F_FIELDS, __METHOD__, [ "type" => $ObjectType, "fields" => array()]);
        //====================================================================//
        //      Request definition but Sending NUll ObjectID  
        $this->GenericErrorAction(SPL_S_OBJECTS, SPL_F_FIELDS, __METHOD__, [ "type" => $ObjectType, "id" => Null, "fields" => array()]);
        $this->GenericErrorAction(SPL_S_OBJECTS, SPL_F_FIELDS, __METHOD__, [ "type" => $ObjectType, "id" => 0, "fields" => array()]);
    }

    public function getNextObjectId($ObjectType)
    {
        //====================================================================//
        //   If Object List Not Loaded  
        if ( !isset($this->ObjectList[$ObjectType]) ) {
            
            //====================================================================//
            //   Get Object List from Module  
            $List = Splash::Object($ObjectType)->ObjectsList();

            //====================================================================//
            //   Get Object Count
            $this->ObjectCount[$ObjectType] = $List["meta"]["current"];
            
            //====================================================================//
            //   Remove Meta Datats form Objects List
            unset($List["meta"]);
            
            //====================================================================//
            //   Convert ArrayObjects
            if (is_a($List, "ArrayObject")) {
                $this->ObjectList[$ObjectType] = $List->getArrayCopy();
            } else {
                $this->ObjectList[$ObjectType] = $List;                
            }
        }
        
        //====================================================================//
        //   Verify Objects List is Not Empty  
        if ( $this->ObjectCount[$ObjectType] <= 0 ) {
            return False;
        }
        
        //====================================================================//
        //   Return First Object of List
        $NextObject = array_shift($this->ObjectList[$ObjectType]);
        return $NextObject["id"];
    }   
    
    
    public function VerifyResponse($Data, $Fields, $ObjectId)
    {
        //====================================================================//
        //   Verify Response Block
        $this->assertNotEmpty( $Data                        , "Data Block is Empty");
        $this->assertInstanceOf( "ArrayObject" , $Data      , "Data Block is Not an ArrayObject");

        //====================================================================//
        //   Verify Object Id is Present
        $this->assertArrayHasKey("id",          $Data       , "Object Identifier ['id'] is not defined in returned Data Block.");
        $this->assertEquals( $Data["id"], $ObjectId         , "Object Identifier ['id'] is different in returned Data Block.");
        
        //====================================================================//
        //  Verify Field Data
        foreach ($Fields as $Field) {
            $this->isValidFieldData($Data, $Field->id, $Field->type);
        }        
    }
    
}
