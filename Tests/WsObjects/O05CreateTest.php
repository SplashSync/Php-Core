<?php
namespace Splash\Tests\WsObjects;

use Splash\Tests\Tools\ObjectsCase;
use Splash\Client\Splash;

/**
 * @abstract    Objects Test Suite - Object create Verification Verifications
 *
 * @author SplashSync <contact@splashsync.com>
 */
class O05CreateTest extends ObjectsCase {
    
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
        //   Execute Action Directly on Module  
        $ObjectId = Splash::Object($ObjectType)->Set(Null, $DummyData);
        
        //====================================================================//
        //   Verify Response
        $this->VerifyResponse($ObjectType,$ObjectId);
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
        //   Execute Action Directly on Module  
        $ObjectId = $this->GenericAction(SPL_S_OBJECTS, SPL_F_SET, __METHOD__, [ "id" => Null, "type" => $ObjectType, "fields" => $DummyData]);
        
        //====================================================================//
        //   Verify Response
        $this->VerifyResponse($ObjectType,$ObjectId);        
    }
    
    public function VerifyTestIsAllowed($ObjectType)
    {
        $Definition = Splash::Object($ObjectType)->Description();
        
        //====================================================================//
        //   Verify Create is Allowed
        if ( $Definition["allow_push_created"] ) {
            $this->assertTrue( True , "Object Creation not Allowed, Test Skipped.");
            return True;
        }    
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
        $DummyObject = [];
        $Write = False;
        foreach ( Splash::Object($ObjectType)->Fields() as $Field) {
            
            //====================================================================//
            // Skip Non Required Fields
            if ( !$Field->required ) {
                continue;
            }
            //====================================================================//
            // Check if Write Fields
            if ( $Field->write ) {   
                $Write = True;
            }            
            
            //====================================================================//
            // Generate Fields Dummy Data
            if ( self::isListField($Field->id) ) {
                $id     = self::isListField($Field->id);
                $type   = self::isListField($Field->type);
                $DummyObject[$id["fieldname"]][$id["listname"]] = $this->fakeFieldData($type["fieldname"]);    
            } else {
                $DummyObject[$Field->id] = $this->fakeFieldData($Field->type);    
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
        
        return $DummyObject;
        
    }
    
    public function VerifyResponse($ObjectType,$ObjectId)
    {
        //====================================================================//
        //   Verify Object Id Is Not Empty
        $this->assertNotEmpty( $ObjectId                    , "Returned New Object Id is Empty");

        //====================================================================//
        //   Verify Object Id Is in Right Format
        $this->assertTrue( 
                is_integer($ObjectId) || is_string($ObjectId), 
                "New Object Id is not an Integer or a Strings");
        
        //====================================================================//
        //   Verify Object Change Was Commited
        $this->assertIsLastCommited(SPL_A_CREATE,  $ObjectType , $ObjectId);
    }
    
}
