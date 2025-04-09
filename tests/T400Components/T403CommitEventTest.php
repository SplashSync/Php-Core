<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Core\Tests\T400Components;

use DateTime;
use PHPUnit\Framework\TestCase;
use Splash\Core\Client\CommitEvent;
use Splash\Core\Client\Splash;
use Splash\Core\Dictionary\SplOperations;
use Splash\Core\Interfaces\Object\ObjectInterface;
use Splash\Core\Models\PhpUnit\ObjectsProviderAwareTrait;

/**
 * Components Test Suite - Commit Event Verifications
 */
class T403CommitEventTest extends TestCase
{
    use ObjectsProviderAwareTrait;

    /**
     * Verify Commit Manager Core Functions
     *
     * @dataProvider simpleObjectTypesProvider
     */
    public function testMainFeatures(string $objectType): void
    {
        //==============================================================================
        // Create an Event
        $objectId = random_int((int) 1E3, (int)  1E5);
        $commitEvent = new CommitEvent(
            $objectType,
            $objectId,
            SplOperations::UPDATE,
            "PhpUnit",
            ""
        );
        //==============================================================================
        // Basic Getters
        $this->assertEquals($objectType, $commitEvent->getObjectType());
        $this->assertEquals(SplOperations::UPDATE, $commitEvent->getAction());
        $this->assertEquals(array((string) $objectId), $commitEvent->getObjectIds());
        $this->assertEquals(
            array(
                "type" => $objectType,
                "action" => SplOperations::UPDATE,
                "id" => array((string) $objectId),
                "user" => "PhpUnit",
                "comment" => "",
            ),
            $commitEvent->toArray()
        );
        $this->assertNotEmpty($commitEvent->getWsIdentifier());
        $this->assertIsString($commitEvent->getWsIdentifier());
        $this->assertNotEmpty($commitEvent->getDescription());
    }

    /**
     * Verify Commit Event Locks Management
     *
     * @dataProvider simpleObjectTypesProvider
     */
    public function testLockFeature(string $objectType): void
    {
        //==============================================================================
        // Create an Event
        $objectId = array(
            (string) random_int((int) 1E3, (int)  1E5),
            (string) random_int((int) 1E3, (int)  1E5),
            (string) random_int((int) 1E3, (int)  1E5)
        );
        $createEvent = new CommitEvent(
            $objectType,
            $objectId,
            SplOperations::CREATE,
            "PhpUnit",
            ""
        );
        $updateEvent = new CommitEvent(
            $objectType,
            $objectId,
            SplOperations::UPDATE,
            "PhpUnit",
            ""
        );
        $deleteEvent = new CommitEvent(
            $objectType,
            $objectId,
            SplOperations::DELETE,
            "PhpUnit",
            ""
        );
        //==============================================================================
        // Pick a Random Object Id
        $objectId = $updateEvent->getObjectIds()[array_rand($updateEvent->getObjectIds())];
        $this->assertIsString($objectId);
        //==============================================================================
        // Object is Unlocked
        $this->assertTrue($createEvent->isAllowed());
        $this->assertTrue($updateEvent->isAllowed());
        $this->assertTrue($deleteEvent->isAllowed());
        //==============================================================================
        // Lock Object
        $splashObject = Splash::object($objectType);
        $this->assertInstanceOf(ObjectInterface::class, $splashObject);
        $splashObject->lock($objectId);
        //==============================================================================
        // Object is Locked
        $this->assertFalse($createEvent->isAllowed());
        $this->assertFalse($updateEvent->isAllowed());
        $this->assertFalse($deleteEvent->isAllowed());
        //==============================================================================
        // Unlock Object
        $splashObject = Splash::object($objectType);
        $this->assertInstanceOf(ObjectInterface::class, $splashObject);
        $splashObject->unlock($objectId);
        //==============================================================================
        // Object is Unlocked
        $this->assertTrue($createEvent->isAllowed());
        $this->assertTrue($updateEvent->isAllowed());
        $this->assertTrue($deleteEvent->isAllowed());
    }

