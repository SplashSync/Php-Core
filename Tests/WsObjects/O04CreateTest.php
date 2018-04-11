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
     * @dataProvider objectTypesProvider
     */
    public function testFromModule($Sequence, $ObjectType)
    {
        $this->loadLocalTestSequence($Sequence);
        
        //====================================================================//
        //   Generate Dummy Object Data (Required Fields Only)
        $DummyData = $this->prepareForTesting($ObjectType);
        if ($DummyData == false) {
            return true;
        }
        
        //====================================================================//
        //   Execute Action Directly on Module
        $ObjectId = Splash::object($ObjectType)->set(null, $DummyData);
        
        //====================================================================//
        //   Verify Response
        $this->verifyResponse($ObjectType, $ObjectId);
    }

    /**
     * @dataProvider objectTypesProvider
     */
    public function testFromService($Sequence, $ObjectType)
    {
        $this->loadLocalTestSequence($Sequence);
        
        //====================================================================//
        //   Generate Dummy Object Data (Required Fields Only)
        $DummyData = $this->prepareForTesting($ObjectType);
        if ($DummyData == false) {
            return true;
        }
        
        //====================================================================//
        //   Execute Action Directly on Module
        $ObjectId = $this->genericAction(
            SPL_S_OBJECTS,
            SPL_F_SET,
            __METHOD__,
            [ "id" => null, "type" => $ObjectType, "fields" => $DummyData]
        );
        
        //====================================================================//
        //   Verify Response
        $this->verifyResponse($ObjectType, $ObjectId);
    }
    
    public function verifyTestIsAllowed($ObjectType)
    {
        $Definition = Splash::object($ObjectType)->description();
        
        //====================================================================//
        //   Verify Create is Allowed
        if ($Definition["allow_push_created"]) {
            return true;
        }
        $this->assertTrue(true, "Object Creation not Allowed, Test Skipped.");
        return false;
    }

    public function prepareForTesting($ObjectType)
    {
        //====================================================================//
        //   Verify Test is Required
        if (!$this->verifyTestIsAllowed($ObjectType)) {
            return false;
        }
        
        //====================================================================//
        // Read Required Fields & Prepare Dummy Data
        //====================================================================//
        $Write          = false;
        $Fields         = Splash::object($ObjectType)->fields();
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
        Splash::object($ObjectType)->lock();
        
        //====================================================================//
        // Clean Objects Commited Array
        Splash::$Commited = array();
        
        return $this->fakeObjectData($Fields);
    }
    
    public function verifyResponse($ObjectType, $ObjectId)
    {
        //====================================================================//
        //   Verify Object Id Is Not Empty
        $this->assertNotEmpty($ObjectId, "Returned New Object Id is Empty");

        //====================================================================//
        //   Add Object Id to Created List
        $this->addTestedObject($ObjectType, $ObjectId);
    
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
