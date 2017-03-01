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
     *      @abstract      Check Connexion with NuSOAP Client
     *      @param      int     $silent     No message display if non errors
     *      @return     int         	0 if KO, 1 if OK
     */
    public static function Ping($silent=0)
    {
        //====================================================================//
        // Stack Trace
        Splash::Log()->Trace(__CLASS__,__FUNCTION__);              
        //====================================================================//
        // Initiate Performance Timer
        if (self::Configuration()->TraceTasks) {    $timer_init = microtime(TRUE);      }
        
        //====================================================================//
        // Run NuSOAP Call 
        $r = self::Ws()->Call(SPL_S_PING,NULL,1);
        
        //====================================================================//
        //  Messages Debug Informations
        //====================================================================//
        if (self::Configuration()->TraceTasks) {
            $total = sprintf("%.2f %s", 1000 * (microtime(TRUE) - $timer_init), " ms");
            self::Log()->War("===============================================");
            self::Log()->War("OsWs - Ping : " . $total);
        }        
        
        //====================================================================//
        // Analyze NuSOAP results
        if ( isset ($r->result) && ( $r->result ==  True ) && ($silent) ) {
            self::Log()->Cleanlog();
            return True;
        }
        //====================================================================//
        // If Not Silent, Display result
        else if ( isset ($r->result) && ($r->result == True) ) {
            return self::Log()->Msg("Remote Client Ping Passed (" . self::Ws()->url . ")");
        } else {
            return self::Log()->Err("Remote Client Ping Failled (" . self::Ws()->url . ")");
        }
    }
    
//--------------------------------------------------------------------//
//--------------------------------------------------------------------//
//----  CONNECT WEBSERVICE FUNCTIONS                              ----//
//--------------------------------------------------------------------//
//--------------------------------------------------------------------//
       
    /**
     *      @abstract      Check Connexion with NuSOAP Client
     *      @param      int     $silent     No message display if non errors
     *      @return     int                 0 if KO, 1 if OK
     */
    public static function Connect($silent = False)
    {
        //====================================================================//
        // Stack Trace
        Splash::Log()->Trace(__CLASS__,__FUNCTION__);              
        //====================================================================//
        // Initiate Performance Timer
        if (self::Configuration()->TraceTasks) {    $timer_init = microtime(TRUE);      }
        //====================================================================//
        // Run NuSOAP Call 
        $r = self::Ws()->Call(SPL_S_CONNECT);
        //====================================================================//
        //  Messages Debug Informations
        //====================================================================//
        if (self::Configuration()->TraceTasks) {
            $total = sprintf("%.2f %s", 1000 * (microtime(TRUE) - $timer_init), " ms");
            self::Log()->War("===============================================");
            self::Log()->War("OsWs - Connect : " . $total);
        }        
        //====================================================================//
        // Analyze NuSOAP results
        if ( !isset ($r->result) || ($r->result != True) ) {
            return self::Log()->Err("Remote Client Connection Failled (" . self::Ws()->url . ")");
        }
        //====================================================================//
        // If Not Silent, Display result
        if ($silent) {
            self::Log()->Cleanlog();
        }
        return True;
    }
    
//--------------------------------------------------------------------//
//--------------------------------------------------------------------//
//---- USER MAIN FUNCTIONS                                        ----//
//--------------------------------------------------------------------//
//--------------------------------------------------------------------//

    /**
     *   @abstract     Submit to OsWs Module an Update for a Local Object
     *   @param        array        $ObjectType        OsWs Object Type Name. 
     *   @param        int/array    $local             Object Local Id or Array of Local Id. Only if already synchronized localy 
     *   @param        int          $action            Action Type On this Object (OSWS_A_UPDATE, or OSWS_A_CREATE, or OSWS_A_DELETE) 
     *   @param        string       $user              User Name
     *   @param        string       $comment           Operation Comment for Historics 
     *   @return       int                          0 if KO, 1 if OK, 2 if object Exist and Force == 0    
     */
    public static function Commit($ObjectType,$local=NULL,$action=NULL,$user="",$comment="")
    {
        //====================================================================//
        // Stack Trace
        self::Log()->Trace(__CLASS__,__FUNCTION__);  
        
        //====================================================================//
        // Verify this Object Class is Valid ==> No Action on this Node
        if (Splash::Object($ObjectType) == False)    {
            return True;
        }
        
        //====================================================================//
        // Initiate Tasks parameters array 
        $params                 = new ArrayObject(array(),  ArrayObject::ARRAY_AS_PROPS);
        $params->type           = $ObjectType;                              // Type of the Object
        $params->id             = $local;                                   // Id of Modified object
        $params->action         = $action;                                  // Action Type On this Object 
        $params->user           = $user;                                    // Operation User Name for Historics 
        $params->comment        = $comment;                                 // Operation Comment for Historics 

        //====================================================================//
        // Add This Commit to Session Logs
        static::$Commited[] = $params;
        
        //====================================================================//
        // Verify this Object is Locked ==> No Action on this Node
        //====================================================================//
        if ( is_array($local) || is_a($local, "ArrayObject") ) {
            foreach ($local as $value) {
                if (Splash::Object($ObjectType)->isLocked($value))    {
                    return True;
                }
            }
        } else {
            if (Splash::Object($ObjectType)->isLocked($local))    {
                return True;
            }
        }
        
        //====================================================================//
        // Verify Create Object is Locked ==> No Action on this Node
        if ( ($action === SPL_A_CREATE) && Splash::Object($ObjectType)->isLocked())    {
            return True;
        }        
        //====================================================================//
        // Add Task to Ws Task List
        Splash::Ws()->AddTask(SPL_F_COMMIT, $params, Splash::Trans("MsgSchRemoteCommit",$action,$ObjectType,count($local)) );
        
        //====================================================================//
        // Execute Task
        $Response   =   self::Ws()->Call(SPL_S_OBJECTS);
        
        //====================================================================//
        // Analyze NuSOAP results
        if ( !isset ($Response->result) || ($Response->result != True) ) {
            return False;
        }        
        
        return True;
    }    

}

?>