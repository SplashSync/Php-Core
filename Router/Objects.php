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

namespace   Splash\Router;

use Exception;
use Splash\Components\Router;
use Splash\Core\SplashCore      as Splash;
use Splash\Models\Objects\ObjectInterface;
use Splash\Models\Objects\PrimaryKeysAwareInterface;

/**
 * Server Request Routing Class, Execute/Route actions on Objects Service Requests.
 * This file is included only in case on NuSOAP call to slave server.
 */
class Objects implements RouterInterface
{
    /**
     * {@inheritDoc}
     *
     * @throws Exception
     */
    public static function action(array $task): ?array
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();
        Splash::log()->deb("Object => ".$task['name']." (".$task['desc'].")");

        //====================================================================//
        //  READING OF SERVER OBJECT LIST
        //====================================================================//
        if (SPL_F_OBJECTS === $task['name']) {
            return self::doObjects($task);
        }

        //====================================================================//
        // Safety Check - Minimal Parameters
        //====================================================================//
        // Verify Requested Object Type is Available
        if (!self::isValidTask($task)) {
            return Router::getEmptyResponse($task);
        }

        //====================================================================//
        // Execute Admin Actions
        //====================================================================//
        if (in_array($task['name'], array( SPL_F_DESC , SPL_F_FIELDS , SPL_F_LIST ), true)) {
            return self::doAdminActions($task);
        }
        if (in_array($task['name'], array( SPL_F_GET , SPL_F_SET , SPL_F_DEL ), true)) {
            return self::doSyncActions($task);
        }
        if (in_array($task['name'], array( SPL_F_IDENTIFY ), true)) {
            return self::doPrimaryActions($task);
        }
        if (SPL_F_COMMIT === $task['name']) {
            Splash::log()->war("Objects - Requested task not found => ".$task['name']);

            return Router::getEmptyResponse($task);
        }
        //====================================================================//
        // Task Not Found
        Splash::log()->err("Objects - Requested task not found => ".$task['name']);

