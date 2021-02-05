<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2021 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Tests\WsObjects;

use Splash\Client\Splash;
use Splash\Tests\Tools\ObjectsCase;

/**
 * Objects Test Suite - Object Base Class Verifications
 *
 * @author SplashSync <contact@splashsync.com>
 */
class O00ObjectBaseTest extends ObjectsCase
{
    /**
     * @dataProvider objectTypesProvider
     *
     * @param mixed $testSequence
     * @param mixed $objectType
     *
     * @return void
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
