<?php

namespace Splash\Tests\WsAdmin;

use Splash\Tests\Tools\AbstractBaseCase;

use Splash\Client\Splash;
use Splash\Server\SplashServer;

/**
 * @abstract    Admin Test Suite - Connect Client Verifications
 *
 * @author SplashSync <contact@splashsync.com>
 */
class A02ConnectTest extends AbstractBaseCase
{
    protected function setUp()
    {
        //====================================================================//
        // BOOT or REBOOT MODULE
        Splash::reboot();
        //====================================================================//
        // FAKE SPLASH SERVER HOST URL
        Splash::configuration()->WsHost = $this->getLocalServerSoapUrl();
        Splash::ws()->setup();
    }
    
    public function testConnectClientAction()
    {
        if (!empty(Splash::input("SPLASH_TRAVIS"))) {
            //   Skip Test without Warnings
            $this->assertTrue(true);
            return;
        }
        //====================================================================//
        //   Execute Connect From Module to Splash Server
        $this->assertTrue(
            Splash::connect(),
            "Test of Splash Server Connect Fail. "
                . "Maybe this server is not connected? Check your configuration."
        );
        Splash::log()->cleanLog();
    }
    
    public function testConnectServerAction()
    {
        //====================================================================//
        //   Prepare Request Data
        $request    =   Splash::ws()->pack(array(true));
        //====================================================================//
        //   Execute Connect From Splash Server to Module
        $response   =   SplashServer::connect(Splash::configuration()->WsIdentifier, $request);
        $data       = $this->checkResponse($response);
        //====================================================================//
        //   Verify Response
        $this->assertNotEmpty($data->result, "Connect Result is not True");
        
        //====================================================================//
        //   SAFETY CHECK
        //====================================================================//
        
        //====================================================================//
        //   Execute Connect with No Server Id
        $noId       =   SplashServer::connect(null, $request);
        $this->assertEmpty($noId, "Connection with No Server Id MUST be rejected => Empty Response");

        //====================================================================//
        //   Execute Connect with Wrong Server Id
        $wrongId    =   SplashServer::connect(rand(1E6, 1E10), $request);
        $this->assertEmpty($wrongId, "Connection with Wrong Server Id MUST be rejected => Empty Response");
        
        //====================================================================//
        //   Turn On Output Buffering Again
        ob_start();
    }
    
    public function testConnectServerWrongDataAction()
    {
        //====================================================================//
        //   Prepare Ok Request Data
        $request    =   Splash::ws()->pack(array(true));
        //====================================================================//
        //   Change WebService Encryption Key
        Splash::configuration()->WsEncryptionKey = rand(1E6, 1E10);
        Splash::ws()->setup();
        //====================================================================//
        //   Prepare Request Data
        $wrongRequest       =   Splash::ws()->pack(array(true));
        $this->assertNotEquals($request, $wrongRequest);
        //====================================================================//
        //   Restore WebService Encryption Key
        Splash::reboot();
        
        //====================================================================//
        //   Execute Connect with Right Server Id but Wrong Encryption
        //====================================================================//
        $wrongResponse      =   SplashServer::connect(Splash::configuration()->WsIdentifier, $wrongRequest);
        //====================================================================//
        //   Verify Response
        $this->assertEmpty($wrongResponse, "Connection with Wrong Data Encryption MUST be rejected => Empty Response");
        
        //====================================================================//
        //   Re-Execute Connect From Splash Server to Module
        $this->testConnectServerAction();
    }
}
