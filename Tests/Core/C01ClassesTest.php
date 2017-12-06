<?php
namespace Splash\Tests\Core;

use Splash\Tests\Tools\TestCase;

use Splash\Core\SplashCore     as Splash;

/**
 * @abstract    Core Test Suite - Raw Folders & Class Structure Verifications
 *
 * @author SplashSync <contact@splashsync.com>
 */
class C01ClassesTest extends TestCase {
    
    /**
     * @abstract    Display of Splash Logo 
     */    
    public function testDisplayLogo()
    {
        echo PHP_EOL;
        echo " ______     ______   __         ______     ______     __  __    " . PHP_EOL;
        echo "/\  ___\   /\  == \ /\ \       /\  __ \   /\  ___\   /\ \_\ \   " . PHP_EOL;
        echo "\ \___  \  \ \  _-/ \ \ \____  \ \  __ \  \ \___  \  \ \  __ \  " . PHP_EOL;
        echo " \/\_____\  \ \_\    \ \_____\  \ \_\ \_\  \/\_____\  \ \_\ \_\ " . PHP_EOL;
        echo "  \/_____/   \/_/     \/_____/   \/_/\/_/   \/_____/   \/_/\/_/ " . PHP_EOL;
        echo "                                                                " . PHP_EOL;
        echo ".";
        $this->assertTrue(True);
    }    

    public function testSplashCoreClass()
    {
        
        //====================================================================//
        //   VERIFY SPLASH MODULE BASE 
        //====================================================================//

        //====================================================================//
        //   Core Splash Module  
        $this->assertInstanceOf( "Splash\Core\SplashCore" , Splash::Core() , "Splash Core Class is Not from of Right Instance"); 
        //====================================================================//
        //   Splash Log Manager  
        $this->assertInstanceOf( "Splash\Components\Logger" , Splash::Log() , "Splash Logger Class is Not from of Right Instance"); 
        //====================================================================//
        //   Splash Webservice Manager  
        $this->assertInstanceOf( "Splash\Components\Webservice" , Splash::Ws() , "Splash Webservice Class is Not from of Right Instance"); 
        //====================================================================//
        //   Splash Router Manager  
        $this->assertInstanceOf( "Splash\Components\Router" , Splash::Router() , "Splash Router Class is Not from of Right Instance"); 
        //====================================================================//
        //   Splash Files Manager  
        $this->assertInstanceOf( "Splash\Components\FileManager" , Splash::File() , "Splash File Manager Class is Not from of Right Instance"); 
        //====================================================================//
        //   Splash Validation Manager  
        $this->assertInstanceOf( "Splash\Components\Validator" , Splash::Validate() , "Splash Validator Class is Not from of Right Instance"); 
        
        //====================================================================//
        //   Splash Xml Manager  
        $this->assertInstanceOf( "Splash\Components\XmlManager" , Splash::Xml() , "Splash Xml Manager Class is Not from of Right Instance"); 
        //====================================================================//
        //   Splash Translator Manager  
        $this->assertInstanceOf( "Splash\Components\Translator" , Splash::Translator() , "Splash Translator Class is Not from of Right Instance"); 
        
        
        //====================================================================//
        //   Splash Module  Configuration
        $this->assertInstanceOf( "ArrayObject" , Splash::Configuration() , "Splash Configuration is Not an ArrayObject");
        //====================================================================//
        //   Splash Module  Object List
        $this->assertInternalType( "array" , Splash::Objects() , "Splash Available Objects List is Not an Array");
        //====================================================================//
        //   Splash Module  Object List
        $this->assertInternalType( "array" , Splash::Widgets() , "Splash Available Widgets List is Not an Array");
        
    }
    
    /**
     * @runInSeparateProcess
     */
    public function testSplashClientClass()
    {
        
        //====================================================================//
        //   VERIFY SPLASH SERVER MODULE 
        //====================================================================//

        //====================================================================//
        //   Server Splash Module  
        $this->assertInstanceOf( "Splash\Client\Splash" , new \Splash\Client\Splash() , "Splash Client Class is Not from of Right Instance"); 

    }

    
    /**
     * @runInSeparateProcess
     */
    public function testSplashServerClass()
    {
        
        //====================================================================//
        //   VERIFY SPLASH SERVER MODULE 
        //====================================================================//

        //====================================================================//
        //   Server Splash Module  
        $this->assertInstanceOf( "Splash\Server\SplashServer" , new \Splash\Server\SplashServer() , "Splash Server Class is Not from of Right Instance"); 

        //====================================================================//
        //   Server Splash Module  
        $this->assertInstanceOf( "Splash\Models\CommunicationInterface" , Splash::Com() , "Splash Communication Interface is Not from of Right Instance"); 
        
    }
    
    
    
    public function testModuleLocalClass()
    {
        
        //====================================================================//
        //   VERIFY SPLASH MODULE LOCAL CLASS 
        //====================================================================//
        //
        //====================================================================//
        //   Verify Local Class Exists & Correctly Mapped
        $this->assertTrue( class_exists( SPLASH_CLASS_PREFIX. "\Local" ) , "Splash Local Class Not found. Check you local class is defined and autoloaded from Namespace Splash\Local\Local.");  
        
        //====================================================================//
        //   Verify Local Mandatory Functions Exists
        $this->assertTrue( Splash::Validate()->isValidLocalFunction("Parameters"),  "Splash Local Class MUST define this function to provide Module Local Parameters."); 
        $this->assertTrue( Splash::Validate()->isValidLocalFunction("Includes"),    "Splash Local Class MUST define this function to include Loacl System dependencies."); 
        $this->assertTrue( Splash::Validate()->isValidLocalFunction("Informations"),"Splash Local Class MUST define this function to be displayed on our servers."); 
        $this->assertTrue( Splash::Validate()->isValidLocalFunction("SelfTest"),    "Splash Local Class MUST define this function to perform addictionnal local tests to insure module st correctly installed & configured."); 
        
        //====================================================================//
        //   Load Local Splash Module  
        $this->assertInstanceOf( "Splash\Local\Local" , Splash::Local() , "Splash Local Class loading failled. Check it's properly defined."); 
        
    }    

    public function testModuleLocalPaths()
    {
        
        //====================================================================//
        //   VERIFY SPLASH MODULE LOCAL PATHS 
        //====================================================================//

        //====================================================================//
        //   Verify Local Path Exists
        $this->assertTrue( Splash::Validate()->isValidLocalPath(),    "Splash Local Class MUST define so that Splash can detect & use it's folder as root path for local Module files."); 
        
        //====================================================================//
        //   Verify Local Mandatory Paths Exists
        $ObjectPath = Splash::getLocalPath() . "/Objects";
        $this->assertTrue( is_dir($ObjectPath) ,    "Splash Local Objects folder MUST be define in " . $ObjectPath . "."); 
        $WidgetPath = Splash::getLocalPath() . "/Widgets";
        $this->assertTrue( is_dir($WidgetPath) ,    "Splash Local Widgets folder MUST be define in " . $WidgetPath . "."); 
        $TranslationPath = Splash::getLocalPath() . "/Translations";
        $this->assertTrue( is_dir($TranslationPath) ,    "Splash Local Translations folder MUST be define in " . $TranslationPath . "."); 
        
    }    
    
}
