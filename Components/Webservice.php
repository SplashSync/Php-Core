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
 * @abstract    This Class Manage Low Level NUSOAP WebService Requests
 * @author      B. Paquier <contact@splashsync.com>
 */

namespace   Splash\Components;

use ArrayObject;

use Splash\Core\SplashCore      as Splash;
use Splash\Server\SplashServer;

//====================================================================//
//   INCLUDES
//====================================================================//

//====================================================================//
//  CLASS DEFINITION
//====================================================================//

class Webservice
{
    //====================================================================//
    // WebService Parameters
    //====================================================================//
 
    const SPLASHHOST    =   "www.splashsync.com/ws/soap";
    //====================================================================//
    // Remote Server Address
    private $host       =   self::SPLASHHOST;
    //====================================================================//
    // Unik Client Identifier ( 1 to 8 Char)
    private $id         =   "";
    //====================================================================//
    // Unik Key for encrypt data transmission with this Server
    private $key        =   "";
    //====================================================================//
    // Webservice tasks
    private $tasks;
    //====================================================================//
    // Webservice Call Url
    public $url;
    //====================================================================//
    // Webservice buffers
    private $In;                   // Input Buffer
    private $Out;                  // Output Buffer
    
    /**
     *      @abstract     Initialise Class with empty webservice parameters
     */
    public function __construct()
    {
        //====================================================================//
        // Initialize Tasks List
        $this->tasks        = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        //====================================================================//
        // Initialize I/O Data Buffers
        $this->In          = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        $this->Out         = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
    }

    //====================================================================//
    //  WEBSERVICE PARAMETERS MANAGEMENT
    //====================================================================//

    /**
     *      @abstract   Initialise Class with webservice parameters
     *
     *      @return     bool
     */
    public function setup()
    {
        //====================================================================//
        // Read Parameters
        $this->id       =   Splash::configuration()->WsIdentifier;
        $this->key      =   Splash::configuration()->WsEncryptionKey;
        
        //====================================================================//
        // If Another Host is Defined => Allow Overide of Server Host Address
        if (!empty(Splash::configuration()->WsHost)) {
            $this->host     =   Splash::configuration()->WsHost;
        } else {
            $this->host     =   self::SPLASHHOST;
        }

        //====================================================================//
        // Safety Check
        if (!$this->verify()) {
            return false;
        }
        return Splash::log()->deb("MsgWsSetParams");
    }

    /**
     *     @abstract    Verify Webservice parameters
     *
     *     @return      bool
     */
    public function verify()
    {
        
        //====================================================================//
        // Verify host address is present
        if (empty($this->host)) {
            return Splash::log()->err("ErrWsNoHost");
        }
        
        //====================================================================//
        // Verify Server Id not empty
        if (empty($this->id)) {
            return Splash::log()->err("ErrWsNoId");
        }

        //====================================================================//
        // Verify Server Id not empty
        if (empty($this->key)) {
            return Splash::log()->err("ErrWsNoKey");
        }

        return true;
    }

    //====================================================================//
    //  DATA BUFFER MANAGEMENT
    //====================================================================//

    /**
     *      @abstract    Encrypt/Decrypt Serialized Data Object
     *      @param       string      $act    Action to perform on Data (encrypt/decrypt)
     *      @param       mixed       $In     Input Data
     *      @param       string      $key    Encoding Shared Key
     *      @param       string      $iv      Encoding Shared IV (Initialisation Vector)
     *      @return      string      $Out    Output Encrypted Data (Or 0 if fail)
     */
    public function crypt($act, $In, $key, $iv)
    {
        //====================================================================//
        // Safety Check
        //====================================================================//
        // Verify Crypt Direction
        if ($act == "encrypt") {
            Splash::log()->deb("MsgWsEnCrypt");
        } elseif ($act == "decrypt") {
            Splash::log()->deb("MsgWsDeCrypt");
        } else {
            return $this->Err("ErrWsCryptAction");
        }
        //====================================================================//
        // Verify All Parameters are given
        if (empty($In) || empty($key) || empty($iv)) {
            return Splash::log()->err("ErrParamMissing", __FUNCTION__);
        }
        //====================================================================//
        // Init output as error value
        $Out = false;
        //====================================================================//
        // hash of secret key
        $CryptKey = hash('sha256', $key);
        //====================================================================//
        // hash of initialisation vector
        // Note : encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $CryptIv = substr(hash('sha256', $iv), 0, 16);
        //====================================================================//
        // Open SSL Encryption
        if ($act == 'encrypt') {
            $Out = base64_encode(openssl_encrypt($In, Splash::configuration()->WsCrypt, $CryptKey, 0, $CryptIv));
        } //====================================================================//
        // Open SSL Decryption
        elseif ($act == 'decrypt') {
            $Out = openssl_decrypt(base64_decode($In), Splash::configuration()->WsCrypt, $CryptKey, 0, $CryptIv);
        }
        //====================================================================//
        //  Debug Informations
//        Splash::log()->Deb("OsWs Crypt - Secret Key : " . $secret_key . " ==> " . $key );
//        Splash::log()->Deb("OsWs Crypt - Secret IV : " . $secret_iv . " ==> " . $iv );
//        Splash::log()->Deb("OsWs Crypt - Result : " . $Out);
        return $Out;
    }

