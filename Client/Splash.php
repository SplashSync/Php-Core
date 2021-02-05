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

namespace Splash\Client;

use ArrayObject;
use Splash\Core\SplashCore;

/**
 * Main User Client Class for Using Splash Webservice Module
 *
 * @author      B. Paquier <contact@splashsync.com>
 */
class Splash extends SplashCore
{
    /**
     * list of all Commits done inside this current session
     *
     * @var array
     */
    public static $commited = array();

    //--------------------------------------------------------------------//
    //--------------------------------------------------------------------//
    //----  PING WEBSERVICE FUNCTIONS                                 ----//
    //--------------------------------------------------------------------//
    //--------------------------------------------------------------------//

    /**
     * Check Connexion with NuSOAP Client
     *
     * @param bool $silent No message display if non errors
     *
     * @return bool
     */
    public static function ping($silent = false)
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();
        //====================================================================//
        // Initiate Performance Timer
        $intTimer = microtime(true);
        //====================================================================//
        // Run NuSOAP Call
        $result = self::ws()->call(SPL_S_PING, null, true);
        //====================================================================//
        //  Messages Debug Informations
        //====================================================================//
        if (self::configuration()->TraceTasks) {
            $total = sprintf('%.2f %s', 1000 * (microtime(true) - $intTimer), ' ms');
            self::log()->war('===============================================');
            self::log()->war('Splash - Ping : '.$total);
        }
        //====================================================================//
        // Analyze NuSOAP results
        if ($result && isset($result->result) && (true == $result->result) && ($silent)) {
            self::log()->cleanLog();

            return true;
        }
        //====================================================================//
        // If Not Silent, Display result
        if ($result && isset($result->result) && (true == $result->result)) {
            return self::log()->msg('Remote Client Ping Passed ('.self::ws()->url.')');
        }

