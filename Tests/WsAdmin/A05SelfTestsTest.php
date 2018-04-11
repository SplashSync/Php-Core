<?php
namespace Splash\Tests\WsAdmin;

use Splash\Tests\Tools\AbstractBaseCase;

use Splash\Client\Splash;

/**
 * @abstract    Admin Test Suite - SelfTest Client Verifications
 *
 * @author SplashSync <contact@splashsync.com>
 */
class A05SelfTestsTest extends AbstractBaseCase
{
    public function testFromLocalClass()
    {
        //====================================================================//
        //   Execute Action From Module
        $Data = Splash::local()->selfTest();
        
        //====================================================================//
        //   Verify Response
        $this->verifyResponse($Data);
    }

    
    public function testFromAdmin()
    {
        
        //====================================================================//
        //   Execute Action From Splash Server to Module
        $Data = $this->genericAction(SPL_S_ADMIN, SPL_F_GET_SELFTEST, __METHOD__);
        
        //====================================================================//
        //   Verify Response
        $this->verifyResponse($Data);
    }
    
    public function verifyResponse($Data)
    {
        //====================================================================//
        //   Render Logs if Fails*
        if (!$Data) {
            fwrite(STDOUT, Splash::log()->getConsoleLog());
        }
        
        //====================================================================//
        //   Verify Response
        $this->assertIsSplashBool($Data, "SelfTest");
        $this->assertNotEmpty($Data, "SelfTest not Passed!! Check logs to see why!");
    }
}
