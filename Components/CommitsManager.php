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

namespace Splash\Components;

use DateTime;
use Exception;
use Splash\Client\CommitEvent;
use Splash\Client\Splash;

/**
 * Splash Webservice Changes Commit Manager
 */
class CommitsManager
{
    const CACHE_TTL = (7 * 3600);

    /**
     * Is Intelligent Shutdown Commit Mode Enabled Flag
     *
     * @var null|bool
     */
    protected static ?bool $intelCommitMode;

    /**
     * List of all Commits done inside this current session
     *
     * @var array[]
     */
    private static array $committed = array();

    /**
     * List of Commit Events for Shutdown Commits
     *
     * @var null|array<string, CommitEvent>
     */
    private static ?array $waitingEvents;

    /**
     * Submit an Update for a Local Object
     *
     * @param string                    $objectType Object Type Name
     * @param int|int[]|string|string[] $local      Local Object IDs or Array of Local Ids
     * @param string                    $action     Action Type (SPL_A_UPDATE, or SPL_A_CREATE, or SPL_A_DELETE)
     * @param string                    $user       User Name
     * @param string                    $comment    Operation Comment for Historic
     *
     * @return bool
     */
    public static function commit(
        string $objectType,
        $local,
        string $action,
        string $user = '',
        string $comment = ''
    ): bool {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();
        //====================================================================//
        // Verify this Object Class is Valid ==> No Action on this Node
        if (!self::isValidObjectType($objectType)) {
            return true;
        }
        //====================================================================//
        // Create Commit Event
        $commitEvent = new CommitEvent($objectType, $local, $action, $user, $comment);
        //====================================================================//
        // Initiate Tasks parameters array
        self::$committed[] = $commitEvent->toArray();
        //====================================================================//
        // Verify this Object is Locked ==> No Action on this Node
        if (!$commitEvent->isAllowed() || static::isTravisMode($commitEvent)) {
            return true;
        }
        //====================================================================//
        // Intel Commits Mode ==> Push Event to Waiting Queue
        if (self::isIntelCommitsMode()) {
            self::addWaitingEvent($commitEvent);
            Splash::log()->msg(Splash::trans("MsgWsIntelCommit"));
            //====================================================================//
            //  Smart Notifications => Filter Messages, Only Warnings & Errors
            if (Splash::configuration()->SmartNotify) {
                Splash::log()->smartFilter();
            }

            return true;
        }
        //====================================================================//
        // Execute Server Commit
        return self::executeCommit($commitEvent);
    }

    /**
     * Perform Manager Self Test
     *
     * @return bool
     */
    public static function selfTest(): bool
    {
        Splash::translator()->load('objects');
        //====================================================================//
        //  Check If a Intel Commit is Active
        if (!self::isIntelCommitsMode()) {
            //====================================================================//
            //  Check If Apcu is Active
            if (!empty(Splash::configuration()->WsPostCommit) && !self::hasApcuFeature()) {
                Splash::log()->war("NoWsIntelCommitApcu");
            }

            return true;
        }
        //====================================================================//
        //  Intel Commit is Active
        Splash::log()->msg("HasWsIntelCommit");
        //====================================================================//
        //  Check Waiting List
        $waitingCount = count(self::getWaitingEvents());
        if ($waitingCount) {
            Splash::log()->war("HasWsIntelCommitWip", (string) $waitingCount);
        }

        return true;
    }

    //====================================================================//
    // Functional
    //====================================================================//

    /**
     * Check If Intel Commit Mode is Active
     *
     * @return bool
     */
    public static function isIntelCommitsMode(): bool
    {
        //====================================================================//
        //  Init Already Done
        if (isset(self::$intelCommitMode)) {
            return self::$intelCommitMode;
        }
        //====================================================================//
        //  Check if Post Commit Mode is Active
        if (empty(Splash::configuration()->WsPostCommit)) {
            return self::$intelCommitMode = false;
        }
        //====================================================================//
        //  Register Post Commit Function
        $callBack = array(self::class,"executePostCommit");
        if (!is_callable($callBack)) {
            return self::$intelCommitMode = false;
        }
        register_shutdown_function($callBack);

        return self::$intelCommitMode = true;
    }

    /**
     * Reset List of Session Committed Objects
     *
     * @return void
     */
    public static function reset(): void
    {
        self::$committed = array();
        self::$intelCommitMode = null;
        self::$waitingEvents = null;
    }

    /**
     * Update All Commit Events to be restarted immediately
     *
     * @return void
     */
    public static function restartAll(): void
    {
        //====================================================================//
        // Force Events for Retry
        self::$waitingEvents = self::getWaitingEvents();
        foreach (self::$waitingEvents as $commitEvent) {
            $commitEvent->setRetryAt(new DateTime("-1 second"));
        }
        //==============================================================================
        // Save Cache
        self::saveCache();
    }

    //====================================================================//
    // Commits Events Queue Management
    //====================================================================//

