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

namespace Splash\Server;

use ArrayObject;
use Splash\Core\SplashCore  as Splash;
use Throwable;

//====================================================================//
//  CLASS DEFINITION
//====================================================================//

/**
 * Splash Sync Server. Manage Splash Requests & Responses.
 * This file is included only in case on SOAP call to slave server.
 *
 * @author      B. Paquier <contact@splashsync.com>
 */
class SplashServer
{
    //====================================================================//
    // Webservice I/O Buffers
    //====================================================================//
    /** @var ArrayObject */
    private static $inputs;         // Input Buffer
    /** @var ArrayObject */
    private static $outputs;        // Output Buffer

    /**
     * Class Constructor
     */
    public function __construct()
    {
        self::init();
    }

    //====================================================================//
    //  WEBSERVICE REGISTERED REQUEST FUNCTIONS
    //====================================================================//

    /**
     * Minimal Test of Webservice connexion
     *
     * @return mixed WebService Packaged Data Outputs or NUSOAP Error
     */
    public static function ping()
    {
        self::init();

        //====================================================================//
        // Simple Message reply, No Encryption
        Splash::log()->msg("Ping Successful.");
        self::$outputs->result = true;

        //====================================================================//
        // Transmit Answer with No Encryption
        return Splash::ws()->pack(self::$outputs, true);
    }

    /**
     * Connect Webservice and fetch server informations
     *
     * @param string $id   WebService Node Identifier
     * @param string $data WebService Packaged Data Inputs
     *
     * @return mixed WebService Packaged Data Outputs or NUSOAP Error
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
        Splash::log()->msg("Connection Successful (".Splash::getName()." V".Splash::getVersion().")");
        //====================================================================//
        // Transmit Answers To Master
        //====================================================================//
        return self::transmit(true);
    }

    /**
     * Administrative server functions
     *
     * @param string $id   WebService Node Identifier
     * @param string $data WebService Packaged Data Inputs
     *
     * @return mixed WebService Packaged Data Outputs or NUSOAP Error
     */
    public static function admin($id, $data)
    {
        return self::run($id, $data, __FUNCTION__);
    }

    /**
     * Objects server functions
     *
     * @param string $id   WebService Node Identifier
     * @param string $data WebService Packaged Data Inputs
     *
     * @return mixed WebService Packaged Data Outputs or NUSOAP Error
     */
    public static function objects($id, $data)
    {
        return self::run($id, $data, __FUNCTION__);
    }

    /**
     * Files Transfers server functions
     *
     * @param string $id   WebService Node Identifier
     * @param string $data WebService Packaged Data Inputs
     *
     * @return mixed WebService Packaged Data Outputs or NUSOAP Error
     */
    public static function files($id, $data)
    {
        return self::run($id, $data, __FUNCTION__);
    }

    /**
     * Widgets Retrieval server functions
     *
     * @param string $id   WebService Node Identifier
     * @param string $data WebService Packaged Data Inputs
     *
     * @return mixed WebService Packaged Data Outputs or NUSOAP Error
     */
    public static function widgets($id, $data)
    {
        return self::run($id, $data, __FUNCTION__);
    }

    //====================================================================//
    //  WEBSERVICE SERVER MANAGEMENT
    //====================================================================//

    /**
     * Class Initialisation
     *
     * @return bool
     */
    public static function init()
    {
        Splash::core();
        //====================================================================//
        // Initialize I/O Data Buffers
        self::$inputs = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        self::$outputs = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);

        return true;
    }

    /**
     * Declare fatal Error Handler => Called in case of Script Exceptions
     *
     * @return void
     */
    public static function fatalHandler()
    {
        //====================================================================//
        // Read Last Error
        $error = error_get_last();
        if (!$error) {
            return;
        }
        //====================================================================//
        // Fatal Error
        if (E_ERROR == $error["type"]) {
            //====================================================================//
            // Parse Error in Response.
            Splash::com()->fault($error);
            //====================================================================//
            // Process methods & Return the results.
            Splash::com()->handle();
        //====================================================================//
        // Non Fatal Error
        } else {
            Splash::log()->war($error["message"]." on File ".$error["file"]." Line ".$error["line"]);
        }
    }

    //====================================================================//
    //  SERVER STATUS & CONFIG DEBUG FUNCTIONS
    //====================================================================//

    /**
     * Analyze & Debug Server Status
     *
     * @return null|string
     */
    public static function getStatusInformations()
    {
        $html = null;

        try {
            //====================================================================//
            // Output Server Informations
            $html .= Splash::log()->getHtmlListItem("Server Informations");
            $html .= "<PRE>".print_r(Splash::ws()->getServerInfos()->getArrayCopy(), true)."</PRE>";

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
            $html .= Splash::log()->getHtmlLogList(true);
            echo $html;

            return null;
        }

        $html .= Splash::log()->getHtmlLogList(true);

        return $html;
    }

    /**
     * Treat Received Data and Initialize Server before request exectution
     *
     * @param string $data Received Raw Data
     *
     * @return bool
     */
    private static function receive($data)
    {
        //====================================================================//
        // Unpack Raw received data
        self::$inputs = Splash::ws()->unPack($data);
        if (empty(self::$inputs)) {
            return false;
        }
        //====================================================================//
        // Import Server Referer Informations
        Splash::configuration()->server = array();
        if (isset(self::$inputs->server) && !empty(self::$inputs->server)) {
            Splash::configuration()->server = self::$inputs->server;
        }
        //====================================================================//
        // Setup Debug Flag if Requested
        if (isset(self::$inputs->debug) && !empty(self::$inputs->debug)) {
            if (!defined('SPLASH_DEBUG')) {
                define('SPLASH_DEBUG', true);
            }
        }
        //====================================================================//
        // Enable Verbose Logs Flag if Requested
        if (isset(self::$inputs->verbose) && !empty(self::$inputs->verbose)) {
            Splash::log()->setDebug(true);
        }
        //====================================================================//
        // Fill Static Server Informations To Output
        self::$outputs->server = Splash::ws()->getServerInfos();

        return true;
    }

    /**
     * Treat Computed Data and return packaged data buffer for tranmit to master
     *
     * @param bool $result Global Operation Result (0 if KO, 1 if OK)
     *
     * @return false|string To Transmit Raw Data or False if KO
     */
    private static function transmit($result)
    {
        //====================================================================//
        // Safety Check
        if (empty(self::$outputs)) {
            return false;
        }

        //====================================================================//
        // Prepare Data Output Buffer
        //====================================================================//

        //====================================================================//
        // Set Global Operation Result
        self::$outputs->result = $result;

        //====================================================================//
        // Flush Php Output Buffer
        Splash::log()->flushOuputBuffer();

        //====================================================================//
        // Transfers Log Reccords to _Out Buffer
        self::$outputs->log = Splash::log();

        //====================================================================//
        // Package data and return to Server
        return Splash::ws()->pack(self::$outputs);
    }

    /**
     * All-In-One SOAP Server Messages Reception & Dispaching
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
        try {
            $result = Splash::router()->execute($routerName, self::$inputs, self::$outputs);
        } catch (Throwable $throwable) {
            Splash::log()->report($throwable);

            return self::transmit(false);
        }

        //====================================================================//
        // Transmit Answers To Master
        //====================================================================//
        return self::transmit($result);
    }
}