    /**
     *      @abstract    Prepare Data Packets for transmit.
     *      @param       Array   $In         Input Data ArrayObject
     *      @param       bool    $Uncrypted  Force no encrypt on message.
     *      @return      string  $Out        Output Packet Data ( Encrypted or not )
     */
    public function pack($In, $Uncrypted = false)
    {
        //====================================================================//
        // Debug Log
        Splash::log()->deb("MsgWsPack");
        
        //====================================================================//
        // Encode Data Buffer
        //====================================================================//
        if (Splash::configuration()->WsEncode == "XML") {
            //====================================================================//
            // Convert Data Buffer To XML
            $Serial = Splash::xml()->objectToXml($In);
        } else {
            //====================================================================//
            // Serialize Data Buffer
            $Serial = serialize($In);
        }
        
        //====================================================================//
        // Encrypt serialized data buffer
        //====================================================================//
        if (!$Uncrypted) {
            $Out = $this->crypt("encrypt", $Serial, $this->key, $this->id);
        } //====================================================================//
        // Else, switch to base64
        else {
            $Out = base64_encode($Serial);
        }
        
        //====================================================================//
        //  Debug Informations
        //====================================================================//
        if ((!SPLASH_SERVER_MODE) && (Splash::configuration()->TraceOut)) {
            Splash::log()->war("MsgWsFinalPack", print_r($Serial, true));
        }
        return $Out;
    }

    /**
     *      @abstract   Unpack received Data Packets.
     *      @param      string   $In         Input Data
     *      @param      bool     $Uncrypted  Force no encrypt on message.
     *      @return     Array    $Out        Output Packet Data
     */
    public function unPack($In, $Uncrypted = false)
    {
        //====================================================================//
        // Debug Log
        Splash::log()->deb("MsgWsunPack");
        
        //====================================================================//
        // Decrypt response
        //====================================================================//
        if (!empty($In) && !$Uncrypted) {
            $Decode = $this->crypt("decrypt", $In, $this->key, $this->id);
        } //====================================================================//
        // Else, switch from base64
        else {
            $Decode = base64_decode($In, true);
        }
        
        //====================================================================//
        // Decode Data Response
        //====================================================================//
        // Convert Data Buffer To XML
        if (Splash::configuration()->WsEncode == "XML") {
            if (strpos($Decode, '<SPLASH>') !== false) {
                $Out = Splash::xml()->XmlToArrayObject($Decode);
            }
            //====================================================================//
        // Unserialize Data buffer
        } else {
            if (!empty($Decode)) {
                $Out = unserialize($Decode);
            }
        }
        
        //====================================================================//
        // Trow Exception if fails
        if (empty($Out)) {
            Splash::log()->err("ErrWsunPack");
        }
        
        //====================================================================//
        //  Messages Debug Informations
        //====================================================================//
        //  Data Decoded (PHP Serialized Objects or XML)
        if ((!SPLASH_SERVER_MODE) && (Splash::configuration()->TraceIn)) {
            Splash::log()->war("Splash unPack - Data Decode : " . print_r($Decode, true));
        }
        //  Final Decoded Data (ArrayObject Structure)
        //  Splash::log()->Deb("OsWs unPack - Data unSerialized : " . print_r($Out,true) );
  
        //====================================================================//
        // Return Result or False
        return empty($Out)?false:$Out;
    }

    /**
     *      @abstract   Clean Ws Input Buffer before Call Request
     *      @return     int     $result     0 if KO, 1 if OK
     */
    public function cleanIn()
    {
        //====================================================================//
        //  Free current output buffer
        unset($this->In);
        //====================================================================//
        //  Initiate a new input buffer
        $this->In = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        return true;
    }

