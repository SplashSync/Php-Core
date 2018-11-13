<?php

namespace Splash\Tests\WsAdmin;

use Splash\Tests\Tools\AbstractBaseCase;

use Splash\Client\Splash;
use ArrayObject;

/**
 * @abstract    Admin Test Suite - Get Objects List Client Verifications
 *
 * @author SplashSync <contact@splashsync.com>
 */
class A04WidgetsTest extends AbstractBaseCase
{
    public function testObjectsFromClass()
    {
        //====================================================================//
        //   Execute Action From Module
        $data = Splash::widgets();
        //====================================================================//
        //   Module May Return an Array (ArrayObject created by WebService)
        if (is_array($data)) {
            $data   =   new ArrayObject($data);
        }
        //====================================================================//
        //   Verify Response
        $this->verifyResponse($data);
    }

    
    public function testWidgetsActionFromAdmin()
    {
        //====================================================================//
        //   Execute Action From Splash Server to Module
        $data = $this->genericAction(SPL_S_ADMIN, SPL_F_GET_WIDGETS, __METHOD__);
        //====================================================================//
        //   Verify Response
        $this->verifyResponse($data);
    }
    
    public function testWidgetsActionFromWidgets()
    {
        //====================================================================//
        //   Execute Action From Splash Server to Module
        $data = $this->genericAction(SPL_S_WIDGETS, SPL_F_WIDGET_LIST, __METHOD__);
        //====================================================================//
        //   Verify Response
        $this->verifyResponse($data);
    }
    
    public function verifyResponse($data)
    {
        //====================================================================//
        //   Verify Response
        $this->assertNotEmpty($data, "Widgets List is Empty");
        $this->assertInstanceOf("ArrayObject", $data, "Widgets List is Not an ArrayObject");
        //====================================================================//
        // CHECK ITEMS
        foreach ($data as $widgetType) {
            $this->assertNotEmpty($widgetType, "Widgets Type is Empty");
            $this->assertInternalType(
                "string",
                $widgetType,
                "Widgets Type is Not an String. (Given" . print_r($widgetType, true) . ")"
            );
        }
    }
}
