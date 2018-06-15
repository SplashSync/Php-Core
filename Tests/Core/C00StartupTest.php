<?php
namespace Splash\Tests\Core;

use Splash\Tests\Tools\TestCase;

use Splash\Core\SplashCore     as Splash;

use Splash\Tests\Tools\AbstractBaseCase;

/**
 * @abstract    Core Test Suite - Raw Folders & Class Structure Verifications
 *
 * @author SplashSync <contact@splashsync.com>
 */
class C00StartupTest extends TestCase
{
    
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
        $this->assertTrue(true);
    }

    /**
     * @abstract    Display of Tested Objects
     */
    public function testDisplayTestedObjects()
    {
        
        $Objects    =   Splash::objects();
        $this->assertTrue(true);
        
        if (!is_array($Objects)) {
            return;
        }
        
        echo " = Tested Objects : ";
        
        foreach ($Objects as $ObjectType) {
            //====================================================================//
            //   Filter Tested Object Types  =>> Skip
            if (!AbstractBaseCase::isAllowedObjectType($ObjectType)) {
                continue;
            }
            echo $ObjectType . ", ";
        }
        echo PHP_EOL;
        echo "..";
    }
}
