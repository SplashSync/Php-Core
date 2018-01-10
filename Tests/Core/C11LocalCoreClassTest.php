<?php
namespace Splash\Tests\Core;

use Splash\Tests\Tools\TestCase;

use Splash\Core\SplashCore     as Splash;


use ArrayObject;

/**
 * @abstract    Core Test Suite - Module's Local Class Basics Verifications
 *
 * @author SplashSync <contact@splashsync.com>
 */
class C11LocalCoreClassTest extends TestCase {
    
//    protected function onNotSuccessfulTest(\Throwable $t)
//    {
//        fwrite(STDOUT, Splash::Log()->GetConsoleLog() );
//        throw $t;
//    }
    
    public function testParameterFunction()
    {
        
        //====================================================================//
        //   VERIFY LOCAL PARAMETERS READING
        //====================================================================//

        $Parameters = Splash::Local()->Parameters();
        
        //====================================================================//
        //   Verify Parameters
        $this->assertInternalType( "array" , $Parameters            , "Returned Local Parameters are Not inside an Array");
        $this->assertNotEmpty( $Parameters                          , "Returned Empty Parameters");
        $this->assertArrayHasKey( "WsIdentifier", $Parameters       , "Local Parameter is Missing");
        $this->assertArrayHasKey( "WsEncryptionKey", $Parameters    , "Local Parameter is Missing");
        $this->assertNotEmpty( $Parameters["WsIdentifier"]          , "Local Parameter is Empty");
        $this->assertNotEmpty( $Parameters["WsEncryptionKey"]       , "Local Parameter is Empty");
        
        //====================================================================//
        //   Verify Module Parsing
        $this->assertTrue( Splash::Validate()->isValidLocalParameterArray($Parameters), "Local Parameter Module's Verifictaion failled.");
        
    }
    
    public function testIncludesFunction()
    {
        //====================================================================//
        //   VERIFY LOCAL INCLUDES PASS & REPEATABLE
        //====================================================================//
        for ( $i=0 ; $i<5 ; $i++) {
            $this->assertTrue(Splash::Local()->Includes(), "Local Include Must Return True & be repeatable.");
        }
    }
    
    public function testSelfTestFunction()
    {
        //====================================================================//
        //   VERIFY LOCAL SELFTEST PASS & REPEATABLE
        //====================================================================//
        for ( $i=0 ; $i<5 ; $i++) {
            $this->assertTrue(Splash::Local()->SelfTest(), "Local SelfTest Must Return True (Passed) & be repeatable.");
        }
    }
    
    
    public function testInformationsFunction()
    {
        
        //====================================================================//
        //   VERIFY LOCAL INFOS READING
        //====================================================================//

        
        //====================================================================//
        // Init Response Object
        $In = new ArrayObject(array("Dummy" => True),  ArrayObject::ARRAY_AS_PROPS);
        
        //====================================================================//
        // Fetch Informations
        $Out = Splash::Local()->Informations($In);
        
        //====================================================================//
        //   Verify Informations
        $this->assertInstanceOf( "ArrayObject" , $Out               , "Returned Local Informations are Not inside an ArrayObject");
        $this->assertArrayHasKey( "company", $Out                   , "Local Informations is Missing");
        $this->assertArrayHasKey( "address", $Out                   , "Local Informations is Missing");
        $this->assertArrayHasKey( "zip", $Out                       , "Local Informations is Missing");
        $this->assertArrayHasKey( "town", $Out                      , "Local Informations is Missing");
        $this->assertArrayHasKey( "www", $Out                       , "Local Informations is Missing");
        $this->assertArrayHasKey( "email", $Out                     , "Local Informations is Missing");
        $this->assertArrayHasKey( "phone", $Out                     , "Local Informations is Missing");
        
        //====================================================================//
        //   Verify Module Informations are Still Present
        $this->assertArrayHasKey( "Dummy", $Out                     , "Splash Original Informations are Missing");
        
        //====================================================================//
        //   Verify Module Parsing
        $this->assertNotEmpty( Splash::Informations(), "Module Informations Reading failled.");
        
    }
    
}
