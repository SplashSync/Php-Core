<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2019 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

/**
 * @abstract    Splash Sync Server. Manage Splash Requests & Responses.
 *              This file is included only in case on NuSOAP call to slave server.
 *
 * @author      B. Paquier <contact@splashsync.com>
 */

namespace Splash\Server;

use ArrayObject;
use Splash\Core\SplashCore  as Splash;

//====================================================================//
//  CLASS DEFINITION
//====================================================================//
 
class SplashServer
{
    //====================================================================//
    // Webservice I/O Buffers
    //====================================================================//
    private static $Inputs;         // Input Buffer
    private static $Outputs;        // Output Buffer
    
    /**
     * @abstract       Class Constructor
     */
    public function __construct()
    {
        self::init();
    }
    
    //====================================================================//
    //  WEBSERVICE REGISTERED REQUEST FUNCTIONS
    //====================================================================//
    
    /**
     *      @abstract      Minimal Test of Webservice connexion
     *
     *      @return        mixed    WebService Packaged Data Outputs or NUSOAP Error
     */
    public static function ping()
    {
        self::init();
        
        //====================================================================//
        // Simple Message reply, No Encryption
        Splash::log()->msg("Ping Successful.");
        self::$Outputs->result  = true;
        
        //====================================================================//
        // Transmit Answer with No Encryption
        return Splash::ws()->pack(self::$Outputs, true);
    }
    
    /**
     *      @abstract      Connect Webservice and fetch server informations
     *
     *      @param         string   $id         OsWs WebService Node Identifier
     *      @param         string   $data       OsWs WebService Packaged Data Inputs
     *
     *      @return        mixed    WebService Packaged Data Outputs or NUSOAP Error
     */
    public static function connect($id, $data)
    {
        //====================================================================//
        // Verify Node Id
        //====================================================================//
        if (Splash::configuration()->WsIdentifier !== $id) {
            return null;
        }
        self::init();
        //====================================================================//
        // Unpack NuSOAP Request
        //====================================================================//
        if (true != self::receive($data)) {
            return null;
        }
        //====================================================================//
        // Execute Request
        //====================================================================//
        Splash::log()->msg("Connection Successful (" . Splash::getName() . " V" . Splash::getVersion() . ")");
        //====================================================================//
        // Transmit Answers To Master
        //====================================================================//
        return self::transmit(true);
    }
    
    /**
     *      @abstract      Administrative server functions
     *
     *      @param         string   $id         OsWs WebService Node Identifier
     *      @param         string   $data       OsWs WebService Packaged Data Inputs
     *
     *      @return        mixed    WebService Packaged Data Outputs or NUSOAP Error
     *
     */
    public static function admin($id, $data)
    {
        return self::run($id, $data, __FUNCTION__);
    }

    /**
     *      @abstract      Objects server functions
     *
     *      @param         string   $id         OsWs WebService Node Identifier
     *      @param         string   $data       OsWs WebService Packaged Data Inputs
     *
     *      @return        mixed    WebService Packaged Data Outputs or NUSOAP Error
     */
    public static function objects($id, $data)
    {
        return self::run($id, $data, __FUNCTION__);
    }

    /**
     *      @abstract      Files Transfers server functions
     *
     *      @param         string   $id         OsWs WebService Node Identifier
     *      @param         string   $data       OsWs WebService Packaged Data Inputs
     *
     *      @return        mixed    WebService Packaged Data Outputs or NUSOAP Error
     */
    public static function files($id, $data)
    {
        return self::run($id, $data, __FUNCTION__);
    }

    /**
     *      @abstract      Widgets Retrieval server functions
     *
     *      @param         string   $id         OsWs WebService Node Identifier
     *      @param         string   $data       OsWs WebService Packaged Data Inputs
     *
     *      @return        mixed    WebService Packaged Data Outputs or NUSOAP Error
     */
    public static function widgets($id, $data)
    {
        return self::run($id, $data, __FUNCTION__);
    }
        
    //====================================================================//
    //  WEBSERVICE SERVER MANAGEMENT
    //====================================================================//
   
    /**
     *      @abstract       Class Initialisation
     *
     *      @return         bool
     */
    public static function init()
    {
        Splash::core();
        //====================================================================//
        // Initialize I/O Data Buffers
        self::$Inputs          = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        self::$Outputs         = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);

