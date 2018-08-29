<?php

namespace Splash\Tests\WsAdmin;

use Splash\Tests\Tools\AbstractBaseCase;

use Splash\Client\Splash;
use Splash\Server\SplashServer;

/**
 * @abstract    Admin Test Suite - Ping Client Verifications
 *
 * @author SplashSync <contact@splashsync.com>
 */
class A01PingTest extends AbstractBaseCase
{
    public function testDummy()
    {
        $this->assertTrue(true);
    }

    protected function setUp()
    {
        //====================================================================//
        // BOOT or REBOOT MODULE
        Splash::reboot();
        //====================================================================//
        // Force Module to Use NuSOAP if Php SOAP Selected
        if (Splash::configuration()->WsMethod == "SOAP") {
            Splash::configuration()->WsMethod = "NuSOAP";
        }
        //====================================================================//
        // FAKE SPLASH SERVER HOST URL
        Splash::configuration()->WsHost = $this->getLocalServerSoapUrl();
        Splash::ws()->setup();
    }
    
    public function testPingClientAction()
    {
        if (!empty(Splash::input("SPLASH_TRAVIS"))) {
            //   Skip Test without Warnings
            $this->assertTrue(true);
            return;
//            //   Skip Test with Warnings
//            $this->markTestSkipped('No HTTP Calls in Client Mode');
        }

        //====================================================================//
        //   Execute Ping From Module to Splash Server
        $this->assertTrue(
            Splash::ping(),
            "Test of Splash Server Ping Fail. "
                . "Maybe this server is not connected? Check your configuration."
        );
        
        Splash::log()->cleanLog();
    }
    
    public function testPingServerAction()
    {
        
        //====================================================================//
        //   Execute Ping From Splash Server to Module
        $Response   =   SplashServer::ping();
        $Data       =   Splash::ws()->unPack($Response, 1);

        //====================================================================//
        //   Verify Response
        $this->assertNotEmpty($Response, "Ping Response Block is Empty");
        $this->assertNotEmpty($Data, "Ping Response Data is Empty");
        $this->assertInstanceOf("ArrayObject", $Data, "Ping Response Data is Not an ArrayObject");
        $this->assertArrayHasKey("result", $Data, "Ping Result is Missing");
        $this->assertNotEmpty($Data->result, "Ping Result is not True");
    }
}
