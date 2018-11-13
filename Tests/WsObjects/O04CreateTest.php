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
        //   Execute Action Directly on Module
        $objectId = Splash::object($objectType)->set(null, $dummyData);
        
        //====================================================================//
        //   Verify Response
        $this->verifyResponse($objectType, $objectId);
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
        //   Execute Action Directly on Module
        $objectId = $this->genericAction(
            SPL_S_OBJECTS,
            SPL_F_SET,
            __METHOD__,
            [ "id" => null, "type" => $objectType, "fields" => $dummyData]
        );
        
        //====================================================================//
        //   Verify Response
        $this->verifyResponse($objectType, $objectId);
    }
    
    public function verifyTestIsAllowed($objectType)
    {
        $definition = Splash::object($objectType)->description();
        
        //====================================================================//
        //   Verify Create is Allowed
        if ($definition["allow_push_created"]) {
            return true;
        }
        $this->assertTrue(true, "Object Creation not Allowed, Test Skipped.");
        return false;
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
    
    public function verifyResponse($objectType, $objectId)
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
        
        //====================================================================//
        //   Verify Object Change Was Commited
        $this->assertIsLastCommited(SPL_A_CREATE, $objectType, $objectId);
    }
}
