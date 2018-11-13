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
 
    const       SPLASHHOST      =   "www.splashsync.com/ws/soap";
    //====================================================================//
    // Remote Server Address
    protected $host           =   self::SPLASHHOST;
    //====================================================================//
    // Unik Client Identifier ( 1 to 8 Char)
    protected $id             =   "";
    //====================================================================//
    // Unik Key for encrypt data transmission with this Server
    protected $key            =   "";
    //====================================================================//
    // Webservice Tasks Buffer
    private $tasks;
    //====================================================================//
    // Webservice Call Url
    public $url;
    //====================================================================//
    // Webservice buffers
    private $inputs;        // Input Buffer
    private $outputs;       // Output Buffer
    private $rawIn;         // Raw Call Input Buffer
    private $rawOut;        // Raw Call Output Buffer
    
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
        $this->inputs       = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        $this->outputs      = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
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
    private function verify()
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
     *  @abstract   Encrypt/Decrypt Serialized Data Object
     *  @param      string      $action         Action to perform on Data (encrypt/decrypt)
     *  @param      mixed       $data           Input Data
     *  @param      string      $sharedKey      Encoding Shared Key
     *  @param      string      $sharedVector   Encoding Shared IV (Initialisation Vector)
     *  @return     string      $Out            Output Encrypted Data (Or 0 if fail)
     */
    private function crypt($action, $data, $sharedKey, $sharedVector)
    {
        //====================================================================//
        // Safety Check
        //====================================================================//
        // Verify Crypt Direction
        if ($action == "encrypt") {
            Splash::log()->deb("MsgWsEnCrypt");
        } elseif ($action == "decrypt") {
            Splash::log()->deb("MsgWsDeCrypt");
        } else {
            return Splash::log()->err("ErrWsCryptAction");
        }
        //====================================================================//
        // Verify All Parameters are given
        if (empty($data) || empty($sharedKey) || empty($sharedVector)) {
            return Splash::log()->err("ErrParamMissing", __FUNCTION__);
        }
        //====================================================================//
        // Init output as error value
        $out = false;
        //====================================================================//
        // hash of secret key
        $cryptKey = hash('sha256', $sharedKey);
        //====================================================================//
        // hash of initialisation vector
        // Note : encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $cryptIv = substr(hash('sha256', $sharedVector), 0, 16);
        //====================================================================//
        // Open SSL Encryption
        if ($action == 'encrypt') {
            $out = base64_encode(openssl_encrypt($data, Splash::configuration()->WsCrypt, $cryptKey, 0, $cryptIv));
        //====================================================================//
        // Open SSL Decryption
        } elseif ($action == 'decrypt') {
            $out = openssl_decrypt(base64_decode($data), Splash::configuration()->WsCrypt, $cryptKey, 0, $cryptIv);
        }
        //====================================================================//
        //  Debug Informations
//        Splash::log()->deb("OsWs Crypt - Secret Key : " . $secret_key . " ==> " . $key );
//        Splash::log()->deb("OsWs Crypt - Secret IV : " . $secret_iv . " ==> " . $iv );
//        Splash::log()->deb("OsWs Crypt - Result : " . $Out);
        return $out;
    }

    /**
     * @abstract    Prepare Data Packets for transmit.
     * @param   Array   $data           Input Data ArrayObject
     * @param   bool    $isUncrypted    Force no encrypt on message.
     * @return  string  $Out            Output Packet Data ( Encrypted or not )
     */
    public function pack($data, $isUncrypted = false)
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
            $serial = Splash::xml()->objectToXml($data);
        } else {
            //====================================================================//
            // Serialize Data Buffer
            $serial = serialize($data);
        }
        
        //====================================================================//
        // Encrypt serialized data buffer
        //====================================================================//
        if (!$isUncrypted) {
            $out = $this->crypt("encrypt", $serial, $this->key, $this->id);
        //====================================================================//
        // Else, switch to base64
        } else {
            $out = base64_encode($serial);
        }
        
        //====================================================================//
        //  Debug Informations
        //====================================================================//
        if ((!SPLASH_SERVER_MODE) && (Splash::configuration()->TraceOut)) {
            Splash::log()->war("MsgWsFinalPack", print_r($serial, true));
        }
        return $out;
    }

    /**
     * @abstract   Unpack received Data Packets.
     *
     * @param   string      $data           Input Data
     * @param   bool        $isUncrypted    Force no encrypt on message.
     *
     * @return  ArrayObject
     */
    public function unPack($data, $isUncrypted = false)
    {
        //====================================================================//
        // Debug Log
        Splash::log()->deb("MsgWsunPack");
        
        //====================================================================//
        // Decrypt response
        //====================================================================//
        if (!empty($data) && !$isUncrypted) {
            $decode = $this->crypt("decrypt", $data, $this->key, $this->id);
        //====================================================================//
        // Else, switch from base64
        } else {
            $decode = base64_decode($data, true);
        }
        
        //====================================================================//
        // Decode Data Response
        //====================================================================//
        // Convert Data Buffer To XML
        if (Splash::configuration()->WsEncode == "XML") {
            if (strpos($decode, '<SPLASH>') !== false) {
                $out = Splash::xml()->XmlToArrayObject($decode);
            }
        //====================================================================//
        // Unserialize Data buffer
        } else {
            if (!empty($decode)) {
                $out = unserialize($decode);
            }
        }
        
        //====================================================================//
        // Trow Exception if fails
        if (empty($out)) {
            Splash::log()->err("ErrWsunPack");
        }
        
        //====================================================================//
        //  Messages Debug Informations
        //====================================================================//
//        //  Data Decoded (PHP Serialized Objects or XML)
//        if ((!SPLASH_SERVER_MODE) && (Splash::configuration()->TraceIn)) {
//            Splash::log()->war("Splash unPack - Data Decode : " . print_r($Decode, true));
//        }
//        //====================================================================//
//        //  Final Decoded Data (ArrayObject Structure)
//        Splash::log()->deb("Splash unPack - Data unSerialized : " . print_r($Out,true) );
  
        //====================================================================//
        // Return Result or False
        return empty($out)?false:$out;
    }
    
    /**
     * @abstract    Clean Ws Input Buffer before Call Request
     * @return     true
     */
    private function cleanIn()
    {
        //====================================================================//
        //  Free current output buffer
        unset($this->inputs);
        //====================================================================//
        //  Initiate a new input buffer
        $this->inputs = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        return true;
    }

    /**
     * @abstract   Clean parameters of Ws Call Request
     * @return     true
     */
    private function cleanOut()
    {
        //====================================================================//
        //  Free current tasks list
        unset($this->tasks);
        //====================================================================//
        //  Initiate a new tasks list
        $this->tasks    =  new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        //====================================================================//
        //  Free current output buffer
        unset($this->outputs);
        //====================================================================//
        //  Initiate a new output buffer
        $this->outputs     = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        return true;
    }
    
    //====================================================================//
    //  CORE WEBSERVICE FUNCTIONS
    //====================================================================//

    /**
     * @abstract   Perform operation with WebService Client
     *
     * @param   string      $service        server method to use
     * @param   array       $tasks          List of task to perform inside this request.
     *                                      If NULL, internal task list is used.
     * @param   bool        $isUncrypted    Force message not to be crypted (Used for Ping Only)
     * @param   bool        $clean          Clean task buffer at the end of this function
     *
     * @return  ArrayObject|false
     */
    public function call($service, $tasks = null, $isUncrypted = false, $clean = true)
    {
        //====================================================================//
        // WebService Call =>> Initialisation
        if (!$this->init($service)) {
            return false;
        }
        //====================================================================//
        // WebService Call =>> Add Tasks
        if (!$this->addTasks($tasks)) {
            return false;
        }
        //====================================================================//
        // Prepare Raw Request Data
        //====================================================================//
        $this->rawOut = array(
            'id' => $this->id ,
            'data' => $this->pack($this->outputs, $isUncrypted));
        //====================================================================//
        // Prepare Webservice Client
        //====================================================================//
        if (!$this->buildClient()) {
            return false;
        }
        //====================================================================//
        // Call Execution
        $this->rawIn = Splash::com()->call($this->outputs->service, $this->rawOut);
        //====================================================================//
        // Analyze & Decode Response
        //====================================================================//
        if (!$this->decodeResponse($isUncrypted)) {
            return false;
        }
        //====================================================================//
        // If required, lean _Out buffer parameters before exit
        if ($clean) {
            $this->cleanOut();
        }
        return $this->inputs;
    }

    /**
     * @abstract   Simulate operation on Local WebService Client
     *
     * @param   string      $service        server method to use
     * @param   array       $tasks          List of task to perform inside this request.
     *                                              If NULL, internal task list is used.
     * @param   bool        $isUncrypted    Force message not to be crypted (Used for Ping Only)
     * @param   bool        $clean          Clean task buffer at the end of this function
     *
     * @return  ArrayObject|false
     */
    public function simulate($service, $tasks = null, $isUncrypted = false, $clean = true)
    {
        //====================================================================//
        // WebService Call =>> Initialisation
        if (!$this->init($service)) {
            return false;
        }
        //====================================================================//
        // WebService Call =>> Add Tasks
        if (!$this->addTasks($tasks)) {
            return false;
        }
        //====================================================================//
        //   Execute Action From Splash Server to Module
        $response   =   SplashServer::$service(
            Splash::configuration()->WsIdentifier,
            $this->pack($this->outputs, $isUncrypted)
        );
        //====================================================================//
        // If required, lean _Out buffer parameters before exit
        if ($clean) {
            $this->cleanOut();
        }
        return $response;
    }
    
    /**
     * @abstract    Init WebService Call
     *
     * @param   string      $service        server method to use
     *
     * @return  bool
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
        $this->outputs->server     = $this->getServerInfos();
        // Remote Service to call
        $this->outputs->service    = $service;
        // Share Debug Flag with Server
        $this->outputs->debug      = (int) SPLASH_DEBUG;
        
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
            $this->outputs->tasks      = $this->tasks;
            $this->outputs->taskscount = count($this->outputs->tasks);
            Splash::log()->deb("[NuSOAP] Call Loaded " . $this->outputs->tasks->count() . " Internal tasks");
        } else {
            $this->outputs->tasks  =   new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        }
        
        //====================================================================//
        // Add External Tasks to the request
        if (!empty($tasks)) {
            $this->outputs->tasks->append($tasks);
            $this->outputs->taskscount = count($tasks);
            Splash::log()->deb("[NuSOAP] Call Loaded " . count($tasks) . " External tasks");
        }
        
        return true;
    }
    
    /**
     * @abstract   Create & Setup WebService Client
     * @return     true
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
     * @param      bool     $isUncrypted      Force message not to be crypted (Used for Ping Only)
     * @return     bool
     */
    private function decodeResponse($isUncrypted)
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
        // Decode & Store Generic SOAP Errors if present
        if ($this->rawIn instanceof \SoapFault) {
            //====================================================================//
            //  Debug Informations
            Splash::log()->deb("[SOAP] Fault Details= "   . $this->rawIn->getTraceAsString());
            //====================================================================//
            //  Errro Message
            return Splash::log()->err("ErrWsNuSOAPFault", $this->rawIn->getCode(), $this->rawIn->getMessage());
        }
        
        //====================================================================//
        // Unpack NuSOAP Answer
        //====================================================================//
        if (!empty($this->rawIn)) {
            //====================================================================//
            // Unpack Data from Raw packet
            $this->inputs = $this->unPack($this->rawIn, $isUncrypted);
            //====================================================================//
            // Merge Logging Messages from remote with current class messages
            if (isset($this->inputs->log)) {
                Splash::log()->merge($this->inputs->log);
            }
        } else {
            //====================================================================//
            //  Add Information to Debug Log
            Splash::log()->deb("[NuSOAP] Id='"          . print_r($this->id, true) . "'");
            //====================================================================//
            //  Error Message
            return Splash::log()->err("ErrWsNoResponse", $this->outputs->service, $this->url);
        }
        
        return true;
    }
    
    //====================================================================//
    //  TASKS STORAGE MANAGEMENT
    //====================================================================//
   
    /**
     * @abstract    Add a new task for NuSOAP Call Request
     *
     * @param   string      $name       Task Identifier Name (Listed in OsWs.inc.php)
     * @param   array       $params     Task Parameters
     * @param   string      $desc       Task Name/Description
     *
     * @return  $this
     */
    public function addTask($name, $params, $desc = "No Description")
    {
        //====================================================================//
        // Create a new task
        $task   =   new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        //====================================================================//
        // Prepare Task Id
        $taskId             =   $this->tasks->count() + 1;
        //====================================================================//
        // Fill task with informations
        $task["id"]         =   $taskId;
        $task["name"]       =   $name;
        $task["desc"]       =   $desc;
        $task["params"]     =   $params;
        //====================================================================//
        // Add Task to Tasks list
        $this->tasks[$taskId]       = $task;
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
     *
     * @return       ArrayObject   $Response
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function getServerInfos()
    {
        //====================================================================//
        // Init Result Array
        $response = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        
        //====================================================================//
        // Server Infos
        $response->ServerType       = "PHP";                            // INFO - Server Language Type
        $response->ServerVersion    = phpversion();                     // INFO - Server Language Version
        $response->ProtocolVersion  = SPL_PROTOCOL;                     // INFO - Server Protocal Version
        //====================================================================//
        // Server Infos
        $response->Self             = Splash::input("PHP_SELF");           // INFO - Current Url
        $response->ServerAddress    = Splash::input("SERVER_ADDR");        // INFO - Server IP Address
        // Read System Folder without symlinks
        $response->ServerRoot       = realpath(Splash::input("DOCUMENT_ROOT"));
        $response->UserAgent        = Splash::input("HTTP_USER_AGENT");    // INFO - Browser User Agent
        $response->WsMethod         = Splash::configuration()->WsMethod;    // Current Splash WebService Component

        //====================================================================//
        // Server Urls
        //====================================================================//
        // CRITICAL - Server Host Name
        // Check if Overiden by Application Module
        if (isset(Splash::configuration()->ServerHost)) {
            $response->ServerHost   =   Splash::configuration()->ServerHost;
        // Check if Available with Secured Reading
        } elseif (!empty(Splash::input("SERVER_NAME"))) {
            $response->ServerHost   = Splash::input("SERVER_NAME");
        // Fallback to Unsecured Mode (Required for Phpunit)
        } else {
            $response->ServerHost   = $_SERVER["SERVER_NAME"];
        }
        
        //====================================================================//
        // Server IPv4 Address
        $response->ServerIP        = Splash::input("SERVER_ADDR");
        
        //====================================================================//
        // Server WebService Path
        if (isset(Splash::configuration()->ServerPath)) {
            $response->ServerPath      =   Splash::configuration()->ServerPath;
        } else {
            $fullPath   =   dirname(__DIR__);
            $relPath    =   explode($response->ServerRoot, $fullPath);
            if (isset($relPath[1])) {
                $response->ServerPath  =   $relPath[1] . "/soap.php";
            } else {
                $response->ServerPath  =   null;
            }
        }
        
        $response->setFlags(ArrayObject::STD_PROP_LIST);
        
        return $response;
    }

    /**
     * @abstract     Return Server Outputs Buffer
     * @return       array   $result
     */
    public function getOutputBuffer()
    {
        return $this->outputs;
    }
    
    /**
     * @abstract   Get Client Server Schema (http or https)
     * @return  string
     */
    public function getServerScheme()
    {
        if ((! empty(Splash::input('REQUEST_SCHEME')) && (Splash::input('REQUEST_SCHEME') == 'https')) ||
             (! empty(Splash::input('HTTPS')) && (Splash::input('HTTPS') == 'on')) ||
             (! empty(Splash::input('SERVER_PORT')) && (Splash::input('SERVER_PORT') == '443'))) {
            return 'https';
        }
        return "http";
    }
    
    /**
     * @abstract   Build WebService Client Url
     * @return  string
     */
    private function getClientUrl()
    {
        //====================================================================//
        // Fetch Server Informations
        $serverInfos    = $this->getServerInfos();
        //====================================================================//
        // Build Server Url
        $host   =   $serverInfos["ServerHost"];
        if ((strpos($host, "http://") !== false) || (strpos($host, "https://") !== false)) {
            return $host . $serverInfos["ServerPath"];
        }
        return $this->getServerScheme() . "://" . $host . $serverInfos["ServerPath"];
    }
    
    /**
     * @abstract   Build WebService Client Debug Html Link
     * @return  string
     */
    private function getClientDebugLink()
    {
        //====================================================================//
        // Compute target client debug url
        $url    = $this->getClientUrl();
        $params = "?node=" . $this->id;
        return '<a href="' . $url . $params . '" target="_blank" >' . $url . '</a>';
    }
    
    //====================================================================//
    //  WEBSERVICE SELF-TESTS
    //====================================================================//
    
    /**
     * @abstract   Check Reverse Connexion with THIS Client
     * @return  bool
     */
    public function selfTest()
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace(__CLASS__, __FUNCTION__);
        //====================================================================//
        // Clone Webservice Class
        $testClient = clone $this;
        //====================================================================//
        // Setup Webservice Class
        $testClient->host  = $this->getClientUrl();
        
        //====================================================================//
        // Run SOAP Call - Reverse Ping
        $ping = $testClient->call(SPL_S_PING, null, 1);
        if (empty($ping) || !isset($ping->result) || !$ping->result) {
            Splash::log()->err(Splash::trans("ErrReversePing", $testClient->host));
            return Splash::log()->err(Splash::trans("ErrReverseDebug", $this->getClientDebugLink()));
        }
        
        //====================================================================//
        // Run SOAP Call - Reverse Connect
        $connect = $testClient->call(SPL_S_CONNECT, array());
        if (empty($connect) || !isset($connect->result) || !$connect->result) {
            Splash::log()->err(Splash::trans("ErrReverseConnect", $testClient->host));
            return Splash::log()->err(Splash::trans("ErrReverseDebug", $this->getClientDebugLink()));
        }
        Splash::log()->msg(Splash::trans("MsgReverseConnect"));
        
        return true;
    }
}
