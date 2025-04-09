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

use ReflectionClass;
use Splash\Core\Client\CommitEvent;
use Splash\Core\Client\Splash;
use Splash\Core\Components\Webservice;
use Splash\Core\Dictionary\SplOperations;
use Splash\Core\Models\PhpUnit\ObjectsProviderAwareTrait;
use Splash\Core\Models\PhpUnit\PrivateMethodInvokerTrait;
use Splash\Tests\Tools\Components\TestCommitsManager as CommitsManager;
use Splash\Tests\Tools\ObjectsCase;

/**
 * Components Test Suite - Commits Manager Verifications
 */
class T404CommitsManagerTest extends ObjectsCase
{
    use PrivateMethodInvokerTrait;
    use ObjectsProviderAwareTrait;

    //==============================================================================
    // MAIN COMMIT FEATURES
    //==============================================================================

    /**
     * Verify Commit Manager Core Functions
     *
     * @dataProvider dummyCommitsProvider
     */
    public function testCoreFunctions(string $objectType): void
    {
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
     * @param string          $objectType
     * @param string|string[] $objectIds
     * @param string          $action
     */
    public function testGenericCommit(string $objectType, $objectIds, string $action): void
    {
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
     * @param string          $objectType
     * @param string|string[] $objectIds
     */
    public function testPostCommitStorage(string $objectType, $objectIds): void
    {
        //==============================================================================
        // Setup Mock WebService
        $webserviceMock = $this->assertMockWebserviceSetup();
        if (method_exists($webserviceMock, "method")) {
            $webserviceMock->method('call')->willReturn(array("result" => "1"));
        }
        //==============================================================================
        // Test Commit
        CommitsManager::reset();
        $firstSessionEvent = new CommitEvent($objectType, $objectIds, SplOperations::CREATE, "", "");
        CommitsManager::addWaitingEvent(new CommitEvent($objectType, $objectIds, SplOperations::CREATE, "", ""));
        CommitsManager::addWaitingEvent(new CommitEvent($objectType, $objectIds, SplOperations::UPDATE, "", ""));
        CommitsManager::addWaitingEvent(new CommitEvent($objectType, $objectIds, SplOperations::DELETE, "", ""));
        CommitsManager::addWaitingEvent(new CommitEvent($objectType, $objectIds, SplOperations::CREATE, "", ""));
        CommitsManager::addWaitingEvent(new CommitEvent($objectType, $objectIds, SplOperations::UPDATE, "", ""));
        CommitsManager::addWaitingEvent(new CommitEvent($objectType, $objectIds, SplOperations::DELETE, "", ""));
        //==============================================================================
        // Verify Waiting Events
        $waitingEvents = CommitsManager::getWaitingEvents();
        $this->assertIsArray($waitingEvents);
        $this->assertNotEmpty($waitingEvents);
        $this->assertCount(3, $waitingEvents);

        $firstEvent = array_shift($waitingEvents);
        $this->assertInstanceOf(CommitEvent::class, $firstEvent);
        $this->assertEquals(SplOperations::CREATE, $firstEvent->getAction());
        $this->assertEquals($firstSessionEvent->getObjectIds(), $firstEvent->getObjectIds());

        $lastEvent = array_pop($waitingEvents);
        $this->assertInstanceOf(CommitEvent::class, $lastEvent);
        $this->assertEquals(SplOperations::DELETE, $lastEvent->getAction());
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
     * @param string          $objectType
     * @param string|string[] $objectIds
     * @param string          $action
     */
    public function testPostCommitSuccess(string $objectType, $objectIds, string $action): void
    {
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
     * @param string          $objectType
     * @param string|string[] $objectIds
     * @param string          $action
     */
    public function testPostCommitFail(string $objectType, $objectIds, string $action): void
    {
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
     * @param string          $objectType
     * @param string|string[] $objectIds
     * @param string          $action
     */
    public function testPostCommitObsolete(string $objectType, $objectIds, string $action): void
    {
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
     * Generate Dummy Commits Dataset
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
        $objectTypes = $this->simpleObjectTypesProvider();
        $actions = array(SplOperations::CREATE, SplOperations::UPDATE, SplOperations::DELETE);
        //====================================================================//
        // Build Simple Dummy Commits
        $dummyCommits = array();
        foreach ($objectTypes as $objectType) {
            foreach ($actions as $action) {
                $key = ucfirst($objectType[0])." ".ucfirst($action);
                $dummyCommits[$key] = array(
                    $objectType[0],
                    uniqid($objectType[0]),
                    $action,
                    "Test User",
                    sprintf("Comment for %s, %s Test", $objectType[0], ucfirst($action)),
                );
            }
        }
        //====================================================================//
        // Build Multi Dummy Commits
        foreach ($objectTypes as $objectType) {
            foreach ($actions as $action) {
                $key = ucfirst($objectType[0])." ".ucfirst($action)." Multi";
                $dummyCommits[$key] = array(
                    $objectType[0],
                    array(uniqid($objectType[0]), uniqid($objectType[0]), uniqid($objectType[0])),
                    $action,
                    "Test User",
                    sprintf("Comment for %s, %s Test", $objectType[0], ucfirst($action)),
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