        return self::log()->err('Remote Client Ping Failed ('.self::ws()->url.')');
    }

    //--------------------------------------------------------------------//
    //--------------------------------------------------------------------//
    //----  CONNECT WEBSERVICE FUNCTIONS                              ----//
    //--------------------------------------------------------------------//
    //--------------------------------------------------------------------//

    /**
     * Check Connexion with NuSOAP Client
     *
     * @param bool $silent No message display if non errors
     *
     * @return bool
     */
    public static function connect($silent = false)
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();
        //====================================================================//
        // Initiate Performance Timer
        $initTimer = microtime(true);
        //====================================================================//
        // Run NuSOAP Call
        $result = self::ws()->call(SPL_S_CONNECT);
        //====================================================================//
        //  Messages Debug Informations
        //====================================================================//
        if (self::configuration()->TraceTasks) {
            $total = sprintf('%.2f %s', 1000 * (microtime(true) - $initTimer), ' ms');
            self::log()->war('===============================================');
            self::log()->war('Splash - Connect : '.$total);
        }
        //====================================================================//
        // Analyze NuSOAP results
        if (!$result || !isset($result->result) || (true != $result->result)) {
            return self::log()->err('Remote Client Connection Failed ('.self::ws()->url.')');
        }
        //====================================================================//
        // If Not Silent, Display result
        if ($silent) {
            self::log()->cleanLog();
        }

        return true;
    }

    //--------------------------------------------------------------------//
    //--------------------------------------------------------------------//
    //---- USER MAIN FUNCTIONS                                        ----//
    //--------------------------------------------------------------------//
    //--------------------------------------------------------------------//

    /**
     * Submit an Update for a Local Object
     *
     * @param string           $objectType object Type Name
     * @param array|int|string $local      object Local Id or Array of Local Id
     * @param string           $action     Action Type (SPL_A_UPDATE, or SPL_A_CREATE, or SPL_A_DELETE)
     * @param string           $user       User Name
     * @param string           $comment    Operation Comment for Historics
     *
     * @return bool
     */
    public static function commit($objectType, $local = null, $action = null, $user = '', $comment = '')
    {
        //====================================================================//
        // Stack Trace
        self::log()->trace();
        //====================================================================//
        // Verify this Object Class is Valid ==> No Action on this Node
        if (false == Splash::object($objectType)) {
            return true;
        }
        //====================================================================//
        // Initiate Tasks parameters array
        $params = self::getCommitParameters($objectType, $local, $action, $user, $comment);
        //====================================================================//
        // Add This Commit to Session Logs
        static::$commited[] = $params;
        //====================================================================//
        // Verify this Object is Locked ==> No Action on this Node
        if (!self::isCommitAllowed($objectType, $local, $action)) {
            return true;
        }
        //====================================================================//
        // Add Task to Ws Task List
        Splash::ws()->addTask(
            SPL_F_COMMIT,
            $params,
            Splash::trans('MsgSchRemoteCommit', (string) $action, $objectType, (string) Splash::count($local))
        );
        //====================================================================//
        // Execute Task
        $response = self::ws()->call(SPL_S_OBJECTS);
        //====================================================================//
        // Analyze NuSOAP results
        return self::isCommitSuccess($response);
    }

    /**
     * @abstract     Check if Commit Call was Successful
     *
     * @param ArrayObject|false $response Splash Server Response
     *
     * @return bool
     */
    public static function isCommitSuccess($response)
    {
        //====================================================================//
        // Analyze NuSOAP results
        if (!$response || !isset($response->result) || (true != $response->result)) {
            return false;
        }
        //====================================================================//
        //  Smart Notifications => Filter Messages, Only Warnings & Errors
        if (self::configuration()->SmartNotify) {
            self::log()->smartFilter();
        }

        return true;
    }

    /**
     * Build Call Parameters Array
     *
     * @param string           $objectType object Type Name
     * @param array|int|string $local      object Local Id or Array of Local Id
     * @param string           $action     Action Type (SPL_A_UPDATE, or SPL_A_CREATE, or SPL_A_DELETE)
     * @param string           $user       User Name
     * @param string           $comment    Operation Comment for Historics
     *
     * @return arrayObject
     */
    private static function getCommitParameters($objectType, $local = null, $action = null, $user = '', $comment = '')
    {
        $params = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        $params->type = $objectType;        // Type of the Object
        $params->id = $local;               // Id of Modified object
        $params->action = $action;          // Action Type On this Object
        $params->user = $user;              // Operation User Name for Historics
        $params->comment = $comment;        // Operation Comment for Historics

        return $params;
    }

    /**
     * Check if Commit is Allowed Local Object
     *
     * @param string           $objectType object Type Name
     * @param array|int|string $local      object Local Id or Array of Local Id
     * @param string           $action     Action Type (SPL_A_UPDATE, or SPL_A_CREATE, or SPL_A_DELETE)
     *
     * @return bool
     */
    private static function isCommitAllowed($objectType, $local = null, $action = null)
    {
        //====================================================================//
        // Verify this Object is Locked ==> No Action on this Node
        //====================================================================//
        if (is_array($local)) {
            foreach ($local as $value) {
                if (Splash::object($objectType)->isLocked($value)) {
                    return false;
                }
            }
        } else {
            if (Splash::object($objectType)->isLocked($local)) {
                return false;
            }
        }
        //====================================================================//
        // Verify Create Object is Locked ==> No Action on this Node
        if ((SPL_A_CREATE === $action) && Splash::object($objectType)->isLocked()) {
            return false;
        }
        //====================================================================//
        // Verify if Travis Mode (PhpUnit) ==> No Commit Allowed
        return !self::isTravisMode($objectType, $local, $action);
    }

    /**
     * Check if Commit we Are in Travis Mode
     *
     * @param string                $objectType object Type Name
     * @param null|array|int|string $local      object Local Id or Array of Local Id
     * @param string                $action     Action Type (SPL_A_UPDATE, or SPL_A_CREATE, or SPL_A_DELETE)
     *
     * @return bool
     */
    private static function isTravisMode($objectType, $local, $action = null)
    {
        //====================================================================//
        // Detect Travis from SERVER CONSTANTS
        if (empty(Splash::input('SPLASH_TRAVIS'))) {
            return false;
        }
        $objectIds = is_array($local) ? implode('|', $local) : $local;
        self::log()->war('Module Commit Skipped ('.$objectType.', '.$action.', '.$objectIds.')');

        return true;
    }
}
