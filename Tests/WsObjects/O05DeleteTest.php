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
    public function testFromModule($testSequence, $objectType)
    {
        $this->loadLocalTestSequence($testSequence);
        
        //====================================================================//
        //   Generate Dummy Object Data (Required Fields Only)
        $dummyData = $this->prepareForTesting($objectType);
        if ($dummyData == false) {
            return true;
        }
        
        //====================================================================//
        //   Create a New Object on Module
        $objectId = Splash::object($objectType)->set(null, $dummyData);
        
        //====================================================================//
        //   Verify Response
        $this->verifyCreateResponse($objectType, $objectId);

        //====================================================================//
        // Lock New Objects To Avoid Action Commit
        Splash::object($objectType)->lock($objectId);

        //====================================================================//
        //   Delete Object on Module
        $data = Splash::object($objectType)->delete($objectId);
        
        //====================================================================//
        //   Verify Response
        $this->verifyDeleteResponse($objectType, $objectId, $data);
    }

    /**
     * @dataProvider objectTypesProvider
     */
    public function testFromService($testSequence, $objectType)
    {
        $this->loadLocalTestSequence($testSequence);
        
        //====================================================================//
        //   Generate Dummy Object Data (Required Fields Only)
        $dummyData = $this->prepareForTesting($objectType);
        if ($dummyData == false) {
            return true;
        }
        
        //====================================================================//
        //   Create a New Object on Module
        $objectId = Splash::object($objectType)->set(null, $dummyData);
        
        //====================================================================//
        //   Verify Response
        $this->verifyCreateResponse($objectType, $objectId);
        
        //====================================================================//
        //   Execute Action Directly on Module
        $data = $this->genericAction(SPL_S_OBJECTS, SPL_F_DEL, __METHOD__, [ "id" => $objectId, "type" => $objectType]);
        
        //====================================================================//
        //   Verify Response
        $this->verifyDeleteResponse($objectType, $objectId, $data);
    }
    
    /**
     * @dataProvider objectTypesProvider
     */
    public function testFromObjectsServiceErrors($testSequence, $objectType)
    {
        $this->loadLocalTestSequence($testSequence);
        
        //====================================================================//
        //      Request definition without Sending Parameters
        $this->genericErrorAction(SPL_S_OBJECTS, SPL_F_GET, __METHOD__, []);
        //====================================================================//
        //      Request definition without Sending ObjectType
        $this->genericErrorAction(SPL_S_OBJECTS, SPL_F_GET, __METHOD__, [ "id" => null ]);
        //====================================================================//
        //      Request definition without Sending ObjectId
        $this->genericErrorAction(SPL_S_OBJECTS, SPL_F_GET, __METHOD__, [ "type" => $objectType]);
    }

    
    public function verifyTestIsAllowed($objectType)
    {
        $definition = Splash::object($objectType)->description();

        $this->assertNotEmpty($definition);
        //====================================================================//
        //   Verify Create is Allowed
        if (!$definition["allow_push_created"]) {
            return false;
        }
        //====================================================================//
        //   Verify Delete is Allowed
        if (!$definition["allow_push_deleted"]) {
            return false;
        }
        return true;
    }

    
    public function prepareForTesting($objectType)
    {
        //====================================================================//
        //   Verify Test is Required
        if (!$this->verifyTestIsAllowed($objectType)) {
            return false;
        }
        
        //====================================================================//
        // Read Required Fields & Prepare Dummy Data
        //====================================================================//
        $write          = false;
        $fields         = Splash::object($objectType)->fields();
        foreach ($fields as $key => $field) {
            //====================================================================//
            // Skip Non Required Fields
            if (!$field->required) {
                unset($fields[$key]);
            }
            //====================================================================//
            // Check if Write Fields
            if ($field->write) {
                $write = true;
            }
        }
        
        //====================================================================//
        // If No Writable Fields
        if (!$write) {
            return false;
        }
        
        //====================================================================//
        // Lock New Objects To Avoid Action Commit
        Splash::object($objectType)->lock();
        
        //====================================================================//
        // Clean Objects Commited Array
        Splash::$commited = array();
        
        return $this->fakeObjectData($fields);
    }

    public function verifyCreateResponse($objectType, $objectId)
    {
        //====================================================================//
        //   Verify Object Id Is Not Empty
        $this->assertNotEmpty($objectId, "Returned New Object Id is Empty");

        //====================================================================//
        //   Add Object Id to Created List
        $this->addTestedObject($objectType, $objectId);
        
        //====================================================================//
        //   Verify Object Id Is in Right Format
        $this->assertTrue(
            is_integer($objectId) || is_string($objectId),
            "New Object Id is not an Integer or a Strings"
        );
    }
    
    
    public function verifyDeleteResponse($objectType, $objectId, $data)
    {
        //====================================================================//
        //   Verify Response
        $this->assertIsSplashBool($data, "Object Delete Response Must be a Bool");
        $this->assertNotEmpty($data, "Object Delete Response is Not True");
        
        //====================================================================//
        //   Verify Repeating Delete as Same Result
        $repeatedResponse    =   Splash::object($objectType)->delete($objectId);
        $this->assertTrue(
            $repeatedResponse,
            "Object Repeated Delete, Must return True even if Object Already Deleted."
        );
        
        //====================================================================//
        //   Verify Object not Present anymore
        $fields         =   $this->reduceFieldList(Splash::object($objectType)->fields(), true, false);
        $getResponse    =   Splash::object($objectType)->get($objectId, $fields);
        $this->assertFalse($getResponse, "Object Not Delete, I can still read it!!");
    }
}
