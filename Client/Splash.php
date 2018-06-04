<?php
/*
 * This file is part of SplashSync Project.
 *
 * Copyright (C) Splash Sync <www.splashsync.com>
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @abstract    Main User Client Class for Using Splash Webservice Module
 * @author      B. Paquier <contact@splashsync.com>
 */

namespace Splash\Client;

use Splash\Core\SplashCore;
use ArrayObject;

//====================================================================//
//   INCLUDES
//====================================================================//

////====================================================================//
//// Include Splash Core Class
//require_once("SplashCore.php");

//====================================================================//
//********************************************************************//
//====================================================================//
//  SPLASH BASE CLASS
//====================================================================//
//********************************************************************//
//====================================================================//

class Splash extends SplashCore
{
    /**
     * @abstract    list of all Commits done inside this current session
     * @var         array
     */
    public static $Commited = array();
    
    //--------------------------------------------------------------------//
    //--------------------------------------------------------------------//
    //----  PING WEBSERVICE FUNCTIONS                                 ----//
    //--------------------------------------------------------------------//
    //--------------------------------------------------------------------//
   
    /**
     * @abstract      Check Connexion with NuSOAP Client
     * @param       bool    $silent     No message display if non errors
     * @return      bool
     */
    public static function ping($silent = false)
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace(__CLASS__, __FUNCTION__);
        //====================================================================//
        // Initiate Performance Timer
        if (self::configuration()->TraceTasks) {
            $timer_init = microtime(true);
        }
        
        //====================================================================//
        // Run NuSOAP Call
        $Result = self::ws()->call(SPL_S_PING, null, 1);
        
        //====================================================================//
        //  Messages Debug Informations
        //====================================================================//
        if (self::configuration()->TraceTasks) {
            $total = sprintf("%.2f %s", 1000 * (microtime(true) - $timer_init), " ms");
            self::log()->war("===============================================");
            self::log()->war("OsWs - Ping : " . $total);
        }
        