    /**
     * Verify Commit Event Locks Management
     *
     * @dataProvider simpleObjectTypesProvider
     */
    public function testLockCreateFeature(string $objectType): void
    {
        //==============================================================================
        // Create an Event
        $objectId = (string) random_int((int) 1E3, (int)  1E5);
        $createEvent = new CommitEvent(
            $objectType,
            $objectId,
            SplOperations::CREATE,
            "PhpUnit",
            ""
        );
        $updateEvent = new CommitEvent(
            $objectType,
            $objectId,
            SplOperations::UPDATE,
            "PhpUnit",
            ""
        );
        $deleteEvent = new CommitEvent(
            $objectType,
            $objectId,
            SplOperations::DELETE,
            "PhpUnit",
            ""
        );
        //==============================================================================
        // Pick a Random Object Id
        $this->assertIsString($objectId);
        //==============================================================================
        // Object is Unlocked
        $this->assertTrue($createEvent->isAllowed());
        $this->assertTrue($updateEvent->isAllowed());
        $this->assertTrue($deleteEvent->isAllowed());
        //==============================================================================
        // Lock Object
        $splashObject = Splash::object($objectType);
        $this->assertInstanceOf(ObjectInterface::class, $splashObject);
        $splashObject->lock();
        //==============================================================================
        // Object is Locked
        $this->assertFalse($createEvent->isAllowed());
        $this->assertTrue($updateEvent->isAllowed());
        $this->assertTrue($deleteEvent->isAllowed());
        //==============================================================================
        // Unlock Object
        $splashObject = Splash::object($objectType);
        $this->assertInstanceOf(ObjectInterface::class, $splashObject);
        $splashObject->unlock();
        //==============================================================================
        // Object is Unlocked
        $this->assertTrue($createEvent->isAllowed());
        $this->assertTrue($updateEvent->isAllowed());
        $this->assertTrue($deleteEvent->isAllowed());
    }

    /**
     * Verify Commit Event Md5 Builder
     *
     * @dataProvider simpleObjectTypesProvider
     */
    public function testMd5Feature(string $objectType): void
    {
        //==============================================================================
        // Create Events
        $objectId = random_int((int) 1E3, (int) 1E5);
        $baseEvent = new CommitEvent(
            $objectType,
            $objectId,
            SplOperations::UPDATE,
            "PhpUnit",
            ""
        );
        $sameObjectEvent = new CommitEvent(
            $objectType,
            array((int) $objectId),
            SplOperations::UPDATE,
            "PhpUnit",
            ""
        );
        $diffObjectEvent = new CommitEvent(
            $objectType,
            $objectId + 1,
            SplOperations::UPDATE,
            "PhpUnit",
            ""
        );
        $actionEvent = new CommitEvent(
            $objectType,
            $objectId,
            SplOperations::CREATE,
            "PhpUnit",
            ""
        );
        Splash::configuration()->WsIdentifier = uniqid();
        $wdIdEvent = new CommitEvent(
            $objectType,
            $objectId,
            SplOperations::UPDATE,
            "PhpUnit",
            ""
        );
        //==============================================================================
        // Verify Events Md5
        $this->assertEquals($baseEvent->getMd5(), $sameObjectEvent->getMd5());
        $this->assertNotEquals($baseEvent->getMd5(), $diffObjectEvent->getMd5());
        $this->assertNotEquals($baseEvent->getMd5(), $actionEvent->getMd5());
        $this->assertNotEquals($baseEvent->getMd5(), $wdIdEvent->getMd5());
    }

    /**
     * Verify Commit Event Retry Feature
     *
     * @dataProvider simpleObjectTypesProvider
     */
    public function testRetryFeature(string $objectType): void
    {
        //==============================================================================
        // Create Events
        $objectId = random_int((int) 1E3, (int) 1E5);
        $commitEvent = new CommitEvent(
            $objectType,
            $objectId,
            SplOperations::UPDATE,
            "PhpUnit",
            ""
        );
        //==============================================================================
        // Verify Event
        $this->assertTrue($commitEvent->isReady());
        $this->assertNull($commitEvent->getRetryAt());
        $lastDate = new DateTime();
        for ($i = 0; $i < 5; $i++) {
            $commitEvent->setFail();
            $this->assertFalse($commitEvent->isReady());
            $this->assertFalse($commitEvent->isObsolete());
            $this->assertInstanceOf(DateTime::class, $commitEvent->getRetryAt());
            $this->assertGreaterThan($lastDate, $commitEvent->getRetryAt());
            $lastDate = $commitEvent->getRetryAt();
        }
        $commitEvent->setFail();
        $this->assertFalse($commitEvent->isReady());
        $this->assertTrue($commitEvent->isObsolete());
    }
}