        return self::checkResponse(Router::getEmptyResponse($task));
    }

    //====================================================================//
    //  LOW LEVEL FUNCTIONS
    //====================================================================//

    /**
     * @param array $response
     *
     * @return array
     */
    private static function checkResponse(array $response): array
    {
        $response['result'] = !empty($response['data']);

        return $response;
    }

    /**
     * Verify Received Task
     *
     * @param array $task Full Task Request Array
     *
     * @return bool
     */
    private static function isValidTask(array $task): bool
    {
        //====================================================================//
        // Verify Requested Object Type is Available
        if (empty($task['params'])) {
            Splash::log()->err("Object Router - Missing Task Parameters... ");

            return false;
        }
        //====================================================================//
        // Verify Requested Object Type is Available
        if (empty($task['params']['type'])) {
            Splash::log()->err("Object Router - Missing Object Type... ");

            return false;
        }
        //====================================================================//
        // Verify Requested Object Type is Valid
        if (true != Splash::validate()->isValidObject($task['params']['type'])) {
            Splash::log()->err("Object Router - Object Type is Invalid... ");

            return false;
        }

        return true;
    }

    /**
     * Execute Objects Admin Actions
     *
     * @param array $task
     *
     * @throws Exception
     *
     * @return array
     */
    private static function doAdminActions(array $task): array
    {
        //====================================================================//
        // Initial Response
        $response = Router::getEmptyResponse($task);
        //====================================================================//
        // Load Parameters
        $objectClass = Splash::object($task['params']['type']);
        //====================================================================//
        // Execute Requested Task
        //====================================================================//
        switch ($task['name']) {
            case SPL_F_DESC:
                //====================================================================//
                //  READING OF Object Description
                //====================================================================//
                $response['data'] = $objectClass->description();

                break;
            case SPL_F_FIELDS:
                //====================================================================//
                //  READING OF Available Fields
                //====================================================================//
                $response['data'] = $objectClass->fields();

                break;
            case SPL_F_LIST:
                //====================================================================//
                //  READING OF OBJECT LIST
                //====================================================================//
                $filters = $task['params']['filters'] ?? null;
                $params = $task['params']['params'] ?? array();
                $response['data'] = $objectClass->objectsList($filters, $params ?: array());

                break;
        }

        return self::checkResponse($response);
    }

    /**
     * Execute Objects Sync Actions
     *
     * @param array $task
     *
     * @throws Exception
     *
     * @return array
     */
    private static function doSyncActions(array $task): array
    {
        //====================================================================//
        // Initial Response
        $response = Router::getEmptyResponse($task);

        //====================================================================//
        // Load Parameters
        $objectClass = Splash::object($task['params']['type']);
        $objectId = $task['params']['id'] ?? null;
        $fields = $task['params']['fields'] ?? null;

        //====================================================================//
        // Verify Object Id
        if (!Splash::validate()->isValidObjectId($objectId)) {
            return $response;
        }

        //====================================================================//
        // Execute Requested Task
        //====================================================================//
        switch ($task['name']) {
            case SPL_F_GET:
                //====================================================================//
                //  READING OF OBJECT DATA
                //====================================================================//
                $response['data'] = self::doGet($objectClass, $objectId, $fields);

                break;
            case SPL_F_SET:
                //====================================================================//
                //  WRITING OF OBJECT DATA
                //====================================================================//
                $response['data'] = self::doSet($objectClass, $objectId, $fields);

                break;
            case SPL_F_DEL:
                //====================================================================//
                //  DELETE OF AN OBJECT
                //====================================================================//
                $response['data'] = self::doDelete($objectClass, $objectId);

                break;
        }

        return self::checkResponse($response);
    }

    /**
     * Execute Objects Primary Keys Actions
     *
     * @param array $task
     *
     * @throws Exception
     *
     * @return array
     */
    private static function doPrimaryActions(array $task): array
    {
        //====================================================================//
        // Initial Response
        $response = Router::getEmptyResponse($task);

        //====================================================================//
        // Load Parameters
        $objectClass = Splash::object($task['params']['type']);
        $keys = $task['params']['keys'] ?? null;

        //====================================================================//
        // Execute Requested Task
        //====================================================================//
        switch ($task['name']) {
            case SPL_F_IDENTIFY:
                //====================================================================//
                //  IDENTIFY OBJECT BY PRIMARY KEYS
                //====================================================================//
                $response['data'] = self::doIdentify($objectClass, $keys);
                $response['result'] = true;

                return $response;
        }

        return self::checkResponse($response);
    }

    /**
     * Get Objects List Action
     *
     * @param array $task
     *
     * @throws Exception
     *
     * @return array
     */
    private static function doObjects(array $task): array
    {
        //====================================================================//
        // Initial Response
        $response = Router::getEmptyResponse($task);
        //====================================================================//
        // Read Objects Types List from Local System
        $response['data'] = Splash::objects();
        //====================================================================//
        // Return Response
        return self::checkResponse($response);
    }

    /**
     * Do Object Reading Action
     *
     * @param ObjectInterface $objectClass
     * @param null|string     $objectId
     * @param array           $fields
     *
     * @return null|array
     */
    private static function doGet(ObjectInterface $objectClass, ?string $objectId, array $fields): ?array
    {
        //====================================================================//
        // Verify Object Field List
        if (!$objectId || !Splash::validate()->isValidObjectFieldsList($fields)) {
            return null;
        }

        //====================================================================//
        // Read Data from local system
        return  $objectClass->get($objectId, $fields);
    }

    /**
     * Do Object Writing Action
     *
     * @param ObjectInterface $objectClass
     * @param string          $objectId
     * @param array           $fields
     *
     * @return null|string
     */
    private static function doSet(ObjectInterface $objectClass, string $objectId, array $fields): ?string
    {
        //====================================================================//
        // Take Lock for this object => No Commit Allowed for this Object
        $objectClass->lock($objectId);
        //====================================================================//
        // Write Data on local system
        $objectData = $objectClass->set($objectId, $fields);
        //====================================================================//
        // Release Lock for this object
        $objectClass->unLock($objectId);
        //====================================================================//
        // Return Response
        return $objectData;
    }

    /**
     * Do Object Delete Action
     *
     * @param ObjectInterface $objectClass
     * @param null|string     $objectId
     *
     * @return bool
     */
    private static function doDelete(ObjectInterface $objectClass, ?string $objectId): bool
    {
        //====================================================================//
        // Safety Check
        if (!$objectId) {
            return false;
        }
        //====================================================================//
        // Take Lock for this object => No Commit Allowed for this Object
        $objectClass->lock($objectId);
        //====================================================================//
        // Delete Data on local system
        return $objectClass->delete($objectId);
    }

    /**
     * Do Object Identification by Primary Keys
     *
     * @param ObjectInterface       $objectClass
     * @param array<string, string> $keys        Primary Keys List
     *
     * @return null|string
     */
    private static function doIdentify(ObjectInterface $objectClass, array $keys): ?string
    {
        //====================================================================//
        // Verify Object is Primary Keys Aware
        if (!$objectClass instanceof PrimaryKeysAwareInterface) {
            return null;
        }
        //====================================================================//
        // Read Data from local system
        return  $objectClass->getByPrimary($keys);
    }
}
