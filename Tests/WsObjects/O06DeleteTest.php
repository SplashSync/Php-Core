<?php
namespace Splash\Tests\WsObjects;

use Splash\Tests\Tools\ObjectsCase;
use Splash\Client\Splash;

/**
 * @abstract    Objects Test Suite - Fields List Verifications
 *
 * @author SplashSync <contact@splashsync.com>
 */
class O06DeleteTest extends ObjectsCase {
    
    
    /**
     * @dataProvider ObjectTypesProvider
     */
    public function testFromModule($ObjectType)
    {
        //====================================================================//
        //   Generate Dummy Object Data (Required Fields Only)   
        $DummyData = $this->PrepareForTesting($ObjectType);
        if ( $DummyData == False ) {
            return True;
        }
        
        //====================================================================//
        //   Create a New Object on Module  
        $ObjectId = Splash::Object($ObjectType)->Set(Null, $DummyData);
        
        //====================================================================//
        //   Verify Response
        $this->VerifyCreateResponse($ObjectId);

        //====================================================================//
        // Lock New Objects To Avoid Action Commit 
        Splash::Object($ObjectType)->Lock($ObjectId);

        //====================================================================//
        //   Delete Object on Module  
        $Data = Splash::Object($ObjectType)->Delete($ObjectId);
        
        //====================================================================//
        //   Verify Response
        $this->VerifyDeleteResponse($ObjectType, $ObjectId, $Data);
        
    }

    /**
     * @dataProvider ObjectTypesProvider
     */
    public function testFromService($ObjectType)
    {
        //====================================================================//
        //   Generate Dummy Object Data (Required Fields Only)   
        $DummyData = $this->PrepareForTesting($ObjectType);
        if ( $DummyData == False ) {
            return True;
        }
        
        //====================================================================//
        //   Create a New Object on Module  
        $ObjectId = Splash::Object($ObjectType)->Set(Null, $DummyData);
        
        //====================================================================//
        //   Verify Response
        $this->VerifyCreateResponse($ObjectId);
        
        //====================================================================//
        //   Execute Action Directly on Module  
        $Data = $this->GenericAction(SPL_S_OBJECTS, SPL_F_DEL, __METHOD__, [ "id" => $ObjectId, "type" => $ObjectType]);
        
        //====================================================================//
        //   Verify Response
        $this->VerifyDeleteResponse($ObjectType, $ObjectId, $Data);
        
    }
    
    /**
     * @dataProvider ObjectTypesProvider
     */
    public function testFromObjectsServiceErrors($ObjectType)
    {
        //====================================================================//
        //      Request definition without Sending Parameters  
        $this->GenericErrorAction(SPL_S_OBJECTS, SPL_F_GET, __METHOD__, []);
        //====================================================================//
        //      Request definition without Sending ObjectType  
        $this->GenericErrorAction(SPL_S_OBJECTS, SPL_F_GET, __METHOD__, [ "id" => Null ]);
        //====================================================================//
        //      Request definition without Sending ObjectId  
        $this->GenericErrorAction(SPL_S_OBJECTS, SPL_F_GET, __METHOD__, [ "type" => $ObjectType]);
        
    }

    
    public function VerifyTestIsAllowed($ObjectType)
    {
        $Definition = Splash::Object($ObjectType)->Description();

        $this->assertNotEmpty($Definition);
        //====================================================================//
        //   Verify Create is Allowed
        if ( !$Definition["allow_push_created"] ) {
            return False;
        }    
        //====================================================================//
        //   Verify Delete is Allowed
        if ( !$Definition["allow_push_deleted"] ) {
            return False;
        }    
        return True;
    }

    
    public function PrepareForTesting($ObjectType)
    {
        //====================================================================//
        //   Verify Test is Required   
        if ( !$this->VerifyTestIsAllowed($ObjectType) ) {
            return False;
        }
        
        //====================================================================//
        // Read Required Fields & Prepare Dummy Data
        //====================================================================//
        $Write          = False;
        $Fields         = Splash::Object($ObjectType)->Fields();
        foreach ( $Fields as $Key => $Field) {
            
            //====================================================================//
            // Skip Non Required Fields
            if ( !$Field->required ) {
                unset( $Fields[$Key] );
            }
            //====================================================================//
            // Check if Write Fields
            if ( $Field->write ) {   
                $Write = True;
            }            
        }
        
        //====================================================================//
        // If No Writable Fields 
        if ( !$Write ) {
            return False;
        } 
        
        //====================================================================//
        // Lock New Objects To Avoid Action Commit 
        Splash::Object($ObjectType)->Lock();
        
        //====================================================================//
        // Clean Objects Commited Array 
        Splash::$Commited = Array();
        
        return $this->fakeObjectData($Fields);
    }

    public function VerifyCreateResponse($ObjectId)
    {
        //====================================================================//
        //   Verify Object Id Is Not Empty
        $this->assertNotEmpty( $ObjectId                    , "Returned New Object Id is Empty");

        //====================================================================//
        //   Verify Object Id Is in Right Format
        $this->assertTrue( 
                is_integer($ObjectId) || is_string($ObjectId), 
                "New Object Id is not an Integer or a Strings");
        
    }
    
    
    public function VerifyDeleteResponse($ObjectType,$ObjectId,$Data)
    {
        //====================================================================//
        //   Verify Response
        $this->assertIsSplashBool( $Data                    , "Object Delete Response Must be a Bool");
        $this->assertNotEmpty( $Data                        , "Object Delete Response is Not True");
        
        //====================================================================//
        //   Verify Repeating Delete as Same Result
        $RepeatedResponse    =   Splash::Object($ObjectType)->Delete($ObjectId);
        $this->assertTrue( $RepeatedResponse                , "Object Repeated Delete, Must return True even if Object Already Deleted.");
        
        //====================================================================//
        //   Verify Object not Present anymore
        $Fields = $this->reduceFieldList(Splash::Object($ObjectType)->Fields(), True, False);
        $GetResponse    =   Splash::Object($ObjectType)->Get($ObjectId, $Fields );
        $this->assertFalse( $GetResponse                    , "Object Not Delete, I can still read it!!");
        
    }
    
}
