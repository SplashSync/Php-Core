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
        $data = Splash::objects();
        //====================================================================//
        //   Module May Return an Array (ArrayObject created by WebService)
        if (is_array($data)) {
            $data   =   new \ArrayObject($data);
        }
        //====================================================================//
        //   Verify Response
        $this->verifyResponse($data);
    }

    public function testObjectsFromAdminService()
    {
        //====================================================================//
        //   Execute Action From Splash Server to Module
        $data = $this->genericAction(SPL_S_ADMIN, SPL_F_GET_OBJECTS, __METHOD__);
        //====================================================================//
        //   Verify Response
        $this->verifyResponse($data);
    }
    
    public function testObjectsFromObjectsService()
    {
        //====================================================================//
        //   Execute Action From Splash Server to Module
        $data = $this->genericAction(SPL_S_OBJECTS, SPL_F_OBJECTS, __METHOD__);
        //====================================================================//
        //   Verify Response
        $this->verifyResponse($data);
    }
    
    public function verifyResponse($data)
    {
        //====================================================================//
        //   Verify Response
        $this->assertNotEmpty($data, "Objects List is Empty");
        $this->assertInstanceOf("ArrayObject", $data, "Objects List is Not an ArrayObject");
        //====================================================================//
        // CHECK ITEMS
        foreach ($data as $objectType) {
            $this->assertNotEmpty($objectType, "Objects Type is Empty");
            $this->assertInternalType(
                "string",
                $objectType,
                "Objects Type is Not an String. (Given" . print_r($objectType, true) . ")"
            );
        }
    }
}
