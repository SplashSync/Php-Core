<?php

use Splash\Tests\Tools\BaseClass;

use Splash\Client\Splash;
use Splash\Server\SplashServer;

/**
 * @abstract    Admin Test Suite - Ping Client Verifications
 *
 * @author SplashSync <contact@splashsync.com>
 */
class A01PingTest extends BaseClass { 
    
    public function testDummy()
    {
        $this->assertTrue(True);
    }

    public function testPingClientAction()
    {
        //====================================================================//
        //   Execute Ping From Module to Splash Server  
        $this->assertTrue(Splash::Ping(), " Test of Splash Server Ping Fail. Maybe this server is not connected? Check your configuration.");
        
        Splash::Log()->CleanLog();
    }
    
    public function testPingServerAction()
    {

        //====================================================================//
        //   Execute Ping From Splash Server to Module  
        $Response   =   SplashServer::Ping();
        $Data       =   Splash::Ws()->unPack( $Response , 1 );     

        //====================================================================//
        //   Verify Response
        $this->assertNotEmpty( $Response                        , "Ping Response Block is Empty");
        $this->assertNotEmpty( $Data                            , "Ping Response Data is Empty");
        $this->assertInstanceOf( "ArrayObject" , $Data          , "Ping Response Data is Not an ArrayObject");
        $this->assertArrayHasKey( "result", $Data               , "Ping Result is Missing");
        $this->assertNotEmpty( $Data->result                    , "Ping Result is not True");

        
        //====================================================================//
        //   SAFETY CHECK 
        //====================================================================//

        

    }
    
    
    
}
