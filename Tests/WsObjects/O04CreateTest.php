<?php
namespace Splash\Tests\WsObjects;

use Splash\Tests\Tools\ObjectsCase;
use Splash\Client\Splash;

/**
 * @abstract    Objects Test Suite - Object create Verification Verifications
 *
 * @author SplashSync <contact@splashsync.com>
 */
class O04CreateTest extends ObjectsCase
{
    
    /**
     * @dataProvider ObjectTypesProvider
     */
    public function testFromModule($Sequence, $ObjectType)
    {
        $this->loadLocalTestSequence($Sequence);
        
        //====================================================================//
        //   Generate Dummy Object Data (Required Fields Only)
        $DummyData = $this->PrepareForTesting($ObjectType);
        if ($DummyData == false) {
            return true;
        }
        
        //====================================================================//
        //   Execute Action Directly on Module
        $ObjectId = Splash::Object($ObjectType)->Set(null, $DummyData);
        
        //====================================================================//
        //   Verify Response
        $this->VerifyResponse($ObjectType, $ObjectId);
    }

    /**
     * @dataProvider ObjectTypesProvider
     */
    public function testFromService($Sequence, $ObjectType)
    {
        $this->loadLocalTestSequence($Sequence);
        
        //====================================================================//
        //   Generate Dummy Object Data (Required Fields Only)
        $DummyData = $this->PrepareForTesting($ObjectType);
        if ($DummyData == false) {
            return true;
        }
        
        //====================================================================//
        //   Execute Action Directly on Module
        $ObjectId = $this->GenericAction(SPL_S_OBJECTS, SPL_F_SET, __METHOD__, [ "id" => null, "type" => $ObjectType, "fields" => $DummyData]);
        
        //====================================================================//
        //   Verify Response
        $this->VerifyResponse($ObjectType, $ObjectId);
    }
    
    public function VerifyTestIsAllowed($ObjectType)
    {
        $Definition = Splash::Object($ObjectType)->Description();
        
        //====================================================================//
        //   Verify Create is Allowed
        if ($Definition["allow_push_created"]) {
            return true;
        }
        $this->assertTrue(true, "Object Creation not Allowed, Test Skipped.");
        return false;
    }

    public function PrepareForTesting($ObjectType)
    {
        //====================================================================//
        //   Verify Test is Required
        if (!$this->VerifyTestIsAllowed($ObjectType)) {
            return false;
        }
        
        //====================================================================//
        // Read Required Fields & Prepare Dummy Data
        //====================================================================//
        $Write          = false;
        $Fields         = Splash::Object($ObjectType)->Fields();
        foreach ($Fields as $Key => $Field) {
            
            //====================================================================//
            // Skip Non Required Fields
            if (!$Field->required) {
                unset($Fields[$Key]);
            }
            //====================================================================//
            // Check if Write Fields
            if ($Field->write) {
                $Write = true;
            }
        }
        
        //====================================================================//
        // If No Writable Fields
        if (!$Write) {
            return false;
        }
        
        //====================================================================//
        // Lock New Objects To Avoid Action Commit
        Splash::Object($ObjectType)->Lock();
        
        //====================================================================//
        // Clean Objects Commited Array
        Splash::$Commited = array();
        
        return $this->fakeObjectData($Fields);
    }
    
    public function VerifyResponse($ObjectType, $ObjectId)
    {
        //====================================================================//
        //   Verify Object Id Is Not Empty
        $this->assertNotEmpty($ObjectId, "Returned New Object Id is Empty");

        //====================================================================//
        //   Add Object Id to Created List
        $this->AddTestedObject($ObjectType, $ObjectId);
    
        //====================================================================//
        //   Verify Object Id Is in Right Format
        $this->assertTrue(
                is_integer($ObjectId) || is_string($ObjectId),
                "New Object Id is not an Integer or a Strings"
        );
        
        //====================================================================//
        //   Verify Object Change Was Commited
        $this->assertIsLastCommited(SPL_A_CREATE, $ObjectType, $ObjectId);
    }
}
