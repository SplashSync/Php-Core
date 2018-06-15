<?php
namespace Splash\Tests\Core;

use Splash\Tests\Tools\TestCase;

use Splash\Core\SplashCore     as Splash;

/**
 * @abstract    Core Test Suite - Raw Folders & Class Structure Verifications
 *
 * @author SplashSync <contact@splashsync.com>
 */
class C01ClassesTest extends TestCase
{

    public function testSplashCoreClass()
    {
        
        //====================================================================//
        //   VERIFY SPLASH MODULE BASE
        //====================================================================//

        //====================================================================//
        //   Core Splash Module
        $this->assertInstanceOf(
            "Splash\Core\SplashCore",
            Splash::core(),
            "Splash Core Class is Not from of Right Instance"
        );
        //====================================================================//
        //   Splash Log Manager
        $this->assertInstanceOf(
            "Splash\Components\Logger",
            Splash::log(),
            "Splash Logger Class is Not from of Right Instance"
        );
        //====================================================================//
        //   Splash Webservice Manager
        $this->assertInstanceOf(
            "Splash\Components\Webservice",
            Splash::ws(),
            "Splash Webservice Class is Not from of Right Instance"
        );
        //====================================================================//
        //   Splash Router Manager
        $this->assertInstanceOf(
            "Splash\Components\Router",
            Splash::router(),
            "Splash Router Class is Not from of Right Instance"
        );
        //====================================================================//
        //   Splash Files Manager
        $this->assertInstanceOf(
            "Splash\Components\FileManager",
            Splash::file(),
            "Splash File Manager Class is Not from of Right Instance"
        );
        //====================================================================//
        //   Splash Validation Manager
        $this->assertInstanceOf(
            "Splash\Components\Validator",
            Splash::validate(),
            "Splash Validator Class is Not from of Right Instance"
        );
        
        //====================================================================//
        //   Splash Xml Manager
        $this->assertInstanceOf(
            "Splash\Components\XmlManager",
            Splash::xml(),
            "Splash Xml Manager Class is Not from of Right Instance"
        );
        //====================================================================//
        //   Splash Translator Manager
        $this->assertInstanceOf(
            "Splash\Components\Translator",
            Splash::translator(),
            "Splash Translator Class is Not from of Right Instance"
        );
        //====================================================================//
        //   Splash Module  Configuration
        $this->assertInstanceOf(
            "ArrayObject",
            Splash::configuration(),
            "Splash Configuration is Not an ArrayObject"
        );
        //====================================================================//
        //   Splash Module  Object List
        $this->assertInternalType(
            "array",
            Splash::objects(),
            "Splash Available Objects List is Not an Array"
        );
        //====================================================================//
        //   Splash Module  Object List
        $this->assertInternalType(
            "array",
            Splash::widgets(),
            "Splash Available Widgets List is Not an Array"
        );
    }
    
    public function testSplashClientClass()
    {
        
        //====================================================================//
        //   VERIFY SPLASH SERVER MODULE
        //====================================================================//

        //====================================================================//
        //   Server Splash Module
        $this->assertInstanceOf(
            "Splash\Client\Splash",
            new \Splash\Client\Splash(),
            "Splash Client Class is Not from of Right Instance"
        );
    }

    public function testSplashServerClass()
    {
        
        //====================================================================//
        //   VERIFY SPLASH SERVER MODULE
        //====================================================================//

        //====================================================================//
        //   Server Splash Module
        $this->assertInstanceOf(
            "Splash\Server\SplashServer",
            new \Splash\Server\SplashServer(),
            "Splash Server Class is Not from of Right Instance"
        );

        //====================================================================//
        //   Server Splash Module
        $this->assertInstanceOf(
            "Splash\Models\CommunicationInterface",
            Splash::com(),
            "Splash Communication Interface is Not from of Right Instance"
        );
    }
    
    public function testModuleLocalClass()
    {
        
        //====================================================================//
        //   VERIFY SPLASH MODULE LOCAL CLASS
        //====================================================================//
        //
        //====================================================================//
        //   Verify Local Class Exists & Correctly Mapped
        $this->assertTrue(
            class_exists(SPLASH_CLASS_PREFIX. "\Local"),
            "Splash Local Class Not found. Check you local class"
                . " is defined and autoloaded from Namespace Splash\Local\Local."
        );
        
        //====================================================================//
        //   Verify Local Mandatory Functions Exists
        $this->assertTrue(
            Splash::validate()->isValidLocalFunction("Parameters"),
            "Splash Local Class MUST define this function to provide Module Local Parameters."
        );
        $this->assertTrue(
            Splash::validate()->isValidLocalFunction("Includes"),
            "Splash Local Class MUST define this function to include Loacl System dependencies."
        );
        $this->assertTrue(
            Splash::validate()->isValidLocalFunction("Informations"),
            "Splash Local Class MUST define this function to be displayed on our servers."
        );
        $this->assertTrue(
            Splash::validate()->isValidLocalFunction("SelfTest"),
            "Splash Local Class MUST define this function to perform addictionnal"
                . " local tests to insure module st correctly installed & configured."
        );
        
        //====================================================================//
        //   Load Local Splash Module
        $this->assertInstanceOf(
            "Splash\Local\Local",
            Splash::local(),
            "Splash Local Class loading failled. Check it's properly defined."
        );
    }

    public function testModuleLocalPaths()
    {
        
        //====================================================================//
        //   VERIFY SPLASH MODULE LOCAL PATHS
        //====================================================================//

        //====================================================================//
        //   Verify Local Path Exists
        $this->assertTrue(
            Splash::validate()->isValidLocalPath(),
            "Splash Local Class MUST define so that Splash can "
                . "detect & use it's folder as root path for local Module files."
        );
        
        //====================================================================//
        //   Verify Local Mandatory Paths Exists
        $ObjectPath = Splash::getLocalPath() . "/Objects";
        $this->assertTrue(
            is_dir($ObjectPath),
            "Splash Local Objects folder MUST be define in " . $ObjectPath . "."
        );
        $WidgetPath = Splash::getLocalPath() . "/Widgets";
        $this->assertTrue(
            is_dir($WidgetPath),
            "Splash Local Widgets folder MUST be define in " . $WidgetPath . "."
        );
        $TranslationPath = Splash::getLocalPath() . "/Translations";
        $this->assertTrue(
            is_dir($TranslationPath),
            "Splash Local Translations folder MUST be define in " . $TranslationPath . "."
        );
    }
}
