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

namespace Splash\Server;

use Splash\Core\SplashCore  as Splash;
use Throwable;

//====================================================================//
//  SPLASH SERVER CLASS
//====================================================================//

/**
 * Splash Sync Server. Manage Splash Requests & Responses.
 * This file is included only in case on SOAP call to slave server.
 */
class SplashServer
{
    //====================================================================//
    // Webservice I/O Buffers
    //====================================================================//

    /**
     * Data Received by Server
     *
     * @var null|array
     */
    private static ?array $inputs;

    /**
     * Data Output Buffer
     *
     * @var array
     */
    private static array $outputs;

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
     * @return null|string WebService Packaged Data Outputs
     */
    public static function ping(): ?string
    {
        self::init();

        //====================================================================//
        // Simple Message reply, No Encryption
        Splash::log()->msg("Ping Successful.");
        self::$outputs['result'] = true;

        //====================================================================//
        // Transmit Answer with No Encryption
        return Splash::ws()->pack(self::$outputs, true);
    }

    /**
     * Connect Webservice and fetch server information
     *
     * @param string $id   WebService Node Identifier
     * @param string $data WebService Packaged Data Inputs
     *
     * @return null|string WebService Packaged Data Outputs
     */
    public static function connect(string $id, string $data): ?string
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
     * @return null|string WebService Packaged Data Outputs
     */
    public static function admin(string $id, string $data): ?string
    {
        return self::run($id, $data, __FUNCTION__);
    }

    /**
     * Objects server functions
     *
     * @param string $id   WebService Node Identifier
     * @param string $data WebService Packaged Data Inputs
     *
     * @return null|string WebService Packaged Data Outputs
     */
    public static function objects($id, $data): ?string
    {
        return self::run($id, $data, __FUNCTION__);
    }

    /**
     * Files Transfers server functions
     *
     * @param string $id   WebService Node Identifier
     * @param string $data WebService Packaged Data Inputs
     *
     * @return null|string WebService Packaged Data Outputs
     */
    public static function files(string $id, string $data): ?string
    {
        return self::run($id, $data, __FUNCTION__);
    }

    /**
     * Widgets Retrieval server functions
     *
     * @param string $id   WebService Node Identifier
     * @param string $data WebService Packaged Data Inputs
     *
     * @return null|string WebService Packaged Data Outputs
     */
    public static function widgets(string $id, string $data): ?string
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
    public static function init(): bool
    {
        Splash::core();
        //====================================================================//
        // Initialize I/O Data Buffers
        self::$inputs = array();
        self::$outputs = array();

        return true;
    }

    /**
     * Declare fatal Error Handler => Called in case of Script Exceptions
     *
     * @return void
     */
    public static function fatalHandler(): void
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
        } else {
            //====================================================================//
            // Non Fatal Error
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
    public static function getStatusInformations(): ?string
    {
        $html = null;

        try {
            //====================================================================//
            // Output Server Informations
            $html .= Splash::log()->getHtmlListItem("Server Informations");
            $html .= "<PRE>".print_r(Splash::ws()->getServerInfos(), true)."</PRE>";

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
     * Treat Received Data and Initialize Server before request execution
     *
     * @param string $data Received Raw Data
     *
     * @return bool
     */
    private static function receive(string $data): bool
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
        if (isset(self::$inputs['server']) && !empty(self::$inputs['server'])) {
            Splash::configuration()->server = self::$inputs['server'];
        }
        //====================================================================//
        // Setup Debug Flag if Requested
        if (self::$inputs['debug'] ?? false) {
            if (!defined('SPLASH_DEBUG')) {
                define('SPLASH_DEBUG', true);
            }
        }
        //====================================================================//
        // Enable Verbose Logs Flag if Requested
        if (self::$inputs['verbose'] ?? false) {
            Splash::log()->setDebug(true);
        }
        //====================================================================//
        // Fill Static Server Informations To Output
        self::$outputs['server'] = Splash::ws()->getServerInfos();

        return true;
    }

    /**
     * Treat Computed Data and return packaged data buffer for transmit to master
     *
     * @param bool $result Global Operation Result (0 if KO, 1 if OK)
     *
     * @return null|string To Transmit Raw Data or NULL if KO
     */
    private static function transmit(bool $result): ?string
    {
        //====================================================================//
        // Safety Check
        if (empty(self::$outputs)) {
            return null;
        }
        //====================================================================//
        // Prepare Data Output Buffer
        //====================================================================//

        //====================================================================//
        // Set Global Operation Result
        self::$outputs['result'] = $result;
        //====================================================================//
        // Flush Php Output Buffer
        Splash::log()->flushOutputBuffer();
        //====================================================================//
        // Transfers Log Records to _Out Buffer
        self::$outputs['log'] = Splash::log()->getRawLog();

        //====================================================================//
        // Package data and return to Server
        return Splash::ws()->pack(self::$outputs);
    }

    /**
     * All-In-One SOAP Server Messages Reception & Dispatching
     * Unpack all pending tasks and send order to local task routers for execution.
     *
     * @param string $serverId   WebService Node Identifier
     * @param string $data       WebService Packaged Data Inputs
     * @param string $routerName Name of the router function to use for task execution
     *
     * @return null|string To Transmit Raw Data or NULL if KO
     */
    private static function run(string $serverId, string $data, string $routerName): ?string
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
        if ((true != self::receive($data)) || !self::$inputs) {
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
