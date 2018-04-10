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
     * @dataProvider objectTypesProvider
     */
    public function testLockFeature($Sequence, $ObjectType)
    {
        $this->loadLocalTestSequence($Sequence);
        
        //====================================================================//
        //   FOR NEW OBJECTS
        //====================================================================//
        Splash::object($ObjectType)->unLock();
        $this->assertFalse(Splash::object($ObjectType)->isLocked());
        Splash::object($ObjectType)->lock();
        $this->assertTrue(Splash::object($ObjectType)->isLocked());
        Splash::object($ObjectType)->unLock();
        $this->assertFalse(Splash::object($ObjectType)->isLocked());
        
        //====================================================================//
        //   FOR EXISTING OBJECTS
        //====================================================================//

        //====================================================================//
        //  Integer IDs
        $IntObjectID = rand(1E3, 1E4);
        Splash::object($ObjectType)->unLock($IntObjectID);
        $this->assertFalse(Splash::object($ObjectType)->isLocked($IntObjectID));
        Splash::object($ObjectType)->lock($IntObjectID);
        $this->assertTrue(Splash::object($ObjectType)->isLocked($IntObjectID));
        Splash::object($ObjectType)->unLock($IntObjectID);
        $this->assertFalse(Splash::object($ObjectType)->isLocked($IntObjectID));
        
        //====================================================================//
        //  Integer IDs
        $StrObjectID = base64_encode(rand(1E3, 1E4));
        Splash::object($ObjectType)->unLock($StrObjectID);
        $this->assertFalse(Splash::object($ObjectType)->isLocked($StrObjectID));
        Splash::object($ObjectType)->lock($StrObjectID);
        $this->assertTrue(Splash::object($ObjectType)->isLocked($StrObjectID));
        Splash::object($ObjectType)->unLock($StrObjectID);
        $this->assertFalse(Splash::object($ObjectType)->isLocked($StrObjectID));
    }
}
