<?php
namespace Splash\Tests\WsObjects;

use Splash\Tests\Tools\ObjectsCase;
use Splash\Client\Splash;
use ArrayObject;

/**
 * @abstract    Objects Test Suite - Object Description Verifications
 *
 * @author SplashSync <contact@splashsync.com>
 */
class O01DescriptionTest extends ObjectsCase
{
    
    /**
     * @dataProvider objectTypesProvider
     */
    public function testFromModule($Sequence, $ObjectType)
    {
        $this->loadLocalTestSequence($Sequence);
        //====================================================================//
        //   Execute Action Directly on Module
        $Data = Splash::object($ObjectType)->description();
        //====================================================================//
        //   Module May Return an Array (ArrayObject created by WebService)
        if (is_array($Data)) {
            $Data   =   new ArrayObject($Data);
        }
        //====================================================================//
        //   Verify Response
        $this->verifyResponse($Data);
    }
    
    /**
     * @dataProvider objectTypesProvider
     */
    public function testFromObjectsService($Sequence, $ObjectType)
    {
        $this->loadLocalTestSequence($Sequence);
        //====================================================================//
        //   Execute Action From Splash Server to Module
        $Data = $this->genericAction(SPL_S_OBJECTS, SPL_F_DESC, __METHOD__, [ "id" => null, "type" => $ObjectType]);
        
        //====================================================================//
        //   Verify Response
        $this->verifyResponse($Data);
    }

    public function testFromObjectsServiceErrors()
    {
        //====================================================================//
        //      Request definition without Sending ObjectType
        $this->genericErrorAction(SPL_S_OBJECTS, SPL_F_DESC, __METHOD__);
    }
    
    public function verifyResponse($Data)
    {
        //====================================================================//
        //   Verify Response
        $this->assertNotEmpty($Data, "Object Description is Empty");
        $this->assertInstanceOf("ArrayObject", $Data, "Object Description is Not an ArrayObject");
        
        //====================================================================//
        // All Informations are Available and is right format
        //====================================================================//
        
        //====================================================================//
        // General Object definition
        $this->assertNotEmpty($Data["type"], "Object Type is Empty");
        $this->assertInternalType("string", $Data["type"], "Object Type is Not a String.");
        $this->assertNotEmpty($Data["name"], "Object Name is Empty");
        $this->assertInternalType("string", $Data["name"], "Object Name is Not a String.");
        $this->assertNotEmpty($Data["description"], "Object Description is Empty");
        $this->assertInternalType("string", $Data["description"], "Object Description is Not a String.");
        $this->assertIsSplashBool($Data["disabled"], "Object Disabled Flag is Not a Bool.");

        //====================================================================//
        // Object Limitations
        $this->assertIsSplashBool($Data["allow_push_created"], "Allow Push Created Flag is Not a Bool.");
        $this->assertIsSplashBool($Data["allow_push_updated"], "Allow Push Updated Flag is Not a Bool.");
        $this->assertIsSplashBool($Data["allow_push_deleted"], "Allow Push Deleted Flag is Not a Bool.");
        
        //====================================================================//
        // Object Default Configuration
        $this->assertIsSplashBool($Data["enable_push_created"], "Enable Push Created Flag is Not a Bool.");
        $this->assertIsSplashBool($Data["enable_push_updated"], "Enable Push Updated Flag is Not a Bool.");
        $this->assertIsSplashBool($Data["enable_push_deleted"], "Enable Push Deleted Flag is Not a Bool.");
        $this->assertIsSplashBool($Data["enable_pull_created"], "Enable Pull Created Flag is Not a Bool.");
        $this->assertIsSplashBool($Data["enable_pull_updated"], "Enable Pull Updated Flag is Not a Bool.");
        $this->assertIsSplashBool($Data["enable_pull_deleted"], "Enable Pull Deleted Flag is Not a Bool.");
    }
}
