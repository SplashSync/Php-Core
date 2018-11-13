<?php
namespace Splash\Tests\Core;

use Splash\Tests\Tools\TestCase;

use Splash\Core\SplashCore     as Splash;

use Splash\Components\Logger;
use Splash\Tests\Tools\AbstractBaseCase;

/**
 * @abstract    Core Test Suite - Raw Folders & Class Structure Verifications
 *
 * @author SplashSync <contact@splashsync.com>
 */
class C00StartupTest extends TestCase
{
    
    const   SPLIT   =   "----------------------------------------------------------------";
      
    const   SPLASH1 =   " ______     ______   __         ______     ______     __  __    ";
    const   SPLASH2 =   "/\  ___\   /\  == \ /\ \       /\  __ \   /\  ___\   /\ \_\ \   ";
    const   SPLASH3 =   "\ \___  \  \ \  _-/ \ \ \____  \ \  __ \  \ \___  \  \ \  __ \  ";
    const   SPLASH4 =   " \/\_____\  \ \_\    \ \_____\  \ \_\ \_\  \/\_____\  \ \_\ \_\ ";
    const   SPLASH5 =   "  \/_____/   \/_/     \/_____/   \/_/\/_/   \/_____/   \/_/\/_/ ";
    const   SPLASH6 =   "                                                                ";
            
    /**
     * @abstract    Display of Tested Sequences | Objects | Fields
     */
    public function testDisplayTestContext()
    {
        //====================================================================//
        //   SPLASH SCREEN
        //====================================================================//

        echo PHP_EOL;
        
        echo Logger::getConsoleLine(null, self::SPLIT, Logger::CMD_COLOR_MSG);
        echo Logger::getConsoleLine(null, self::SPLASH1, Logger::CMD_COLOR_WAR);
        echo Logger::getConsoleLine(null, self::SPLASH2, Logger::CMD_COLOR_WAR);
        echo Logger::getConsoleLine(null, self::SPLASH3, Logger::CMD_COLOR_WAR);
        echo Logger::getConsoleLine(null, self::SPLASH4, Logger::CMD_COLOR_WAR);
        echo Logger::getConsoleLine(null, self::SPLASH5, Logger::CMD_COLOR_WAR);
        echo Logger::getConsoleLine(null, self::SPLASH6, Logger::CMD_COLOR_WAR);
        echo Logger::getConsoleLine(null, self::SPLIT, Logger::CMD_COLOR_MSG);
        
        //====================================================================//
        //   TEST MAIN INFORMATIONS
        //====================================================================//

        $this->displayTestedObjects();
        $this->displayTestedSequences();
        $this->displayFilteredFields();
        
        echo Logger::getConsoleLine(null, self::SPLIT, Logger::CMD_COLOR_MSG);
        echo PHP_EOL . ".";
        
        $this->assertTrue(true);
    }
    
    /**
     * @abstract    Display of Tested Objects List
     */
    private function displayTestedObjects()
    {
        //====================================================================//
        //   TESTED OBJECTS
        //====================================================================//
        $objectTypes    =   Splash::objects();
        if (!is_array($objectTypes)) {
            echo Logger::getConsoleLine(" !! Invalid Objects List !! ", " - Tested Objects ", Logger::CMD_COLOR_DEB);
            return;
        }
        foreach ($objectTypes as $key => $objectType) {
            //====================================================================//
            //   Filter Tested Object Types  =>> Skip
            if (!AbstractBaseCase::isAllowedObjectType($objectType)) {
                unset($objectTypes[$key]);
            }
        }
        echo Logger::getConsoleLine(implode(" | ", $objectTypes), "- Tested Objects: ", Logger::CMD_COLOR_DEB);
    }
    
    /**
     * @abstract    Display of Tested Sequences List
     */
    private function displayTestedSequences()
    {
        //====================================================================//
        //   TESTED SEQUENCES
        //====================================================================//
        
        //====================================================================//
        // Check if Local Tests Sequences are defined
        $testSequences  =   "None";
        if (!is_null(Splash::local()) && method_exists(Splash::local(), "TestSequences")) {
            $testSequences  =   Splash::local()->testSequences("List");
        }
        if (!is_array($testSequences) && ($testSequences !== "None")) {
            echo Logger::getConsoleLine("!!Invalid Tests Sequence List!!", " - Tested Objects ", Logger::CMD_COLOR_DEB);
            return;
        }
        if ($testSequences === "None") {
            return;
        }
        echo Logger::getConsoleLine(implode(" | ", $testSequences), "- Test Sequences: ", Logger::CMD_COLOR_DEB);
    }

    /**
     * @abstract    Display of Filter on Objets Fields
     */
    private function displayFilteredFields()
    {
        //====================================================================//
        //   FILTERED FIELDS
        //====================================================================//
        
        //====================================================================//
        //   Filter Tested Object Fields  =>> Skip
        if (defined("SPLASH_FIELDS") && is_scalar(SPLASH_FIELDS) && !empty(explode(",", SPLASH_FIELDS))) {
            echo Logger::getConsoleLine(SPLASH_FIELDS, "- Fields Filter: ", Logger::CMD_COLOR_DEB);
            echo Logger::getConsoleLine("!! TEST WILL FOCUS ON SPECIFIC FIELDS !!", null, Logger::CMD_COLOR_DEB);
        }
    }
}
