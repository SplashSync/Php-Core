<?php
namespace Splash\Tests\WsObjects;

use Splash\Tests\Tools\ObjectsCase;
use Splash\Client\Splash;

//use ArrayObject;

/**
 * @abstract    Objects Test Suite - Object Base Class Verifications
 *
 * @author SplashSync <contact@splashsync.com>
 */
class O00ObjectBaseTest extends ObjectsCase
{
    
    /**
     * @dataProvider ObjectTypesProvider
     */
    public function testLockFeature($Sequence, $ObjectType)
    {
        //====================================================================//
        //   FOR NEW OBJECTS
        //====================================================================//
        Splash::Object($ObjectType)->Unlock();
        $this->assertFalse(Splash::Object($ObjectType)->isLocked());
        Splash::Object($ObjectType)->Lock();
        $this->assertTrue(Splash::Object($ObjectType)->isLocked());
        Splash::Object($ObjectType)->Unlock();
        $this->assertFalse(Splash::Object($ObjectType)->isLocked());
        
        //====================================================================//
        //   FOR EXISTING OBJECTS
        //====================================================================//

        //====================================================================//
        //  Integer IDs
        $IntObjectID = rand(1E3, 1E4);
        Splash::Object($ObjectType)->Unlock($IntObjectID);
        $this->assertFalse(Splash::Object($ObjectType)->isLocked($IntObjectID));
        Splash::Object($ObjectType)->Lock($IntObjectID);
        $this->assertTrue(Splash::Object($ObjectType)->isLocked($IntObjectID));
        Splash::Object($ObjectType)->Unlock($IntObjectID);
        $this->assertFalse(Splash::Object($ObjectType)->isLocked($IntObjectID));
        
        //====================================================================//
        //  Integer IDs
        $StrObjectID = base64_encode(rand(1E3, 1E4));
        Splash::Object($ObjectType)->Unlock($StrObjectID);
        $this->assertFalse(Splash::Object($ObjectType)->isLocked($StrObjectID));
        Splash::Object($ObjectType)->Lock($StrObjectID);
        $this->assertTrue(Splash::Object($ObjectType)->isLocked($StrObjectID));
        Splash::Object($ObjectType)->Unlock($StrObjectID);
        $this->assertFalse(Splash::Object($ObjectType)->isLocked($StrObjectID));
    }
}
