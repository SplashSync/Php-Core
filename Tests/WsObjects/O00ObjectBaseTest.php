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
    public function testLockFeature($testSequence, $objectType)
    {
        $this->loadLocalTestSequence($testSequence);
        
        //====================================================================//
        //   FOR NEW OBJECTS
        //====================================================================//
        Splash::object($objectType)->unLock();
        $this->assertFalse(Splash::object($objectType)->isLocked());
        Splash::object($objectType)->lock();
        $this->assertTrue(Splash::object($objectType)->isLocked());
        Splash::object($objectType)->unLock();
        $this->assertFalse(Splash::object($objectType)->isLocked());
        
        //====================================================================//
        //   FOR EXISTING OBJECTS
        //====================================================================//

        //====================================================================//
        //  Integer IDs
        $intObjectId = rand((int) 1E3, (int) 1E4);
        Splash::object($objectType)->unLock($intObjectId);
        $this->assertFalse(Splash::object($objectType)->isLocked($intObjectId));
        Splash::object($objectType)->lock($intObjectId);
        $this->assertTrue(Splash::object($objectType)->isLocked($intObjectId));
        Splash::object($objectType)->unLock($intObjectId);
        $this->assertFalse(Splash::object($objectType)->isLocked($intObjectId));
        
        //====================================================================//
        //  String IDs
        $strObjectId = base64_encode((string) rand((int) 1E3, (int) 1E4));
        Splash::object($objectType)->unLock($strObjectId);
        $this->assertFalse(Splash::object($objectType)->isLocked($strObjectId));
        Splash::object($objectType)->lock($strObjectId);
        $this->assertTrue(Splash::object($objectType)->isLocked($strObjectId));
        Splash::object($objectType)->unLock($strObjectId);
        $this->assertFalse(Splash::object($objectType)->isLocked($strObjectId));
    }
}