    /**
     *      @abstract   Clean parameters of Ws Call Request
     *      @return     int     $result     0 if KO, 1 if OK
     */
    public function cleanOut()
    {
        //====================================================================//
        //  Free current tasks list
        unset($this->tasks);
        //====================================================================//
        //  Initiate a new tasks list
        $this->tasks    =  new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        
        //====================================================================//
        //  Free current output buffer
        unset($this->Out);
        //====================================================================//
        //  Initiate a new output buffer
        $this->Out     = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        return true;
    }
    
    //====================================================================//
    //  CORE WEBSERVICE FUNCTIONS
    //====================================================================//

    /**
     *      @abstract   Perform operation with WebService Client
     *      @param      string      $Service        server method to use
     *      @param      array       $Tasks          List of task to perform inside this request.
     *                                              If NULL, internal task list is used.
     *      @param      bool        $Uncrypted      force message not to be crypted (Used for Ping Only)
     *      @param      bool        $Clean          Clean task buffer at the end of this function
     *      @return     int         $result         0 if KO, 1 if OK
     */
    public function call($Service, $Tasks = null, $Uncrypted = false, $Clean = true)
    {
        //====================================================================//
        // WebService Call =>> Initialisation
        if (!$this->init($Service)) {
            return false;
        }
        //====================================================================//
        // WebService Call =>> Add Tasks
        if (!$this->addTasks($Tasks)) {
            return false;
        }
        //====================================================================//
        // Prepare Raw Request Data
        //====================================================================//
        $this->RawOut = array(
            'id' => $this->id ,
            'data' => $this->pack($this->Out, $Uncrypted));
        //====================================================================//
        // Prepare Webservice Client
        //====================================================================//
        if (!$this->buildClient()) {
            return false;
        }
        //====================================================================//
        // Call Execution
        $this->RawIn = Splash::com()->call($this->Out->service, $this->RawOut);
        //====================================================================//
        // Analyze & Decode Response
        //====================================================================//
        if (!$this->decodeResponse($Uncrypted)) {
            return false;
        }
        //====================================================================//
        // If required, lean _Out buffer parameters before exit
        if ($Clean) {
            $this->cleanOut();
        }
        return $this->In;
    }

    /**
     *      @abstract   Simulate operation on Local WebService Client
     *      @param      string      $Service        server method to use
     *      @param      array       $Tasks          List of task to perform inside this request.
     *                                              If NULL, internal task list is used.
     *      @param      bool        $Uncrypted      force message not to be crypted (Used for Ping Only)
     *      @param      bool        $Clean          Clean task buffer at the end of this function
     *      @return     int         $result         0 if KO, 1 if OK
     */
    public function simulate($Service, $Tasks = null, $Uncrypted = false, $Clean = true)
    {
        //====================================================================//
        // WebService Call =>> Initialisation
        if (!$this->init($Service)) {
            return false;
        }
        //====================================================================//
        // WebService Call =>> Add Tasks
        if (!$this->addTasks($Tasks)) {
            return false;
        }
        //====================================================================//
        //   Execute Action From Splash Server to Module
        $Response   =   SplashServer::$Service(
            Splash::configuration()->WsIdentifier,
            $this->pack($this->Out, $Uncrypted)
        );
        //====================================================================//
        // If required, lean _Out buffer parameters before exit
        if ($Clean) {
            $this->cleanOut();
        }
        return $Response;
    }
    
    /**
     *      @abstract   Init WebService Call
     *
     *      @param      string      $service        server method to use
     *
     *      @return     bool
     */
    private function init($service)
    {
        
        //====================================================================//
        // Debug
        Splash::log()->deb("MsgWsCall");
        
        //====================================================================//
        // Safety Check
        if (!$this->verify()) {
            return Splash::log()->err("ErrWsInValid");
        }
        
        //====================================================================//
        // Clean Data Input Buffer
        $this->cleanIn();
        
        //====================================================================//
        // Prepare Data Output Buffer
        //====================================================================//
        // Fill buffer with Server Core infos
        $this->Out->server     = $this->getServerInfos();
        // Remote Service to call
        $this->Out->service    = $service;
        // Share Debug Flag with Server
        $this->Out->debug      = (int) SPLASH_DEBUG;
        
        return true;
    }
    
    
    /**
     * @abstract   Add Tasks to WebService Request
     *
     * @param      array       $tasks      List of task to perform inside this request.
     *                                          If NULL, internal task list is used.
     * @return     bool
     */
    private function addTasks($tasks = null)
    {
        
        //====================================================================//
        // No tasks to Add
        if (is_null($tasks) && empty($this->tasks)) {
            return true;
        }

        //====================================================================//
        // Prepare Tasks To Perform
        //====================================================================//
        
        //====================================================================//
        // Add Internal Tasks to buffer
        if (!empty($this->tasks)) {
            $this->Out->tasks      = $this->tasks;
            $this->Out->taskscount = count($this->Out->tasks);
            Splash::log()->deb("[NuSOAP] Call Loaded " . $this->Out->tasks->count() . " Internal tasks");
        } else {
            $this->Out->tasks  =   new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        }
        
        //====================================================================//
        // Add External Tasks to the request
        if (!empty($tasks)) {
            $this->Out->tasks->append($tasks);
            $this->Out->taskscount = count($tasks);
            Splash::log()->deb("[NuSOAP] Call Loaded " . count($tasks) . " External tasks");
        }
        
        return true;
    }
    
