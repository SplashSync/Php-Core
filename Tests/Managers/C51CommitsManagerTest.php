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

namespace Splash\Tests\Managers;

use Exception;
use Splash\Client\Splash;
use Splash\Models\Objects\ObjectInterface;
use Splash\Tests\Tools\Components\TestCommitsManager as CommitsManager;
use Splash\Tests\Tools\ObjectsCase;

/**
 * Components Test Suite - Commits Manager Verifications
 *
 * @author SplashSync <contact@splashsync.com>
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class C51CommitsManagerTest extends ObjectsCase
{
    use \Splash\Tests\Tools\Traits\MethodInvokerTrait;

    //==============================================================================
    // MAIN COMMIT FEATURES
    //==============================================================================

    /**
     * Verify Commit Manager Core Functions
     *
     * @dataProvider dummyCommitsProvider
     *
     * @param string          $testSequence
     * @param string          $objectType
     * @param string|string[] $objectIds
     * @param string          $action
     *
     * @throws Exception
     *
     * @return void
     */
    public function testCoreFunctions(string $testSequence, string $objectType, $objectIds, string $action): void
    {
        //==============================================================================
        // Init
        $this->loadLocalTestSequence($testSequence);
        //==============================================================================
        // Safety Check
        $this->assertNotEmpty(Splash::objects());
        //==============================================================================
        // Create Manager Mock
        $manager = $this->createMock(CommitsManager::class);
        //==============================================================================
        // Validate Object Type
        $this->assertTrue(
            $this->invokeMethod($manager, "isValidObjectType", array($objectType))
        );
        //==============================================================================
        // Validate Object Ids Converter
        $objectIds = $this->invokeMethod($manager, "toObjectIds", array($objectIds));
        $this->assertIsArray($objectIds);
        //==============================================================================
        // Check Commits Allowed
        $this->assertTrue($this->invokeMethod(
            $manager,
            "isCommitAllowed",
            array($objectType, $objectIds, $action)
        ));
    }

    /**
     * Verify Commit Manager Objects Locks Management
     *
     * @dataProvider dummyCommitsProvider
     *
     * @param string          $testSequence
     * @param string          $objectType
     * @param string|string[] $objectIds
     * @param string          $action
     *
     * @throws Exception
     *
     * @return void
     */
    public function testLocksFunctions(
        string $testSequence,
        string $objectType,
        $objectIds,
        string $action
    ): void {
        //==============================================================================
        // Init
        $this->loadLocalTestSequence($testSequence);
        //==============================================================================
        // Safety Check
        $this->assertNotEmpty(Splash::objects());
        //==============================================================================
        // Create Manager Mock
        $manager = $this->createMock(CommitsManager::class);
        //==============================================================================
        // Convert Object Ids
        $objectIds = $this->invokeMethod($manager, "toObjectIds", array($objectIds));
        $this->assertIsArray($objectIds);
        //==============================================================================
        // Pick a Random Object Id
        $objectId = $objectIds[array_rand($objectIds)];
        $this->assertIsString($objectId);
        //==============================================================================
        // Object is Unlocked
        $this->assertTrue($this->invokeMethod(
            $manager,
            "isCommitAllowed",
            array($objectType, $objectIds, $action)
        ));
        //==============================================================================
        // Lock Object
        $splashObject = \Splash\Client\Splash::object($objectType);
        $this->assertInstanceOf(ObjectInterface::class, $splashObject);
        $splashObject->lock($objectId);
        //==============================================================================
        // Object is Locked
        $this->assertFalse($this->invokeMethod(
            $manager,
            "isCommitAllowed",
            array($objectType, $objectIds, $action)
        ));
        //==============================================================================
        // Unlock Object
        $splashObject = \Splash\Client\Splash::object($objectType);
        $this->assertInstanceOf(ObjectInterface::class, $splashObject);
        $splashObject->unlock($objectId);
        //==============================================================================
        // Object is Unlocked
        $this->assertTrue($this->invokeMethod(
            $manager,
            "isCommitAllowed",
            array($objectType, $objectIds, $action)
        ));
    }

    /**
     * Verify Commit Manager Post Commit Mode Enable Process
     *
     * @dataProvider sequencesProvider
     *
     * @param string $testSequence
     *
     * @throws Exception
     *
     * @return void
     */
    public function testPostCommitMode(string $testSequence): void
    {
        //==============================================================================
        // Init
        $this->loadLocalTestSequence($testSequence);
        //==============================================================================
        // Create Manager Mock
        $manager = $this->createMock(CommitsManager::class);
        //==============================================================================
        // Initial State
        $initialState = Splash::configuration()->WsPostCommit;
        $this->assertEquals(
            $initialState,
            $this->invokeMethod($manager, "isPostCommitMode", array(false))
        );
        //==============================================================================
        // Enable Post Commit
        Splash::configuration()->WsPostCommit = true;
        $this->assertTrue(
            $this->invokeMethod($manager, "isPostCommitMode", array(false))
        );
        //==============================================================================
        // Disable Post Commit
        Splash::configuration()->WsPostCommit = false;
        $this->assertFalse(
            $this->invokeMethod($manager, "isPostCommitMode", array(false))
        );
        //==============================================================================
        // Reset
        Splash::configuration()->WsPostCommit = $initialState;
        $this->assertEquals(
            $initialState,
            $this->invokeMethod($manager, "isPostCommitMode", array(false))
        );
    }

    //==============================================================================
    // POST COMMIT FEATURES
    //==============================================================================

    //==============================================================================
    // RETRIED COMMIT FEATURES
    //==============================================================================

    //==============================================================================
    // DATA PROVIDERS
    //==============================================================================

    /**
     * @throws Exception
     *
     * @return array[]
     */
    public function dummyCommitsProvider(): array
    {
        //====================================================================//
        // BOOT MODULE
        Splash::core();
        //====================================================================//
        // Configure
        $objectTypes = $this->objectTypesProvider();
        $actions = array(SPL_A_CREATE, SPL_A_UPDATE, SPL_A_DELETE);
        //====================================================================//
        // Build Simple Dummy Commits
        $dummyCommits = array();
        foreach ($objectTypes as $objectType) {
            foreach ($actions as $action) {
                $key = $objectType[0]." ".ucfirst($objectType[1])." ".ucfirst($action);
                $dummyCommits[$key] = array(
                    $objectType[0],
                    $objectType[1],
                    uniqid($objectType[1]),
                    $action,
                    "Test User",
                    sprintf("Comment for %s, %s Test", $objectType[1], ucfirst($action)),
                );
            }
        }
        //====================================================================//
        // Build Multi Dummy Commits
        foreach ($objectTypes as $objectType) {
            foreach ($actions as $action) {
                $key = $objectType[0]." ".ucfirst($objectType[1])." ".ucfirst($action)." Multi";
                $dummyCommits[$key] = array(
                    $objectType[0],
                    $objectType[1],
                    array(uniqid($objectType[1]), uniqid($objectType[1]), uniqid($objectType[1])),
                    $action,
                    "Test User",
                    sprintf("Comment for %s, %s Test", $objectType[1], ucfirst($action)),
                );
            }
        }
        //====================================================================//
        // Skip tests if No Objects Found
        if (empty($dummyCommits)) {
            $this->markTestSkipped("No Splash Objects Available");
        }

        return $dummyCommits;
    }
}
