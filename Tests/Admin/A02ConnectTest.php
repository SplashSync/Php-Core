<?php

use Splash\Test\Tools\BaseClass;

use Splash\Client\Splash;
use Splash\Server\SplashServer;


/**
 * @abstract    Admin Test Suite - Ping Client Verifications
 *
 * @author SplashSync <contact@splashsync.com>
 */
class A02ConnectTest extends BaseClass {
    
    protected function onNotSuccessfulTest(Exception $e)
    {
        fwrite(STDOUT, Splash::Log()->GetConsoleLog() );
        throw $e;
    }    
    
    public function testConnectClientAction()
    {
        //====================================================================//
        //   Execute Connect From Module to Splash Server  
        $this->assertTrue(Splash::Connect(), " Test of Splash Server Connect Fail. Maybe this server is not connected? Check your configuration.");
        Splash::Log()->CleanLog();
    }
    
    public function testConnectServerAction()
    {
        //====================================================================//
        //   Prepare Request Data
        
//var_dump(Splash::Ws()->CleanOut());        
//var_dump(Splash::Ws()->getOutputBuffer());        
        
        $Request    =   Splash::Ws()->Pack( array(True) );
//var_dump($Request);
//        Splash::Ws()->unPack( $Request ); 
        
        //====================================================================//
        //   Execute Connect From Splash Server to Module  
        $Response   =   SplashServer::Connect(Splash::Configuration()->WsIdentifier, $Request);
        $Data       =   Splash::Ws()->unPack( $Response );     
var_dump($Data);
//        //====================================================================//
//        //   Verify Response
//        $this->assertNotEmpty( $Response                        , "Ping Response Block is Empty");
//        $this->assertNotEmpty( $Data                            , "Ping Response Data is Empty");
//        $this->assertInstanceOf( "ArrayObject" , $Data          , "Ping Response Data is Not an ArrayObject");
//        $this->assertNotEmpty( $Data->result                    , "Ping Result is not True");
        
        
        //====================================================================//
        //   SAFETY CHECK 
        //====================================================================//

        
fwrite(STDOUT, Splash::Log()->GetConsoleLog() );
    }
    
    
    
}