    /**
     *      @abstract   Create & Setup WebService Client
     *
     *      @return     NuSOAP_Client
     */
    private function buildClient()
    {
        //====================================================================//
        // Compute target client url
        if ((strpos($this->host, "http://") === false) && (strpos($this->host, "https://") === false)) {
            $this->url = 'https://' . $this->host;
        } else {
            $this->url = $this->host;
        }
        //====================================================================//
        // Build Webservice Client
        Splash::com()->buildClient($this->url);
        
        return true;
    }

    
    /**
     * @abstract   Decode WebService Client Response
     * @param      bool     $Uncrypted      Force message not to be crypted (Used for Ping Only)
     * @return     bool
     */
    private function decodeResponse($Uncrypted)
    {
                
        //====================================================================//
        // Decode & Store NuSOAP Errors if present
        if (isset($this->client->fault) && !empty($this->client->fault)) {
            //====================================================================//
            //  Debug Informations
            Splash::log()->deb("[NuSOAP] Fault Details='"   . $this->client->faultdetail . "'");
            //====================================================================//
            //  Errro Message
            return Splash::log()->err("ErrWsNuSOAPFault", $this->client->faultcode, $this->client->faultstring);
        }
        
        //====================================================================//
        // Unpack NuSOAP Answer
        //====================================================================//
        if (!empty($this->RawIn)) {
            //====================================================================//
            // Unpack Data from Raw packet
            $this->In = $this->unPack($this->RawIn, $Uncrypted);
            //====================================================================//
            // Merge Logging Messages from remote with current class messages
            if (isset($this->In->log)) {
                Splash::log()->merge($this->In->log);
            }
        } else {
            //====================================================================//
            //  Add Information to Debug Log
            Splash::log()->deb("[NuSOAP] Id='"          . print_r($this->id, true) . "'");
            //====================================================================//
            //  Error Message
            return Splash::log()->err("ErrWsNoResponse", $this->Out->service, $this->url);
        }
        
        return true;
    }
    
    //====================================================================//
    //  TASKS STORAGE MANAGEMENT
    //====================================================================//
   
    /**
     *      \brief      Add a new task for NuSOAP Call Request
     *      \param      string      $name       Task Identifier Name (Listed in OsWs.inc.php)
     *      \param      array       $params     Task Parameters
     *      \param      string      $desc       Task Name/Description
     *      \return     SplashWs
     */
    public function addTask($name, $params, $desc = "No Description")
    {
        //====================================================================//
        // Create a new task
        $task   =   new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        //====================================================================//
        // Prepare Task Id
        $Id                     =       $this->tasks->count() + 1;
        //====================================================================//
        // Fill task with informations
        $task["id"]         =   $Id;
        $task["name"]       =   $name;
        $task["desc"]       =   $desc;
        $task["params"]         =   $params;
        //====================================================================//
        // Add Task to Tasks list
        $this->tasks[$Id]       = $task;
        //====================================================================//
        // Debug
        Splash::log()->deb("TasksAdd", $task["name"], $task["desc"]);

        return $this;
    }
    
    //====================================================================//
    //  INFORMATION RETRIEVAL
    //====================================================================//