        return true;
    }
   
    //====================================================================//
    //  SERVER STATUS & CONFIG DEBUG FUNCTIONS
    //====================================================================//
        
    /**
     *      @abstract      Analyze & Debug Server Status
     *
     *      @return        null|string
     */
    public static function getStatusInformations()
    {
        $html = null;

        try {
            //====================================================================//
            // Output Server Informations
            $html   .=      Splash::log()->getHtmlListItem("Server Informations");
            $html   .=      "<PRE>" . print_r(Splash::ws()->getServerInfos()->getArrayCopy(), true) . "</PRE>";
            
            //====================================================================//
            // Verify PHP Version
            Splash::validate()->isValidPHPVersion();
            //====================================================================//
            // Verify PHP Extensions
            Splash::validate()->isValidPHPExtensions();
            //====================================================================//
            // Verify SOAP Method
            Splash::validate()->isValidSOAPMethod();
            //====================================================================//
            // Execute Splash Local SelfTest
            Splash::selfTest();
            //====================================================================//
            //  Verify Server Webservice Connection
            Splash::ws()->selfTest();
        } catch (\Exception $ex) {
            echo $ex->getMessage();
            $html  .=   Splash::log()->getHtmlLogList(true);
            echo $html;

            return null;
        }

        $html  .=   Splash::log()->getHtmlLogList(true);

        return $html;
    }
 
    /**
     *      @abstract   Treat Received Data and Initialize Server before request exectution
     *
     *      @param      string      $data       Received Raw Data
     *
     *      @return     bool
     */
    private static function receive($data)
    {
        //====================================================================//
        // Unpack Raw received data
        self::$Inputs = Splash::ws()->unPack($data);
        if (empty(self::$Inputs)) {
            return false;
        }
        
        //====================================================================//
        // Import Server request Configuration
        if (isset(self::$Inputs->cfg) && !empty(self::$Inputs->cfg)) {
            //====================================================================//
            // Store Server Request Configuration
            Splash::configuration()->server = self::$Inputs->cfg;
            
            //====================================================================//
            // Setup Debug allowed or not
            Splash::log()->setDebug(self::$Inputs->cfg->debug);
        } else {
            //====================================================================//
            // Store Server Request Configuration
            Splash::configuration()->server = array();
        }
        
        //====================================================================//
        // Fill Static Server Informations To Output
        self::$Outputs->server = Splash::ws()->getServerInfos();
        
        return true;
    }

    /**
     *      @abstract   Treat Computed Data and return packaged data buffer for tranmit to master
     *
     *      @param      bool        $result     Global Operation Result (0 if KO, 1 if OK)
     *
     *      @return     false|string      To Transmit Raw Data or False if KO
     */
    private static function transmit($result)
    {
        //====================================================================//
        // Safety Check
        if (empty(self::$Outputs)) {
            return false;
        }
        
        //====================================================================//
        // Prepare Data Output Buffer
        //====================================================================//
        
        //====================================================================//
        // Set Global Operation Result
        self::$Outputs->result = $result;
        
        //====================================================================//
        // Flush Php Output Buffer
        Splash::log()->flushOuputBuffer();
        
        //====================================================================//
        // Transfers Log Reccords to _Out Buffer
        self::$Outputs->log = Splash::log();
        
        //====================================================================//
        // Package data and return to Server
        return Splash::ws()->pack(self::$Outputs);
    }
  
    /**
     * @abstract    All-In-One SOAP Server Messages Reception & Dispaching
     *              Unpack all pending tasks and send order to local task routers for execution.
     *
     * @param string $serverId   WebService Node Identifier
     * @param string $data       WebService Packaged Data Inputs
     * @param string $routerName Name of the router function to use for task execution
     *
     * @return mixed WebService Packaged Data Outputs or NUSOAP Error
     */
    private static function run($serverId, $data, $routerName)
    {
        //====================================================================//
        // Verify Node Id
        //====================================================================//
        if (Splash::configuration()->WsIdentifier !== $serverId) {
            return self::transmit(false);
        }
        self::init();
        //====================================================================//
        // Unpack NuSOAP Request
        //====================================================================//
        if (true != self::receive($data)) {
            return self::transmit(false);
        }
        //====================================================================//
        // Execute Request
        //====================================================================//
        $result = Splash::router()->execute($routerName, self::$Inputs, self::$Outputs);
        //====================================================================//
        // Transmit Answers To Master
        //====================================================================//
        return self::transmit($result);
    }
}