    /**
     * Get List of Session Committed Objects
     *
     * @return array[]
     */
    public static function getSessionCommitted(): array
    {
        return self::$committed;
    }

    /**
     * Add a Commit Event for Post Treatment
     *
     * @param CommitEvent $commitEvent
     *
     * @return int Number of Waiting Events
     */
    public static function addWaitingEvent(CommitEvent $commitEvent): int
    {
        $md5 = $commitEvent->getMd5();
        //====================================================================//
        // Load Events from APCU Cache
        self::$waitingEvents = self::getWaitingEvents();
        //====================================================================//
        // Push Event on Waiting List
        self::$waitingEvents[$md5] = $commitEvent;
        //====================================================================//
        // Save Events
        self::saveCache();

        return count(self::$waitingEvents);
    }

    /**
     * Get List of Waiting Events
     *
     * @return CommitEvent[]
     */
    public static function getWaitingEvents(): array
    {
        //====================================================================//
        // Load Events from APCU Cache
        if (!isset(self::$waitingEvents)) {
            self::$waitingEvents = self::getCache();
        }
        //====================================================================//
        // Return Events
        return self::$waitingEvents;
    }

    /**
     * Execute Post Commit Actions
     *
     * @return void
     */
    public static function executePostCommit(): void
    {
        $failCount = 0;
        //====================================================================//
        //  Walk on Waiting Events
        foreach (self::getWaitingEvents() as $commitEvent) {
            //====================================================================//
            // Check if Event is to be Send
            if (!$commitEvent->isReady()) {
                continue;
            }
            //====================================================================//
            // Execute Commit
            if (self::executeCommit($commitEvent)) {
                self::setCommitEventSuccess($commitEvent);

                continue;
            }
            self::setCommitEventFail($commitEvent);
            //====================================================================//
            // Limit number of Failures
            if ($failCount++ >= 10) {
                return;
            }
        }
    }

    //====================================================================//
    // PupUnit Tests Methods
    //====================================================================//

    /**
     * Simulate Commit For PhpUnits Tests (USE WITH CARE)
     * Only PhpUnit Tests are Impacted by This Action
     *
     * @param string                    $objectType Object Type Name
     * @param int|int[]|string|string[] $local      Object Local ID or Array of Local IDs
     * @param string                    $action     Action Type (SPL_A_UPDATE, or SPL_A_CREATE, or SPL_A_DELETE)
     * @param null|string               $user       User Name
     * @param null|string               $comment    Operation Comment for logs
     *
     * @throws Exception
     *
     * @return void
     */
    public static function simSessionCommit(
        string $objectType,
        $local,
        string $action,
        ?string $user = null,
        ?string $comment = null
    ): void {
        //====================================================================//
        // Safety Check
        if (!Splash::isDebugMode()) {
            throw new Exception("You cannot Simulate Commit without Debug Mode");
        }
        //====================================================================//
        // Create Commit Event
        $commitEvent = new CommitEvent(
            $objectType,
            $local,
            $action,
            $user ?: 'PhpUnit',
            $comment ?: 'No Comment'
        );
        //====================================================================//
        // Store as Committed
        self::$committed[] = $commitEvent->toArray();
    }

    //====================================================================//
    // Protected Methods
    //====================================================================//

    /**
     * Validate Object Type
     *
     * @param string $objectType Object Type Name
     *
     * @return bool
     */
    protected static function isValidObjectType(string $objectType): bool
    {
        try {
            Splash::object($objectType);

            return true;
        } catch (Exception $exception) {
            Splash::log()->war(
                sprintf('Module Commit Skipped: %s ', $exception->getMessage())
            );

            return false;
        }
    }

    /**
     * Check if Commit we Are in Travis Mode
     *
     * @param CommitEvent $commitEvent
     *
     * @return bool
     */
    protected static function isTravisMode(CommitEvent $commitEvent): bool
    {
        //====================================================================//
        // Detect Travis from SERVER CONSTANTS
        if (empty(Splash::input('SPLASH_TRAVIS'))) {
            return false;
        }
        //====================================================================//
        // Push a Warning for User Infos
        Splash::log()->war(sprintf(
            "Module Commit Skipped (%s, %s, %s)",
            $commitEvent->getObjectType(),
            $commitEvent->getAction(),
            implode('|', $commitEvent->getObjectIds())
        ));

        return true;
    }

    /**
     * Check If Apcu Feature is Active
     *
     * @return bool
     */
    protected static function hasApcuFeature(): bool
    {
        //====================================================================//
        //  Check if Apcu Extension is Active
        if (!\function_exists('apcu_fetch')
            || !filter_var(ini_get('apc.enabled'), \FILTER_VALIDATE_BOOLEAN)) {
            return false;
        }
        //====================================================================//
        //  if We are on CLI
        if ("cli" == \PHP_SAPI) {
            return filter_var(ini_get('apc.enable_cli'), \FILTER_VALIDATE_BOOLEAN);
        }

        return true;
    }

