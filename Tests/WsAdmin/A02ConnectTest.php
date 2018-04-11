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
            $this->markTestSkipped('No HTTP Calls in Client Mode');
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
        $Request    =   Splash::ws()->pack(array(true));
        //====================================================================//
        //   Execute Connect From Splash Server to Module
        $Response   =   SplashServer::connect(Splash::configuration()->WsIdentifier, $Request);
        $Data       = $this->checkResponse($Response);
        //====================================================================//
        //   Verify Response
        $this->assertNotEmpty($Data->result, "Connect Result is not True");
        
        //====================================================================//
        //   SAFETY CHECK
        //====================================================================//
        
        //====================================================================//
        //   Execute Connect with No Server Id
        $NoId       =   SplashServer::connect(null, $Request);
        $this->assertEmpty($NoId, "Connection with No Server Id MUST be rejected => Empty Response");

        //====================================================================//
        //   Execute Connect with Wrong Server Id
        $WrongId    =   SplashServer::connect(rand(1E6, 1E10), $Request);
        $this->assertEmpty($WrongId, "Connection with Wrong Server Id MUST be rejected => Empty Response");
        
        //====================================================================//
        //   Turn On Output Buffering Again
        ob_start();
    }
    
    public function testConnectServerWrongDataAction()
    {
        //====================================================================//
        //   Prepare Ok Request Data
        $Request    =   Splash::ws()->pack(array(true));
        //====================================================================//
        //   Change WebService Encryption Key
        Splash::configuration()->WsEncryptionKey = rand(1E6, 1E10);
        Splash::ws()->setup();
        //====================================================================//
        //   Prepare Request Data
        $WrongRequest       =   Splash::ws()->pack(array(true));
        $this->assertNotEquals($Request, $WrongRequest);
        //====================================================================//
        //   Restore WebService Encryption Key
        Splash::reboot();
        
        //====================================================================//
        //   Execute Connect with Right Server Id but Wrong Encryption
        //====================================================================//
        $WrongResponse      =   SplashServer::connect(Splash::configuration()->WsIdentifier, $WrongRequest);
        //====================================================================//
        //   Verify Response
        $this->assertEmpty($WrongResponse, "Connection with Wrong Data Encryption MUST be rejected => Empty Response");
        
        //====================================================================//
        //   Re-Execute Connect From Splash Server to Module
        $this->testConnectServerAction();
    }
}
