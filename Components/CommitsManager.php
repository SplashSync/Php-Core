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

namespace Splash\Components;

use ArrayObject;
use Exception;
use Splash\Client\Splash;

/**
 * Splash Webservice Changes Commit Manager
 */
class CommitsManager
{
    /**
     * List of all Commits done inside this current session
     *
     * @var array
     */
    private static $committed = array();

    /**
     * Is Shutdown Commit Mode Enabled
     *
     * @var bool
     */
    private static $postCommit;

    /**
     * List of Actions for Shutdown Commit Mode
     *
     * @var array()
     */
    private static $postCommitObjects = array();

    /**
     * Submit an Update for a Local Object
     *
     * @param string           $objectType Object Type Name
     * @param array|int|string $local      Local Object Id or Array of Local Ids
     * @param string           $action     Action Type (SPL_A_UPDATE, or SPL_A_CREATE, or SPL_A_DELETE)
     * @param string           $user       User Name
     * @param string           $comment    Operation Comment for Historic
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
        // Parse Objects Ids
        $objectIds = self::toObjectIds($local);
        //====================================================================//
        // Initiate Tasks parameters array
        static::$committed[] = $params = self::getCommitParameters($objectType, $objectIds, $action, $user, $comment);
        //====================================================================//
        // Verify this Object is Locked ==> No Action on this Node
        if (!self::isCommitAllowed($objectType, $objectIds, $action)) {
            return true;
        }
        //====================================================================//
        // Post Commits Mode ==> No Action Now
        if (self::isPostCommitMode()) {
            self::$postCommitObjects[] = $params;
            Splash::log()->msg(Splash::trans("MsgWsPostCommit"));

            return true;
        }
        //====================================================================//
        // Execute Server Commit
        return self::executeCommit($params);
    }

    /**
     * Execute Post Commits
     *
     * @return bool
     */
    public static function postCommit(): bool
    {
        $success = true;
        //====================================================================//
        //  Walk on Post Commits Parameters
        foreach (self::$postCommitObjects as $index => $postCommitObject) {
            //====================================================================//
            // Execute Commits
            if (self::executeCommit($postCommitObject)) {
                unset(self::$postCommitObjects[$index]);

                continue;
            }
            $success = false;
        }

        return $success;
    }

    /**
     * Get List of Session Committed Objects
     *
     * @return array
     */
    public static function getSessionCommitted(): array
    {
        return self::$committed;
    }

    /**
     * Simulate Commit For PhpUnits Tests (USE WITH CARE)
     * Only PhpUnit Tests are Impacted by This Action
     *
     * @param string           $objectType Object Type Name
     * @param array|int|string $local      Object Local Id or Array of Local Id
     * @param string           $action     Action Type (SPL_A_UPDATE, or SPL_A_CREATE, or SPL_A_DELETE)
     * @param string           $user       User Name
     * @param null|string      $comment    Operation Comment for logs
     *
     * @throws Exception
     *
     * @return void
     */
    public static function simSessionCommit(
        string $objectType,
        $local,
        string $action,
        string $user = 'PhpUnit',
        string $comment = null
    ): void {
        if (!Splash::isDebugMode()) {
            throw new Exception("You cannot Simulate Commit without Debug Mode");
        }

        self::$committed[] = self::getCommitParameters(
            $objectType,
            self::toObjectIds($local),
            $action,
            $user,
            $comment ?? 'Simulated Commit'
        );
    }

    /**
     * Reset List of Session Committed Objects
     *
     * @return void
     */
    public static function resetSessionCommitted(): void
    {
        self::$committed = array();
    }

    /**
     * Validate Object Type
     *
     * @param string $objectType Object Type Name
     *
     * @return bool
     */
    private static function isValidObjectType(string $objectType): bool
    {
        try {
            return !empty(Splash::object($objectType));
        } catch (Exception $exception) {
            Splash::log()->war(
                sprintf('Module Commit Skipped: %s ', $exception->getMessage())
            );

            return false;
        }
    }

    /**
     * Parse Object Ids
     *
     * @param array|int|string $objectIds object Local Id or Array of Local Id
     *
     * @return array
     */
    private static function toObjectIds($objectIds): array
    {
        //====================================================================//
        // Ensure we have an array
        $objectIds = is_array($objectIds) ? $objectIds : array((string) $objectIds);
        //====================================================================//
        // Ensure Objects Ids as String
        array_map(function ($objectId) {
            return (string) $objectId;
        }, $objectIds);

        return $objectIds;
    }

