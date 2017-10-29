<?php

namespace Splash\Tests\WsAdmin;

use Splash\Tests\Tools\BaseCase;

use Splash\Client\Splash;
use Splash\Server\SplashServer;


/**
 * @abstract    Admin Test Suite - Connect Client Verifications
 *
 * @author SplashSync <contact@splashsync.com>
 */
class A02ConnectTest extends BaseCase {
    
    protected function setUp()
    {
        //====================================================================//
        // BOOT or REBOOT MODULE
        Splash::Reboot();
        
        //====================================================================//
        // FAKE SPLASH SERVER HOST URL
        Splash::Configuration()->WsHost = $this->getLocalServerSoapUrl();        
        Splash::Ws()->Setup();
    }       
    
    public function testConnectClientAction()
    {
        if ( !empty(Splash::Input("SPLASH_TRAVIS")) ) {
            $this->markTestSkipped('No HTTP Calls in Client Mode');
        }        
        //====================================================================//
        //   Execute Connect From Module to Splash Server  
        $this->assertTrue(Splash::Connect(), " Test of Splash Server Connect Fail. Maybe this server is not connected? Check your configuration.");
        Splash::Log()->CleanLog();
    }
    
    public function testConnectServerAction()
    {
        //====================================================================//
        //   Prepare Request Data
        $Request    =   Splash::Ws()->Pack( array(True) );
        //====================================================================//
        //   Execute Connect From Splash Server to Module  
        $Response   =   SplashServer::Connect(Splash::Configuration()->WsIdentifier, $Request);
        $Data       = $this->CheckResponse( $Response );     
        //====================================================================//
        //   Verify Response
        $this->assertNotEmpty( $Data->result                    , "Connect Result is not True");
        
        //====================================================================//
        //   SAFETY CHECK 
        //====================================================================//
        
        //====================================================================//
        //   Execute Connect with No Server Id
        $NoId       =   SplashServer::Connect(Null, $Request);
        $this->assertEmpty( $NoId       , "Connection with No Server Id MUST be rejected => Empty Response");

        //====================================================================//
        //   Execute Connect with Wrong Server Id
        $WrongId    =   SplashServer::Connect( rand( 1E6, 1E10 ), $Request);
        $this->assertEmpty( $WrongId    , "Connection with Wrong Server Id MUST be rejected => Empty Response");
        
        //====================================================================//
        //   Turn On Output Buffering Again  
        ob_start();        
    }
    
    public function testConnectServerWrongDataAction()
    {
        //====================================================================//
        //   Prepare Ok Request Data
        $Request    =   Splash::Ws()->Pack( array(True) );
        //====================================================================//
        //   Change WebService Encryption Key
        Splash::Configuration()->WsEncryptionKey = rand( 1E6, 1E10 );
        Splash::Ws()->Setup();
        //====================================================================//
        //   Prepare Request Data
        $WrongRequest       =   Splash::Ws()->Pack( array(True) );
        $this->assertNotEquals( $Request, $WrongRequest );
        //====================================================================//
        //   Restore WebService Encryption Key
        Splash::Reboot();
        
        //====================================================================//
        //   Execute Connect with Right Server Id but Wrong Encryption
        //====================================================================//
        $WrongResponse      =   SplashServer::Connect( Splash::Configuration()->WsIdentifier , $WrongRequest);
        //====================================================================//
        //   Verify Response
        $this->assertEmpty( $WrongResponse    , "Connection with Wrong Data Encryption MUST be rejected => Empty Response");
        
        //====================================================================//
        //   Re-Execute Connect From Splash Server to Module  
        $this->testConnectServerAction();

    }
    
}
