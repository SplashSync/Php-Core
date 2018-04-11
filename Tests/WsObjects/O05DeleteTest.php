<?php
namespace Splash\Tests\WsObjects;

use Splash\Tests\Tools\ObjectsCase;
use Splash\Client\Splash;

/**
 * @abstract    Objects Test Suite - Fields List Verifications
 *
 * @author SplashSync <contact@splashsync.com>
 */
class O05DeleteTest extends ObjectsCase
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
        //   Create a New Object on Module
        $ObjectId = Splash::object($ObjectType)->set(null, $DummyData);
        
        //====================================================================//
        //   Verify Response
        $this->verifyCreateResponse($ObjectType, $ObjectId);

        //====================================================================//
        // Lock New Objects To Avoid Action Commit
        Splash::object($ObjectType)->lock($ObjectId);

        //====================================================================//
        //   Delete Object on Module
        $Data = Splash::object($ObjectType)->delete($ObjectId);
        
        //====================================================================//
        //   Verify Response
        $this->verifyDeleteResponse($ObjectType, $ObjectId, $Data);
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
        //   Create a New Object on Module
        $ObjectId = Splash::object($ObjectType)->set(null, $DummyData);
        
        //====================================================================//
        //   Verify Response
        $this->verifyCreateResponse($ObjectType, $ObjectId);
        
        //====================================================================//
        //   Execute Action Directly on Module
        $Data = $this->genericAction(SPL_S_OBJECTS, SPL_F_DEL, __METHOD__, [ "id" => $ObjectId, "type" => $ObjectType]);
        
        //====================================================================//
        //   Verify Response
        $this->verifyDeleteResponse($ObjectType, $ObjectId, $Data);
    }
    
    /**
     * @dataProvider objectTypesProvider
     */
    public function testFromObjectsServiceErrors($Sequence, $ObjectType)
    {
        $this->loadLocalTestSequence($Sequence);
        
        //====================================================================//
        //      Request definition without Sending Parameters
        $this->genericErrorAction(SPL_S_OBJECTS, SPL_F_GET, __METHOD__, []);
        //====================================================================//
        //      Request definition without Sending ObjectType
        $this->genericErrorAction(SPL_S_OBJECTS, SPL_F_GET, __METHOD__, [ "id" => null ]);
        //====================================================================//
        //      Request definition without Sending ObjectId
        $this->genericErrorAction(SPL_S_OBJECTS, SPL_F_GET, __METHOD__, [ "type" => $ObjectType]);
    }

    
    public function verifyTestIsAllowed($ObjectType)
    {
        $Definition = Splash::object($ObjectType)->description();

        $this->assertNotEmpty($Definition);
        //====================================================================//
        //   Verify Create is Allowed
        if (!$Definition["allow_push_created"]) {
            return false;
        }
        //====================================================================//
        //   Verify Delete is Allowed
        if (!$Definition["allow_push_deleted"]) {
            return false;
        }
        return true;
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

    public function verifyCreateResponse($ObjectType, $ObjectId)
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
    }
    
    
    public function verifyDeleteResponse($ObjectType, $ObjectId, $Data)
    {
        //====================================================================//
        //   Verify Response
        $this->assertIsSplashBool($Data, "Object Delete Response Must be a Bool");
        $this->assertNotEmpty($Data, "Object Delete Response is Not True");
        
        //====================================================================//
        //   Verify Repeating Delete as Same Result
        $RepeatedResponse    =   Splash::object($ObjectType)->delete($ObjectId);
        $this->assertTrue(
            $RepeatedResponse,
            "Object Repeated Delete, Must return True even if Object Already Deleted."
        );
        
        //====================================================================//
        //   Verify Object not Present anymore
        $Fields = $this->reduceFieldList(Splash::object($ObjectType)->fields(), true, false);
        $GetResponse    =   Splash::object($ObjectType)->get($ObjectId, $Fields);
        $this->assertFalse($GetResponse, "Object Not Delete, I can still read it!!");
    }
}
