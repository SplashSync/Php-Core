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

/**
 * Server Request Routiung Class, Execute/Route actions on Objects Service Requests.
 * This file is included only in case on NuSOAP call to slave server.
 *
 * @author      B. Paquier <contact@splashsync.com>
 */

namespace   Splash\Router;

use ArrayObject;
use Splash\Core\SplashCore      as Splash;
use Splash\Models\AbstractObject;

//====================================================================//
//  CLASS DEFINITION
//====================================================================//

/**
 * Splash Server Objects Service Router
 */
class Objects
{
    /**
     * Task execution router. Receive task detail and execute requiered task operations.
     *
     * @param ArrayObject $task Full Task Request Array
     *
     * @return ArrayObject Task results, or False if KO
     */
    public static function action($task)
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();
        Splash::log()->deb("Object => ".$task->name." (".$task->desc.")");

        //====================================================================//
        //  READING OF SERVER OBJECT LIST
        //====================================================================//
        if (SPL_F_OBJECTS === $task->name) {
            return self::doObjects($task);
        }

        //====================================================================//
        // Safety Check - Minimal Parameters
        //====================================================================//
        // Verify Requested Object Type is Available
        if (!self::isValidTask($task)) {
            return self::getEmptyResponse($task);
        }

        //====================================================================//
        // Execute Admin Actions
        //====================================================================//
        if (in_array($task->name, array( SPL_F_DESC , SPL_F_FIELDS , SPL_F_LIST ), true)) {
            return self::doAdminActions($task);
        }
        if (in_array($task->name, array( SPL_F_GET , SPL_F_SET , SPL_F_DEL ), true)) {
            return self::doSyncActions($task);
        }
        if (in_array($task->name, array( SPL_F_COMMIT ), true)) {
            Splash::log()->war("Objects - Requested task not found => ".$task->name);

            return self::getEmptyResponse($task);
        }
        //====================================================================//
        // Task Not Found
        Splash::log()->err("Objects - Requested task not found => ".$task->name);

        return self::checkResponse(self::getEmptyResponse($task));
    }

    //====================================================================//
    //  LOW LEVEL FUNCTIONS
    //====================================================================//

    /**
     * Build an Empty Task Response
     *
     * @param ArrayObject $task Task To Execute
     *
     * @return ArrayObject Task Result ArrayObject
     */
    private static function getEmptyResponse($task)
    {
        //====================================================================//
        // Initial Tasks results ArrayObject
        $response = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);

        //====================================================================//
        // Set Default Result to False
        $response->result = false;
        $response->data = null;

        //====================================================================//
        // Insert Task Description Informations
        $response->name = $task->name;
        $response->desc = $task->desc;

        return $response;
    }

    /**
     * @param ArrayObject $response
     *
     * @return ArrayObject
     */
    private static function checkResponse($response)
    {
        if (false != $response->data) {
            $response->result = true;
        }

        return $response;
    }

    /**
     * Verify Received Task
     *
     * @param ArrayObject $task Full Task Request Array
     *
     * @return bool
     */
    private static function isValidTask($task)
    {
        //====================================================================//
        // Verify Requested Object Type is Available
        if (empty($task->params)) {
            Splash::log()->err("Object Router - Missing Task Parameters... ");

            return false;
            //====================================================================//
        // Verify Requested Object Type is Available
        }
        if (empty($task->params->type)) {
            Splash::log()->err("Object Router - Missing Object Type... ");

            return false;
            //====================================================================//
        // Verify Requested Object Type is Valid
        }
        if (true != Splash::validate()->isValidObject($task->params->type)) {
            Splash::log()->err("Object Router - Object Type is Invalid... ");

            return false;
        }

        return true;
    }

    /**
     * @param ArrayObject $task
     *
     * @return ArrayObject
     */
    private static function doAdminActions($task)
    {
        //====================================================================//
        // Initial Response
        $response = self::getEmptyResponse($task);

        //====================================================================//
        // Load Parameters
        $objectClass = Splash::object($task->params->type);

        //====================================================================//
        // Execute Requested Task
        //====================================================================//
        switch ($task->name) {
            //====================================================================//
            //  READING OF Object Description
            //====================================================================//
            case SPL_F_DESC:
                $response->data = $objectClass->description();

                break;
            //====================================================================//
            //  READING OF Available Fields
            //====================================================================//
            case SPL_F_FIELDS:
                $response->data = $objectClass->fields();

                break;
            //====================================================================//
            //  READING OF OBJECT LIST
            //====================================================================//
            case SPL_F_LIST:
                $filters = isset($task->params->filters) ?   $task->params->filters  : null;
                $params = isset($task->params->params)  ?   $task->params->params   : null;
                $response->data = $objectClass->objectsList($filters, $params);

                break;
        }

        return self::checkResponse($response);
    }

    /**
     * @param ArrayObject $task
     *
     * @return ArrayObject
     */
    private static function doSyncActions($task)
    {
        //====================================================================//
        // Initial Response
        $response = self::getEmptyResponse($task);

        //====================================================================//
        // Load Parameters
        $objectClass = Splash::object($task->params->type);
        $objectId = isset($task->params->id)      ?   $task->params->id       : null;
        $fields = isset($task->params->fields)  ?   $task->params->fields   : null;

        //====================================================================//
        // Verify Object Id
        if (!Splash::validate()->isValidObjectId($objectId)) {
            return $response;
        }

        //====================================================================//
        // Execute Requested Task
        //====================================================================//
        switch ($task->name) {
            //====================================================================//
            //  READING OF OBJECT DATA
            //====================================================================//
            case SPL_F_GET:
                $response->data = self::doGet($objectClass, $objectId, $fields);

                break;
            //====================================================================//
            //  WRITTING OF OBJECT DATA
            //====================================================================//
            case SPL_F_SET:
                $response->data = self::doSet($objectClass, $objectId, $fields);

                break;
            //====================================================================//
            //  DELETE OF AN OBJECT
            //====================================================================//
            case SPL_F_DEL:
                $response->data = self::doDelete($objectClass, $objectId);

                break;
        }

        return self::checkResponse($response);
    }

    /**
     * @param ArrayObject $task
     *
     * @return ArrayObject
     */
    private static function doObjects($task)
    {
        //====================================================================//
        // Initial Response
        $response = self::getEmptyResponse($task);

        //====================================================================//
        // Read Objects Types List from Local System
        $response->data = Splash::objects();

        //====================================================================//
        // Return Response
        return self::checkResponse($response);
    }

    /**
     * @param AbstractObject $objectClass
     * @param null|string    $objectId
     * @param array          $fields
     *
     * @return array|false
     */
    private static function doGet(&$objectClass, $objectId, $fields)
    {
        //====================================================================//
        // Verify Object Field List
        if (!Splash::validate()->isValidObjectFieldsList($fields)) {
            return false;
        }

        //====================================================================//
        // Read Data fron local system
        return  $objectClass->get($objectId, $fields);
    }

    /**
     * @param AbstractObject $objectClass
     * @param string         $objectId
     * @param array          $fields
     *
     * @return false|string
     */
    private static function doSet(&$objectClass, $objectId, $fields)
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
     * @param AbstractObject $objectClass
     * @param null|string    $objectId
     *
     * @return bool
     */
    private static function doDelete(&$objectClass, $objectId)
    {
        //====================================================================//
        // Take Lock for this object => No Commit Allowed for this Object
        $objectClass->lock($objectId);

        //====================================================================//
        // Delete Data on local system
        return $objectClass->delete($objectId);
    }
}
