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

namespace Splash\Tests\Managers;

use Exception;
use ReflectionClass;
use Splash\Client\CommitEvent;
use Splash\Client\Splash;
use Splash\Components\Webservice;
use Splash\Tests\Tools\Components\TestCommitsManager as CommitsManager;
use Splash\Tests\Tools\ObjectsCase;

/**
 * Components Test Suite - Commits Manager Verifications
 */
class C71CommitsManagerTest extends ObjectsCase
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
     * @param string $testSequence
     * @param string $objectType
     *
     * @throws Exception
     *
     * @return void
     */
    public function testCoreFunctions(string $testSequence, string $objectType): void
    {
        //==============================================================================
        // Init
        $this->loadLocalTestSequence($testSequence);
        //==============================================================================
        // Safety Check
        $this->assertNotEmpty(Splash::objects());
        //==============================================================================
        // Validate Object Type
        $this->assertTrue(
            $this->invokeMethodStatic(CommitsManager::class, "isValidObjectType", array($objectType))
        );
    }

    /**
     * Verify Commit Without Post Commit Mode
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
    public function testGenericCommit(string $testSequence, string $objectType, $objectIds, string $action): void
    {
        //==============================================================================
        // Init
        $this->loadLocalTestSequence($testSequence);
        //==============================================================================
        // Test Success Commits.
        $okResponses = array(
            array("result" => false),
            array("result" => true),
            array("result" => "0"),
            array("result" => "1"),
            array("result" => false),
            array("result" => true),
            array("result" => "0"),
            array("result" => "1"),
        );
        foreach ($okResponses as $response) {
            //==============================================================================
            // Setup Mock WebService
            $webserviceMock = $this->assertMockWebserviceSetup();
            if (method_exists($webserviceMock, "method")) {
                $webserviceMock->method('call')->willReturn($response);
            }
            //==============================================================================
            // Setup Commit Manager
            CommitsManager::reset();
            CommitsManager::forceIntelMode(false);
            //==============================================================================
            // Execute Simple Commit
            $this->assertEmpty(CommitsManager::getSessionCommitted());
            $this->assertTrue(CommitsManager::commit($objectType, $objectIds, $action));
            /** @phpstan-ignore-next-line */
            $this->assertNotEmpty(CommitsManager::getSessionCommitted());
            $this->assertEmpty(CommitsManager::getWaitingEvents());
        }
        //==============================================================================
        // Test Success Commits.
        $koResponses = array(
            null,
            array(),
            array("result" => null),
        );
        foreach ($koResponses as $response) {
            //==============================================================================
            // Setup Mock WebService
            $webserviceMock = $this->assertMockWebserviceSetup();
            if (method_exists($webserviceMock, "method")) {
                $webserviceMock->method('call')->willReturn($response);
            }
            //==============================================================================
            // Setup Commit Manager
            CommitsManager::reset();
            CommitsManager::forceIntelMode(false);
            //==============================================================================
            // Execute Simple Commit
            $this->assertEmpty(CommitsManager::getSessionCommitted());
            $this->assertFalse(CommitsManager::commit($objectType, $objectIds, $action));
            $this->assertNotEmpty(CommitsManager::getSessionCommitted());
            $this->assertEmpty(CommitsManager::getWaitingEvents());
        }
    }

    /**
     * Verify Success Commit With Intel Commit Mode
     *
     * @dataProvider dummyCommitsProvider
     *
     * @param string          $testSequence
     * @param string          $objectType
     * @param string|string[] $objectIds
     *
     * @throws Exception
     *
     * @return void
     */
    public function testPostCommitStorage(string $testSequence, string $objectType, $objectIds): void
    {
        //==============================================================================
        // Init
        $this->loadLocalTestSequence($testSequence);
        //==============================================================================
        // Setup Mock WebService
        $webserviceMock = $this->assertMockWebserviceSetup();
        if (method_exists($webserviceMock, "method")) {
            $webserviceMock->method('call')->willReturn(array("result" => "1"));
        }
        //==============================================================================
        // Test Commit
        CommitsManager::reset();
        $firstSessionEvent = new CommitEvent($objectType, $objectIds, SPL_A_CREATE, "", "");
        CommitsManager::addWaitingEvent(new CommitEvent($objectType, $objectIds, SPL_A_CREATE, "", ""));
        CommitsManager::addWaitingEvent(new CommitEvent($objectType, $objectIds, SPL_A_UPDATE, "", ""));
        CommitsManager::addWaitingEvent(new CommitEvent($objectType, $objectIds, SPL_A_DELETE, "", ""));
        CommitsManager::addWaitingEvent(new CommitEvent($objectType, $objectIds, SPL_A_CREATE, "", ""));
        CommitsManager::addWaitingEvent(new CommitEvent($objectType, $objectIds, SPL_A_UPDATE, "", ""));
        CommitsManager::addWaitingEvent(new CommitEvent($objectType, $objectIds, SPL_A_DELETE, "", ""));
        //==============================================================================
        // Verify Waiting Events
        $waitingEvents = CommitsManager::getWaitingEvents();
        $this->assertIsArray($waitingEvents);
        $this->assertNotEmpty($waitingEvents);
        $this->assertCount(3, $waitingEvents);

        $firstEvent = array_shift($waitingEvents);
        $this->assertInstanceOf(CommitEvent::class, $firstEvent);
        $this->assertEquals(SPL_A_CREATE, $firstEvent->getAction());
        $this->assertEquals($firstSessionEvent->getObjectIds(), $firstEvent->getObjectIds());

        $lastEvent = array_pop($waitingEvents);
        $this->assertInstanceOf(CommitEvent::class, $lastEvent);
        $this->assertEquals(SPL_A_DELETE, $lastEvent->getAction());
        $this->assertEquals($firstSessionEvent->getObjectIds(), $lastEvent->getObjectIds());
        //==============================================================================
        // Reset Commits Manager
        CommitsManager::reset();
        //==============================================================================
        // Verify Reload from APCU
        $this->assertCount(3, CommitsManager::getWaitingEvents());
        //==============================================================================
        // Execute Post Commit Action
        $this->invokeMethodStatic(CommitsManager::class, "executePostCommit");
        //==============================================================================
        // Verify Lists
        $this->assertWaitingEventsListIsEmpty();
        //==============================================================================
        // Reset Commits Manager
        CommitsManager::reset();
        //==============================================================================
        // Verify Lists
        $this->assertWaitingEventsListIsEmpty();
    }

    /**
     * Verify Success Commit With Intel Commit Mode
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
    public function testPostCommitSuccess(string $testSequence, string $objectType, $objectIds, string $action): void
    {
        //==============================================================================
        // Init
        $this->loadLocalTestSequence($testSequence);
        //==============================================================================
        // Setup Mock WebService TO WORK
        $webserviceMock = $this->assertMockWebserviceSetup();
        if (method_exists($webserviceMock, "method")) {
            $webserviceMock->method('call')->willReturn(array("result" => "1"));
        }
        //==============================================================================
        // Reset Commit Manager
        CommitsManager::reset();
        $this->assertWaitingEventsListIsEmpty();
        //==============================================================================
        // Add Commit to Waiting List
        CommitsManager::addWaitingEvent(new CommitEvent($objectType, $objectIds, $action, "", ""));
        $this->assertNotEmpty(CommitsManager::getWaitingEvents());
        $this->assertCount(1, CommitsManager::getWaitingEvents());
        //==============================================================================
        // Execute Post Commit Action
        $this->invokeMethodStatic(CommitsManager::class, "executePostCommit");
        //==============================================================================
        // Finish
        CommitsManager::reset();
        $this->assertWaitingEventsListIsEmpty();
    }

    /**
     * Verify Fail Commit With Intel Commit Mode
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
    public function testPostCommitFail(string $testSequence, string $objectType, $objectIds, string $action): void
    {
        //==============================================================================
        // Init
        $this->loadLocalTestSequence($testSequence);
        //==============================================================================
        // Setup Mock WebService TO FAIL
        $webserviceMock = $this->assertMockWebserviceSetup();
        if (method_exists($webserviceMock, "method")) {
            $webserviceMock->method('call')->willReturn(array());
        }
        //==============================================================================
        // Reset Commit Manager
        CommitsManager::reset();
        $this->assertWaitingEventsListIsEmpty();
        //==============================================================================
        // Add Commit to Waiting List
        CommitsManager::addWaitingEvent(new CommitEvent($objectType, $objectIds, $action, "", ""));
        $this->assertNotEmpty(CommitsManager::getWaitingEvents());
        $this->assertNotEmpty(CommitsManager::getWaitingEvents());
        $this->assertCount(1, CommitsManager::getWaitingEvents());
        //==============================================================================
        // Execute Post Commit Action
        $this->invokeMethodStatic(CommitsManager::class, "executePostCommit");
        //==============================================================================
        // Reset Commits Manager
        CommitsManager::reset();
        //==============================================================================
        // Check Commit Event are Waiting
        $this->assertNotEmpty(CommitsManager::getWaitingEvents());
        $this->assertCount(1, CommitsManager::getWaitingEvents());
        //==============================================================================
        // Force All Commit Events to be ready Again
        $this->assertAllEventsAreReadyAgain();
        //==============================================================================
        // Setup Mock WebService TO WORK
        $webserviceMock = $this->assertMockWebserviceSetup();
        if (method_exists($webserviceMock, "method")) {
            $webserviceMock->method('call')->willReturn(array("result" => "1"));
        }
        //==============================================================================
        // Execute Post Commit Action
        $this->invokeMethodStatic(CommitsManager::class, "executePostCommit");
        //==============================================================================
        // Finish
        CommitsManager::reset();
        $this->assertWaitingEventsListIsEmpty();
    }

    /**
     * Verify Success Commit Retry Feature With Intel Commit Mode
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
    public function testPostCommitObsolete(string $testSequence, string $objectType, $objectIds, string $action): void
    {
        //==============================================================================
        // Init
        $this->loadLocalTestSequence($testSequence);
        //==============================================================================
        // Setup Mock WebService TO FAIL
        $webserviceMock = $this->assertMockWebserviceSetup();
        if (method_exists($webserviceMock, "method")) {
            $webserviceMock->method('call')->willReturn(null);
        }
        //==============================================================================
        // Reset Commit Manager
        CommitsManager::reset();
        $this->assertWaitingEventsListIsEmpty();
        //==============================================================================
        // Add Commit to Waiting List
        CommitsManager::addWaitingEvent(new CommitEvent($objectType, $objectIds, $action, "", ""));
        $this->assertNotEmpty(CommitsManager::getWaitingEvents());
        //==============================================================================
        // Force Repeated Commit Executions
        for ($i = 0; $i < 6; $i++) {
            //==============================================================================
            // Force All Commit Events to be ready Again
            $this->assertAllEventsAreReadyAgain();
            //==============================================================================
            // Execute Post Commit Action
            $this->invokeMethodStatic(CommitsManager::class, "executePostCommit");
        }
        //==============================================================================
        // Finish
        CommitsManager::reset();
        $this->assertWaitingEventsListIsEmpty();
    }

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

    //==============================================================================
    // PROTECTED METHODS
    //==============================================================================

    /**
     * Setup Mock Webservice Component
     *
     * @return Webservice
     */
    protected function assertMockWebserviceSetup(): Webservice
    {
        //==============================================================================
        // Setup Mock WebService
        $reflection = new ReflectionClass(Splash::class);
        $method = $reflection->getProperty("soap");
        $method->setAccessible(true);
        $method->setValue(
            Splash::core(),
            $this->createMock(Webservice::class)
        );
        $this->assertTrue(method_exists(Splash::ws(), "method"));

        return Splash::ws();
    }

    /**
     * Force All Commit Events to be ready Again
     *
     * @return void
     */
    protected function assertAllEventsAreReadyAgain(): void
    {
        //====================================================================//
        // Force Events for Retry
        CommitsManager::restartAll();
        //==============================================================================
        // Reboot Commit Manager
        CommitsManager::reset();
        //====================================================================//
        // Force Events for Retry
        $waitingEvents = CommitsManager::getWaitingEvents();
        foreach ($waitingEvents as $commitEvent) {
            $this->assertTrue($commitEvent->isReady());
        }
        //==============================================================================
        // Reboot Commit Manager
        CommitsManager::reset();
    }

    /**
     * Setup Mock Webservice Component
     *
     * @return void
     */
    protected function assertWaitingEventsListIsEmpty(): void
    {
        //==============================================================================
        // Verify Session Committed
        $sessionEvents = CommitsManager::getSessionCommitted();
        $this->assertIsArray($sessionEvents);
        $this->assertEmpty($sessionEvents);
        //==============================================================================
        // Verify Waiting Events
        $waitingEvents = CommitsManager::getWaitingEvents();
        $this->assertIsArray($waitingEvents);
        $this->assertEmpty($waitingEvents);
    }
}