    /**
     * Build Call Parameters Array
     *
     * @param string   $objectType Object Type Name
     * @param string[] $objectIds  Local Objects Ids
     * @param string   $action     Action Type (SPL_A_UPDATE, or SPL_A_CREATE, or SPL_A_DELETE)
     * @param string   $user       User Name
     * @param string   $comment    Operation Comment for Logs
     *
     * @return array
     */
    private static function getCommitParameters(
        string $objectType,
        array $objectIds,
        string $action,
        string $user,
        string $comment
    ): array {
        return array(
            "type" => $objectType,        // Type of the Object
            "id" => $objectIds,           // Id of Modified object
            "action" => $action,          // Action Type On this Object
            "user" => $user,              // Operation User Name for Logs
            "comment" => $comment,        // Operation Comment for Logs
        );
    }

    /**
     * Check if Commit is Allowed Local Object
     *
     * @param string   $objectType Object Type Name
     * @param string[] $objectIds  List Object Local Ids
     * @param string   $action     Action Type (SPL_A_UPDATE, or SPL_A_CREATE, or SPL_A_DELETE)
     *
     * @return bool
     */
    private static function isCommitAllowed(string $objectType, array $objectIds, string $action): bool
    {
        try {
            $splashObject = Splash::object($objectType);
        } catch (Exception $exception) {
            return false;
        }
        //====================================================================//
        // Verify this Object is Locked ==> No Action on this Node
        //====================================================================//
        foreach ($objectIds as $value) {
            if ($splashObject->isLocked($value)) {
                return false;
            }
        }
        //====================================================================//
        // Verify Create Object is Locked ==> No Action on this Node
        if ((SPL_A_CREATE === $action) && $splashObject->isLocked()) {
            return false;
        }
        //====================================================================//
        // Verify if Travis Mode (PhpUnit) ==> No Commit Allowed
        return !self::isTravisMode($objectType, $objectIds, $action);
    }

    /**
     * Check if Commit we Are in Travis Mode
     *
     * @param string   $objectType Object Type Name
     * @param string[] $local      List Object Local Ids
     * @param string   $action     Action Type (SPL_A_UPDATE, or SPL_A_CREATE, or SPL_A_DELETE)
     *
     * @return bool
     */
    private static function isTravisMode(string $objectType, array $local, string $action): bool
    {
        //====================================================================//
        // Detect Travis from SERVER CONSTANTS
        if (empty(Splash::input('SPLASH_TRAVIS'))) {
            return false;
        }
        $objectIds = implode('|', $local);
        Splash::log()->war('Module Commit Skipped ('.$objectType.', '.$action.', '.$objectIds.')');

        return true;
    }

    /**
     * Execute Real Server Commit
     *
     * @param array $parameters Commit Parameters
     *
     * @return bool
     */
    private static function executeCommit(array $parameters): bool
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();
        //====================================================================//
        // Build Task Description
        $description = Splash::trans(
            'MsgSchRemoteCommit',
            $parameters["action"],
            $parameters["type"],
            (string) Splash::count($parameters["id"])
        );
        //====================================================================//
        // Add Task to Ws Task List
        Splash::ws()->addTask(SPL_F_COMMIT, $parameters, $description);
        //====================================================================//
        // Execute Task
        $response = Splash::ws()->call(SPL_S_OBJECTS);
        //====================================================================//
        // Analyze NuSOAP results
        return self::isCommitSuccess($response);
    }

    /**
     * Check if Commit Call was Successful
     *
     * @param ArrayObject|false $response Splash Server Response
     *
     * @return bool
     */
    private static function isCommitSuccess($response): bool
    {
        //====================================================================//
        // Analyze NuSOAP results
        if (!$response || !isset($response->result) || (true != $response->result)) {
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
     * Check If Post Commit Mode is Active
     *
     * @return bool
     */
    private static function isPostCommitMode(): bool
    {
        //====================================================================//
        //  Safety Check = > Never do this on CLI
        if ("cli" == PHP_SAPI) {
            return false;
        }
        //====================================================================//
        //  Check if Post Commit Mode is Active
        if (empty(Splash::configuration()->WsPostCommit)) {
            return false;
        }
        //====================================================================//
        //  Ensure Post Commit Function is Registered
        if (!isset(self::$postCommit)) {
            $callBack = array(self::class,"postCommit");
            if (!is_callable($callBack)) {
                return false;
            }
            register_shutdown_function($callBack);
            self::$postCommit = true;
        }

        return true;
    }
}