        //====================================================================//
        // Analyze NuSOAP results
        if (isset($Result->result) && ($Result->result ==  true) && ($silent)) {
            self::log()->cleanLog();
            return true;
        } //====================================================================//
        // If Not Silent, Display result
        elseif (isset($Result->result) && ($Result->result == true)) {
            return self::log()->msg("Remote Client Ping Passed (" . self::ws()->url . ")");
        } else {
            return self::log()->err("Remote Client Ping Failed (" . self::ws()->url . ")");
        }
    }
    
    //--------------------------------------------------------------------//
    //--------------------------------------------------------------------//
    //----  CONNECT WEBSERVICE FUNCTIONS                              ----//
    //--------------------------------------------------------------------//
    //--------------------------------------------------------------------//
       
    /**
     * @abstract    Check Connexion with NuSOAP Client
     * @param       bool    $silent     No message display if non errors
     * @return      bool
     */
    public static function connect($silent = false)
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace(__CLASS__, __FUNCTION__);
        //====================================================================//
        // Initiate Performance Timer
        if (self::configuration()->TraceTasks) {
            $timer_init = microtime(true);
        }
        //====================================================================//
        // Run NuSOAP Call
        $Result = self::ws()->call(SPL_S_CONNECT);
        //====================================================================//
        //  Messages Debug Informations
        //====================================================================//
        if (self::configuration()->TraceTasks) {
            $total = sprintf("%.2f %s", 1000 * (microtime(true) - $timer_init), " ms");
            self::log()->war("===============================================");
            self::log()->war("OsWs - Connect : " . $total);
        }
        //====================================================================//
        // Analyze NuSOAP results
        if (!isset($Result->result) || ($Result->result != true)) {
            return self::log()->err("Remote Client Connection Failed (" . self::ws()->url . ")");
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
     *   @abstract     Submit an Update for a Local Object
     *   @param        string       $ObjectType        Object Type Name.
     *   @param        int/array    $local             Object Local Id or Array of Local Id.
     *   @param        int          $action            Action Type (SPL_A_UPDATE, or SPL_A_CREATE, or SPL_A_DELETE)
     *   @param        string       $user              User Name
     *   @param        string       $comment           Operation Comment for Historics
     *   @return       bool
     */
    public static function commit($ObjectType, $local = null, $action = null, $user = "", $comment = "")
    {
        //====================================================================//
        // Stack Trace
        self::log()->trace(__CLASS__, __FUNCTION__ . " (" . $action . ", " . $ObjectType . ")");
        
        //====================================================================//
        // Verify this Object Class is Valid ==> No Action on this Node
        if (Splash::object($ObjectType) == false) {
            return true;
        }
        
        //====================================================================//
        // Initiate Tasks parameters array
        $params                 = self::getCommitParameters($ObjectType, $local, $action, $user, $comment);

        //====================================================================//
        // Add This Commit to Session Logs
        static::$Commited[] = $params;
        
        //====================================================================//
        // Verify this Object is Locked ==> No Action on this Node
        if (!self::isCommitAllowed($ObjectType, $local, $action)) {
            return true;
        }
        
        //====================================================================//
        // Add Task to Ws Task List
        Splash::ws()->addTask(
            SPL_F_COMMIT,
            $params,
            Splash::trans("MsgSchRemoteCommit", $action, $ObjectType, count($local))
        );
        
        //====================================================================//
        // Execute Task
        $Response   =   self::ws()->call(SPL_S_OBJECTS);
        
        //====================================================================//
        // Analyze NuSOAP results
        return self::isCommitSuccess($Response);
    }
    
    /**
     *   @abstract     Build Call Parameters Array
     *   @param        array        $ObjectType        Object Type Name.
     *   @param        int/array    $local             Object Local Id or Array of Local Id.
     *   @param        int          $action            Action Type (SPL_A_UPDATE, or SPL_A_CREATE, or SPL_A_DELETE)
     *   @param        string       $user              User Name
     *   @param        string       $comment           Operation Comment for Historics
     *   @return       array
     */
    private static function getCommitParameters($ObjectType, $local = null, $action = null, $user = "", $comment = "")
    {
        $params                 = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        $params->type           = $ObjectType;                              // Type of the Object
        $params->id             = $local;                                   // Id of Modified object
        $params->action         = $action;                                  // Action Type On this Object
        $params->user           = $user;                                    // Operation User Name for Historics
        $params->comment        = $comment;                                 // Operation Comment for Historics
        return $params;
    }
    

    /**
     *   @abstract     Check if Commity is Allowed Local Object
     *   @param        array        $ObjectType        Object Type Name.
     *   @param        int/array    $local             Object Local Id or Array of Local Id.
     *   @param        int          $action            Action Type (SPL_A_UPDATE, or SPL_A_CREATE, or SPL_A_DELETE)
     *   @return       bool
     */
    private static function isCommitAllowed($ObjectType, $local = null, $action = null)
    {
        //====================================================================//
        // Verify this Object is Locked ==> No Action on this Node
        //====================================================================//
        if (is_array($local) || is_a($local, "ArrayObject")) {
            foreach ($local as $value) {
                if (Splash::object($ObjectType)->isLocked($value)) {
                    return false;
                }
            }
        } else {
            if (Splash::object($ObjectType)->isLocked($local)) {
                return false;
            }
        }
        //====================================================================//
        // Verify Create Object is Locked ==> No Action on this Node
        if (($action === SPL_A_CREATE) && Splash::object($ObjectType)->isLocked()) {
            return false;
        }
        
        return true;
    }
    
    /**
     *   @abstract     Check if Commit Call was Successful
     *   @param        ArrayObject      $Response       Splash Server Response
     *   @return       bool
     */
    public static function isCommitSuccess($Response)
    {
        //====================================================================//
        // Analyze NuSOAP results
        if (!isset($Response->result) || ($Response->result != true)) {
            return false;
        }
        return true;
    }
}