    /**
     * @abstract     Return Server Informations
     * @return       array   $result
     */
    public function getServerInfos()
    {
        //====================================================================//
        // Init Result Array
        $r = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        //====================================================================//
        // Server Infos
        $r->ServerType      = "PHP";                            // INFO - Server Language Type
        $r->ServerVersion   = phpversion();                     // INFO - Server Language Version
        $r->ProtocolVersion = SPL_PROTOCOL;                     // INFO - Server Protocal Version
        //====================================================================//
        // Server Infos
        $r->Self            = Splash::input("PHP_SELF");           // INFO - Current Url
        $r->ServerAddress   = Splash::input("SERVER_ADDR");        // INFO - Server IP Address
        // Read System Folder without symlinks
        $r->ServerRoot      = realpath(Splash::input("DOCUMENT_ROOT"));
        $r->UserAgent       = Splash::input("HTTP_USER_AGENT");    // INFO - Browser User Agent
        $r->WsMethod        = Splash::configuration()->WsMethod;    // Current Splash WebService Component
        //====================================================================//
        // Server Urls
        //====================================================================//
        // CRITICAL - Server Host Name
        // Check if Overiden by Application Module
        if (isset(Splash::configuration()->ServerHost)) {
            $r->ServerHost      =   Splash::configuration()->ServerHost;
        // Check if Available with Secured Reading
        } elseif (!empty(Splash::input("SERVER_NAME"))) {
            $r->ServerHost      = Splash::input("SERVER_NAME");
        // Fallback to Unsecured Mode (Required for Phpunit)
        } else {
            $r->ServerHost      = $_SERVER["SERVER_NAME"];
        }
        //====================================================================//
        // Server IPv4 Address
        $r->ServerIP        = Splash::input("SERVER_ADDR");
        //====================================================================//
        // Server WebService Path
        if (isset(Splash::configuration()->ServerPath)) {
            $r->ServerPath      =   Splash::configuration()->ServerPath;
        } else {
            $FullPath           =   dirname(__DIR__);
            $RelativePath       =   explode($r->ServerRoot, $FullPath);
            if (isset($RelativePath[1])) {
                $r->ServerPath  =   $RelativePath[1] . "/soap.php";
            } else {
                $r->ServerPath  =   null;
            }
        }
        $r->setFlags(ArrayObject::STD_PROP_LIST);
        return $r;
    }

    /**
     * @abstract     Return Server Outputs Buffer
     * @return       array   $result
     */
    public function getOutputBuffer()
    {
        return $this->Out;
    }
    
    /**
     *      @abstract   Get Client Server Schema (http or https)
     *
     *      @return     string
     */
    public function getServerScheme()
    {
        return empty(Splash::input("REQUEST_SCHEME")) ? "http" : Splash::input("REQUEST_SCHEME");
    }
    
    /**
     *      @abstract   Build WebService Client Url
     *
     *      @return     string
     */
    private function getClientUrl()
    {
        //====================================================================//
        // Fetch Server Informations
        $ServerInfos    = $this->getServerInfos();
        //====================================================================//
        // Build Server Url
        return $this->getServerScheme() . "://" . $ServerInfos["ServerHost"] . $ServerInfos["ServerPath"];
    }
    
    /**
     *      @abstract   Build WebService Client Debug Html Link
     *
     *      @return     string
     */
    private function getClientDebugLink()
    {
        //====================================================================//
        // Compute target client debug url
        $Url    = $this->getClientUrl();
        $Params = "?node=" . $this->id;
        return '<a href="' . $Url . $Params . '" target="_blank" >' . $Url . '</a>';
    }
    
    //====================================================================//
    //  WEBSERVICE SELF-TESTS
    //====================================================================//
    
    /**
     *      @abstract   Check Reverse Connexion with THIS Client
     *      @param      bool    $Silent     No message display if non errors
     *      @return     int             0 if KO, 1 if OK
     */
    public function selfTest($Silent = true)
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace(__CLASS__, __FUNCTION__);
        //====================================================================//
        // Clone Webservice Class
        $TestsClient = clone $this;
        //====================================================================//
        // Setup Webservice Class
        $TestsClient->host  = $this->getClientUrl();
        //====================================================================//
        // Run NuSOAP Call - Reverse Ping
        $Ping = $TestsClient->call(SPL_S_PING, null, 1);
        if (empty($Ping) || !isset($Ping->result) || !$Ping->result) {
            Splash::log()->err(Splash::trans("ErrReversePing", $TestsClient->host));
            return Splash::log()->err(Splash::trans("ErrReverseDebug", $this->getClientDebugLink()));
        }
        //====================================================================//
        // Run NuSOAP Call - Reverse Ping
        $Connect = $TestsClient->call(SPL_S_CONNECT, array());
        if (empty($Connect) || !isset($Connect->result) || !$Connect->result) {
            Splash::log()->err(Splash::trans("ErrReverseConnect", $TestsClient->host));
            return Splash::log()->err(Splash::trans("ErrReverseDebug", $this->getClientDebugLink()));
        }
        Splash::log()->msg(Splash::trans("MsgReverseConnect"));
        
        return true;
    }
}
