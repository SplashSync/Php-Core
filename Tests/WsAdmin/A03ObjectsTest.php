<?php

namespace Splash\Tests\WsAdmin;

use Splash\Tests\Tools\AbstractBaseCase;

use Splash\Client\Splash;

/**
 * @abstract    Admin Test Suite - Get Objects List Client Verifications
 *
 * @author SplashSync <contact@splashsync.com>
 */
class A03ObjectsTest extends AbstractBaseCase
{
    public function testObjectsFromClass()
    {
        //====================================================================//
        //   Execute Action From Module
        $Data = Splash::objects();
        //====================================================================//
        //   Module May Return an Array (ArrayObject created by WebService)
        if (is_array($Data)) {
            $Data   =   new \ArrayObject($Data);
        }
        //====================================================================//
        //   Verify Response
        $this->verifyResponse($Data);
    }

    public function testObjectsFromAdminService()
    {
        //====================================================================//
        //   Execute Action From Splash Server to Module
        $Data = $this->genericAction(SPL_S_ADMIN, SPL_F_GET_OBJECTS, __METHOD__);
        
        //====================================================================//
        //   Verify Response
        $this->verifyResponse($Data);
    }
    
    public function testObjectsFromObjectsService()
    {
        //====================================================================//
        //   Execute Action From Splash Server to Module
        $Data = $this->genericAction(SPL_S_OBJECTS, SPL_F_OBJECTS, __METHOD__);
        
        //====================================================================//
        //   Verify Response
        $this->verifyResponse($Data);
    }
    
    public function verifyResponse($Data)
    {
        //====================================================================//
        //   Verify Response
        $this->assertNotEmpty($Data, "Objects List is Empty");
        $this->assertInstanceOf("ArrayObject", $Data, "Objects List is Not an ArrayObject");
        
        //====================================================================//
        // CHECK ITEMS
        foreach ($Data as $ObjectType) {
            $this->assertNotEmpty($ObjectType, "Objects Type is Empty");
            $this->assertInternalType(
                "string",
                $ObjectType,
                "Objects Type is Not an String. (Given" . print_r($ObjectType, true) . ")"
            );
        }
    }
}