    //====================================================================//
    // Commits Execution
    //====================================================================//

    /**
     * Execute Real Server Commit
     *
     * @param CommitEvent $commitEvent
     *
     * @return bool
     */
    private static function executeCommit(CommitEvent $commitEvent): bool
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();
        //====================================================================//
        // Add Task to Ws Task List
        Splash::ws()->addTask(SPL_F_COMMIT, $commitEvent->toArray(), $commitEvent->getDescription());
        //====================================================================//
        // Execute Task
        $response = Splash::ws()->call(SPL_S_OBJECTS);
        //====================================================================//
        // Analyze results
        return self::isCommitSuccess($response);
    }

    /**
     * Check if Commit Call was Successful
     *
     * @param null|array $response Splash Server Response
     *
     * @return bool
     */
    private static function isCommitSuccess(?array $response): bool
    {
        //====================================================================//
        // Commit is Considered as Fail only if Splash Server did not respond.
        if (!$response || !isset($response['result'])) {
            return false;
        }
        //====================================================================//
        //  Smart Notifications => Filter Messages, Only Warnings & Errors
        if (Splash::configuration()->SmartNotify) {
            Splash::log()->smartFilter();
        }

        return true;
    }

    /**
     * When Commit Event is Successful
     *
     * @param CommitEvent $commitEvent
     *
     * @return void
     */
    private static function setCommitEventSuccess(CommitEvent $commitEvent): void
    {
        //====================================================================//
        // Remove from List
        unset(self::$waitingEvents[$commitEvent->getMd5()]);
        //====================================================================//
        // Save Events
        self::saveCache();
    }

    /**
     * When Commit Event Fail
     *
     * @param CommitEvent $commitEvent
     *
     * @return void
     */
    private static function setCommitEventFail(CommitEvent $commitEvent): void
    {
        //====================================================================//
        // Increment Fail Counter
        $commitEvent->setFail();
        //====================================================================//
        // If Obsolete
        if ($commitEvent->isObsolete()) {
            //====================================================================//
            // Remove from List
            unset(self::$waitingEvents[$commitEvent->getMd5()]);
        } else {
            //====================================================================//
            // Update on List
            self::$waitingEvents[$commitEvent->getMd5()] = $commitEvent;
        }
        //====================================================================//
        // Save Events
        self::saveCache();
    }

    //====================================================================//
    // Other Private Methods
    //====================================================================//

    /**
     * Get Cache Key for Apcu or Files Storage
     *
     * @return string
     */
    private static function getCacheKey(): string
    {
        return md5(static::class.Splash::configuration()->WsIdentifier);
    }

    /**
     * Save Waiting Events on Apcu Cache or Temp File
     *
     * @return void
     */
    private static function saveCache(): void
    {
        //====================================================================//
        //  Apcu is Active
        if (self::hasApcuFeature()) {
            //====================================================================//
            // Save Events to APCU Cache
            apcu_store(self::getCacheKey(), self::$waitingEvents, self::CACHE_TTL);

            return;
        }
        //====================================================================//
        // Serialize Events
        $fileContents = null;
        foreach (self::$waitingEvents ?? array() as $waitingEvent) {
            $fileContents .= serialize($waitingEvent).PHP_EOL;
        }
        $dir = \sys_get_temp_dir()."/splash";
        //====================================================================//
        // Ensure Storage Dir Exists
        if (!is_dir($dir)) {
            mkdir($dir);
        }
        //====================================================================//
        // Save Events to Temp File
        file_put_contents(
            $dir."/".self::getCacheKey(),
            $fileContents
        );
    }

    /**
     * Load Waiting Events from Apcu Cache or Temp File
     *
     * @return CommitEvent[]
     */
    private static function getCache(): array
    {
        //====================================================================//
        //  Apcu is Active
        if (self::hasApcuFeature()) {
            //====================================================================//
            // Load Events from APCU Cache
            /** @phpstan-ignore-next-line */
            return apcu_fetch(self::getCacheKey()) ?: array();
        }
        $path = \sys_get_temp_dir()."/splash/".self::getCacheKey();
        //====================================================================//
        // Safety Check
        if (!is_file($path) || !is_readable($path)) {
            return array();
        }
        //====================================================================//
        // Walk on Lines
        $commitEvents = array();
        $serialisedEvents = file($path);
        $options = array("allowed_classes" => array(CommitEvent::class, DateTime::class));
        if (is_array($serialisedEvents)) {
            foreach ($serialisedEvents as $serialisedEvent) {
                try {
                    $event = unserialize($serialisedEvent, $options);
                } catch (Exception $exception) {
                    continue;
                }
                if ($event instanceof CommitEvent) {
                    $commitEvents[$event->getMd5()] = $event;
                }
            }
        }

        return $commitEvents;
    }
}
