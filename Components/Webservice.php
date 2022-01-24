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

namespace   Splash\Components;

use ArrayObject;
use Splash\Core\SplashCore      as Splash;
use Splash\Server\SplashServer;

/**
 * This Class Manage Low Level NUSOAP WebService Requests
 *
 * @author      B. Paquier <contact@splashsync.com>
 */
class Webservice
{
    //====================================================================//
    // WebService Parameters
    //====================================================================//

    /** @var string */
    const       SPLASHHOST = 'www.splashsync.com/ws/soap';

    /**
     * Webservice Call Url
     *
     * @var string
     */
    public $url;

    /**
     * Remote Server Address
     *
     * @var string
     */
    protected $host = self::SPLASHHOST;

    /**
     * Unik Client Identifier ( +8 Char)
     *
     * @var string
     */
    protected $id = '';

    /**
     * Unik Key for encrypt data transmission with this Server
     *
     * @var string
     */
    protected $key = '';

    /**
     * Enable Http Authentification
     *
     * @var bool
     */
    protected $httpAuth = false;

    /**
     * Http Authentification User
     *
     * @var string
     */
    protected $httpUser;

    /**
     * Http Authentification Pwd
     *
     * @var string
     */
    protected $httpPassword;

    /**
     * Webservice Tasks Buffer
     *
     * @var ArrayObject
     */
    private $tasks;

    /**
     * Webservice Input Buffer
     *
     * @var ArrayObject
     */
    private $inputs;

    /**
     * Webservice Output Buffer
     *
     * @var ArrayObject
     */
    private $outputs;

    /**
     * Raw Call Input Buffer
     *
     * @var string
     */
    private $rawIn;

    /**
     * Raw Call Output Buffer
     *
     * @var array
     */
    private $rawOut;

    /**
     * Initialise Class with empty webservice parameters
     */
    public function __construct()
    {
        //====================================================================//
        // Initialize Tasks List
        $this->tasks = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        //====================================================================//
        // Initialize I/O Data Buffers
        $this->inputs = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        $this->outputs = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
    }

    //====================================================================//
    //  WEBSERVICE PARAMETERS MANAGEMENT
    //====================================================================//

    /**
     * Initialise Class with webservice parameters
     *
     * @return bool
     */
    public function setup()
    {
        //====================================================================//
        // Read Parameters
        $this->id = Splash::configuration()->WsIdentifier;
        $this->key = Splash::configuration()->WsEncryptionKey;

        //====================================================================//
        // If Another Host is Defined => Allow Override of Server Host Address
        if (!empty(Splash::configuration()->WsHost)) {
            $this->host = Splash::configuration()->WsHost;
        } else {
            $this->host = self::SPLASHHOST;
        }

        //====================================================================//
        // If Http Auth is Required => Setup User & Password
        if (isset(Splash::configuration()->HttpAuth) && !empty(Splash::configuration()->HttpAuth)) {
            $this->httpAuth = true;
        }
        if ($this->httpAuth && isset(Splash::configuration()->HttpUser)) {
            $this->httpUser = Splash::configuration()->HttpUser;
        }
        if ($this->httpAuth && isset(Splash::configuration()->HttpPassword)) {
            $this->httpPassword = Splash::configuration()->HttpPassword;
        }

        //====================================================================//
        // Safety Check
        if (!$this->verify()) {
            return false;
        }

        return Splash::log()->deb('MsgWsSetParams');
    }

    /**
     * Prepare Data Packets for transmit.
     *
     * @param array|ArrayObject $data        Input Data ArrayObject
     * @param bool              $isUncrypted force no encrypt on message
     *
     * @return false|string $Out            Output Packet Data ( Encrypted or not )
     */
    public function pack($data, $isUncrypted = false)
    {
        //====================================================================//
        // Debug Log
        Splash::log()->deb('MsgWsPack');

        //====================================================================//
        // Encode Data Buffer
        //====================================================================//
        if ('XML' == Splash::configuration()->WsEncode) {
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
            $out = $this->crypt('encrypt', $serial, $this->key, $this->id);
        //====================================================================//
        // Else, switch to base64
        } else {
            $out = base64_encode($serial);
        }

        //====================================================================//
        //  Debug Informations
        //====================================================================//
        if (defined('SPLASH_SERVER_MODE') && !empty(SPLASH_SERVER_MODE) && (Splash::configuration()->TraceOut)) {
            Splash::log()->war('MsgWsFinalPack', print_r($serial, true));
        }

        return $out;
    }

    /**
     * Unpack received Data Packets.
     *
     * @param string $data        Input Data
     * @param bool   $isUncrypted force no encrypt on message
     *
     * @return ArrayObject
     */
    public function unPack($data, $isUncrypted = false)
    {
        //====================================================================//
        // Debug Log
        Splash::log()->deb('MsgWsunPack');

        //====================================================================//
        // Decrypt response
        //====================================================================//
        if (!empty($data) && !$isUncrypted) {
            $decode = $this->crypt('decrypt', $data, $this->key, $this->id);
        //====================================================================//
        // Else, switch from base64
        } else {
            $decode = base64_decode($data, true);
        }

        //====================================================================//
        // Decode Data Response
        //====================================================================//
        // Convert Data Buffer To XML
        if ('XML' == Splash::configuration()->WsEncode) {
            if ($decode && false !== strpos($decode, '<SPLASH>')) {
                $out = Splash::xml()->xmlToArrayObject($decode);
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
            Splash::log()->err('ErrWsunPack');
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
        return empty($out) ? false : $out;
    }

    //====================================================================//
    //  CORE WEBSERVICE FUNCTIONS
    //====================================================================//

    /**
     * Perform operation with WebService Client
     *
     * @param string $service     server method to use
     * @param array  $tasks       List of task to perform inside this request.
     *                            If NULL, internal task list is used.
     * @param bool   $isUncrypted Force message not to be crypted (Used for Ping Only)
     * @param bool   $clean       Clean task buffer at the end of this function
     *
     * @return ArrayObject|false
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
            'id' => $this->id,
            'data' => $this->pack($this->outputs, $isUncrypted),
        );
        //====================================================================//
        // Prepare Webservice Client
        //====================================================================//
        if (false === $this->buildClient()) {
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
     * Simulate operation on Local WebService Client
     *
     * @param string $service     server method to use
     * @param array  $tasks       List of task to perform inside this request.
     *                            If NULL, internal task list is used.
     * @param bool   $isUncrypted Force message not to be crypted (Used for Ping Only)
     * @param bool   $clean       Clean task buffer at the end of this function
     *
     * @return false|string
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
        $response = SplashServer::$service(
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

    //====================================================================//
    //  TASKS STORAGE MANAGEMENT
    //====================================================================//

    /**
     * Add a new task for NuSOAP Call Request
     *
     * @param string            $name   Task Identifier Name (Listed in OsWs.inc.php)
     * @param array|ArrayObject $params Task Parameters
     * @param string            $desc   Task Name/Description
     *
     * @return $this
     */
    public function addTask($name, $params, $desc = 'No Description')
    {
        //====================================================================//
        // Create a new task
        $task = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        //====================================================================//
        // Prepare Task Id
        $taskId = $this->tasks->count() + 1;
        //====================================================================//
        // Fill task with informations
        $task['id'] = $taskId;
        $task['name'] = $name;
        $task['desc'] = $desc;
        $task['params'] = $params;
        //====================================================================//
        // Add Task to Tasks list
        $this->tasks[$taskId] = $task;
        //====================================================================//
        // Debug
        Splash::log()->deb('TasksAdd', $task['name'], $task['desc']);

        return $this;
    }

    /**
     * Get Next Task Result Available in Response Tasks Buffer
     *
     * @param ArrayObject|false $response Webservice Call Response
     *
     * @return false|mixed
     */
    public static function getNextResult(&$response)
    {
        //====================================================================//
        // Extract Next Task From Buffer
        $task = self::getNextTask($response);
        //====================================================================//
        // Analyze SOAP Results
        if (!$task || !isset($task->data)) {
            return false;
        }
        //====================================================================//
        // Return Task Data
        return $task->data;
    }

    /**
     * Get Next Task Available in Response Tasks Buffer
     *
     * @param ArrayObject|false $response Webservice Call Response
     *
     * @return false|mixed
     */
    public static function getNextTask(&$response)
    {
        //====================================================================//
        // Analyze SOAP Results
        if (!$response || !isset($response->result) || (true != $response->result)) {
            return false;
        }
        //====================================================================//
        // Check if Tasks Buffer is Empty
        if (!Splash::count($response->tasks)) {
            return false;
        }
        //====================================================================//
        // Detect ArrayObjects
        if ($response->tasks instanceof ArrayObject) {
            $response->tasks = $response->tasks->getArrayCopy();
        }
        //====================================================================//
        // Shift Task Array
        return array_shift($response->tasks);
    }

    //====================================================================//
    //  INFORMATION RETRIEVAL
    //====================================================================//

    /**
     * Return Server Information
     *
     * @return ArrayObject $Response
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
        $response->ServerType = 'PHP';                              // INFO - Server Language Type
        $response->ServerVersion = PHP_VERSION;                     // INFO - Server Language Version
        $response->ProtocolVersion = SPL_PROTOCOL;                  // INFO - Server Protocal Version
        //====================================================================//
        // Server Infos
        $response->Self = Splash::input('PHP_SELF');                // INFO - Current Url
        $response->ServerAddress = Splash::input('SERVER_ADDR');    // INFO - Server IP Address
        // Read System Folder without symlinks
        $response->ServerRoot = realpath((string) Splash::input('DOCUMENT_ROOT'));
        $response->UserAgent = Splash::input('HTTP_USER_AGENT');    // INFO - Browser User Agent
        $response->WsMethod = Splash::configuration()->WsMethod;    // Current Splash WebService Component

        //====================================================================//
        // Server Urls
        //====================================================================//
        // CRITICAL - Server Host Name
        $response->ServerHost = $this->getServerName();

        //====================================================================//
        // Server IPv4 Address
        $response->ServerIP = Splash::input('SERVER_ADDR');

        //====================================================================//
        // Server WebService Path
        if (isset(Splash::configuration()->ServerPath)) {
            $response->ServerPath = Splash::configuration()->ServerPath;
        } elseif (!empty($response->ServerRoot)) {
            $fullPath = dirname(__DIR__);
            $relPath = explode((string) $response->ServerRoot, $fullPath);
            if (is_array($relPath) && isset($relPath[1])) {
                $response->ServerPath = $relPath[1].'/soap.php';
            } else {
                $response->ServerPath = null;
            }
        } else {
            $response->ServerPath = null;
        }

        $response->setFlags(ArrayObject::STD_PROP_LIST);

        return $response;
    }

    /**
     * Return Server Outputs Buffer
     *
     * @return ArrayObject
     */
    public function getOutputBuffer()
    {
        return $this->outputs;
    }

    /**
     * Safe Get Client Server Url
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function getServerName()
    {
        //====================================================================//
        // Check if Server Name is Overiden by Application Module
        if (isset(Splash::configuration()->ServerHost)) {
            return Splash::configuration()->ServerHost;
        }
        //====================================================================//
        // Check if Available with Secured Reading
        if (!empty(Splash::input('SERVER_NAME'))) {
            return Splash::input('SERVER_NAME');
        }
        //====================================================================//
        // Fallback to Unsecured Mode (Required for Phpunit)
        if (isset($_SERVER['SERVER_NAME'])) {
            return $_SERVER['SERVER_NAME'];
        }

        return '';
    }

    /**
     * Get Client Server Schema (http or https)
     *
     * @return string
     */
    public function getServerScheme()
    {
        if ((!empty(Splash::input('REQUEST_SCHEME')) && ('https' == Splash::input('REQUEST_SCHEME'))) ||
             (!empty(Splash::input('HTTPS')) && ('on' == Splash::input('HTTPS'))) ||
             (!empty(Splash::input('SERVER_PORT')) && ('443' == Splash::input('SERVER_PORT')))) {
            return 'https';
        }

        return 'http';
    }

    //====================================================================//
    //  WEBSERVICE SELF-TESTS
    //====================================================================//

    /**
     * Check Reverse Connexion with THIS Client
     *
     * @return bool
     */
    public function selfTest()
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();
        //====================================================================//
        // Clone Webservice Class
        $testClient = clone $this;
        //====================================================================//
        // Setup Webservice Class
        $testClient->host = $this->getClientUrl();

        //====================================================================//
        // Varnish Detection
        if (function_exists('getallheaders') && array_key_exists("X-Varnish", getallheaders())) {
            Splash::log()->war("Varnish detected: if webservice fail, disable ESI mode...");
        }

        //====================================================================//
        // Run SOAP Call - Reverse Ping
        $ping = $testClient->call(SPL_S_PING, null, true);
        if (empty($ping) || !isset($ping->result) || !$ping->result) {
            Splash::log()->err(Splash::trans('ErrReversePing', $testClient->host));

            return Splash::log()->err(Splash::trans('ErrReverseDebug', $this->getClientDebugLink()));
        }

        //====================================================================//
        // Run SOAP Call - Reverse Connect
        $connect = $testClient->call(SPL_S_CONNECT, array());
        if (empty($connect) || !isset($connect->result) || !$connect->result) {
            Splash::log()->err(Splash::trans('ErrReverseConnect', $testClient->host));

            return Splash::log()->err(Splash::trans('ErrReverseDebug', $this->getClientDebugLink()));
        }
        Splash::log()->msg(Splash::trans('MsgReverseConnect'));

        return true;
    }

    /**
     * Verify Webservice parameters
     *
     * @return bool
     */
    private function verify()
    {
        //====================================================================//
        // Verify host address is present
        if (empty($this->host)) {
            return Splash::log()->err('ErrWsNoHost');
        }

        //====================================================================//
        // Verify Server Id not empty
        if (empty($this->id)) {
            return Splash::log()->err('ErrWsNoId');
        }

        //====================================================================//
        // Verify Server Id not empty
        if (empty($this->key)) {
            return Splash::log()->err('ErrWsNoKey');
        }

        //====================================================================//
        // Verify Http Auth Configuration
        if ($this->httpAuth) {
            if (empty($this->httpUser)) {
                return Splash::log()->err('ErrWsNoHttpUser');
            }
            if (empty($this->httpPassword)) {
                return Splash::log()->err('ErrWsNoHttpPwd');
            }
        }

        return true;
    }

    //====================================================================//
    //  DATA BUFFER MANAGEMENT
    //====================================================================//

    /**
     * Encrypt/Decrypt Serialized Data Object
     *
     * @param string $action       Action to perform on Data (encrypt/decrypt)
     * @param mixed  $data         Input Data
     * @param string $sharedKey    Encoding Shared Key
     * @param string $sharedVector Encoding Shared IV (Initialisation Vector)
     *
     * @return false|string
     */
    private function crypt($action, $data, $sharedKey, $sharedVector)
    {
        //====================================================================//
        // Safety Check
        //====================================================================//
        // Verify Crypt Direction
        if ('encrypt' == $action) {
            Splash::log()->deb('MsgWsEnCrypt');
        } elseif ('decrypt' == $action) {
            Splash::log()->deb('MsgWsDeCrypt');
        } else {
            return Splash::log()->err('ErrWsCryptAction');
        }
        //====================================================================//
        // Verify All Parameters are given
        if (empty($data) || empty($sharedKey) || empty($sharedVector)) {
            return Splash::log()->err('ErrParamMissing', __FUNCTION__);
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
        if ('encrypt' == $action) {
            $out = base64_encode(
                (string) openssl_encrypt($data, Splash::configuration()->WsCrypt, $cryptKey, 0, $cryptIv)
            );
        //====================================================================//
        // Open SSL Decryption
        } elseif ('decrypt' == $action) {
            $out = openssl_decrypt(
                (string) base64_decode($data, true),
                Splash::configuration()->WsCrypt,
                $cryptKey,
                0,
                $cryptIv
            );
        }
        //====================================================================//
        //  Debug Informations
//        Splash::log()->deb("OsWs Crypt - Secret Key : " . $secret_key . " ==> " . $key );
//        Splash::log()->deb("OsWs Crypt - Secret IV : " . $secret_iv . " ==> " . $iv );
//        Splash::log()->deb("OsWs Crypt - Result : " . $Out);
        return $out;
    }

    /**
     * Clean Ws Input Buffer before Call Request
     *
     * @return true
     */
    private function cleanIn()
    {
        //====================================================================//
        //  Initiate a new input buffer
        $this->inputs = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);

        return true;
    }

    /**
     * Clean parameters of Ws Call Request
     *
     * @return true
     */
    private function cleanOut()
    {
        //====================================================================//
        //  Initiate a new tasks list
        $this->tasks = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        //====================================================================//
        //  Initiate a new output buffer
        $this->outputs = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);

        return true;
    }

    /**
     * Init WebService Call
     *
     * @param string $service server method to use
     *
     * @return bool
     */
    private function init($service)
    {
        //====================================================================//
        // Debug
        Splash::log()->deb('MsgWsCall');
        //====================================================================//
        // Safety Check
        if (!$this->verify()) {
            return Splash::log()->err('ErrWsInValid');
        }
        //====================================================================//
        // Clean Data Input Buffer
        $this->cleanIn();
        //====================================================================//
        // Prepare Data Output Buffer
        //====================================================================//
        // Fill buffer with Server Core infos
        $this->outputs->server = $this->getServerInfos();
        // Remote Service to call
        $this->outputs->service = $service;
        // Share Debug Flag with Server
        $this->outputs->debug = (int) Splash::isDebugMode();
        // Share Verbose Flag with Server
        $this->outputs->verbose = (int) Splash::log()->isDebugMode();

        return true;
    }

    /**
     * Add Tasks to WebService Request
     *
     * @param array $tasks List of task to perform inside this request.
     *                     If NULL, internal task list is used.
     *
     * @return bool
     */
    private function addTasks($tasks = null)
    {
        //====================================================================//
        // No tasks to Add
        if (is_null($tasks) && count($this->tasks)) {
            return true;
        }

        //====================================================================//
        // Prepare Tasks To Perform
        //====================================================================//

        //====================================================================//
        // Add Internal Tasks to buffer
        if (!count($this->tasks)) {
            $this->outputs->tasks = $this->tasks;
            $this->outputs->taskscount = count($this->outputs->tasks);
            Splash::log()->deb('[WS] Call Loaded '.$this->outputs->tasks->count().' Internal tasks');
        } else {
            $this->outputs->tasks = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        }

        //====================================================================//
        // Add External Tasks to the request
        if (!empty($tasks)) {
            $this->outputs->tasks->append($tasks);
            $this->outputs->taskscount = count($tasks);
            Splash::log()->deb('[WS] Call Loaded '.count($tasks).' External tasks');
        }

        return true;
    }

    /**
     * Create & Setup WebService Client
     *
     * @return bool
     */
    private function buildClient()
    {
        //====================================================================//
        // Compute target client url
        if ((false === strpos($this->host, 'http://')) && (false === strpos($this->host, 'https://'))) {
            $this->url = 'https://'.$this->host;
        } else {
            $this->url = $this->host;
        }
        //====================================================================//
        // Build Webservice Client
        Splash::com()->buildClient($this->url, $this->httpUser, $this->httpPassword);

        return true;
    }

    /**
     * Decode WebService Client Response
     *
     * @param bool $isUncrypted Force message not to be crypted (Used for Ping Only)
     *
     * @return bool
     */
    private function decodeResponse($isUncrypted)
    {
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
            Splash::log()->deb("[WS] Id='".print_r($this->id, true)."'");
            //====================================================================//
            //  Error Message
            return Splash::log()->err('ErrWsNoResponse', $this->outputs->service, $this->url);
        }

        return true;
    }

    /**
     * Build WebService Client Url
     *
     * @return string
     */
    private function getClientUrl()
    {
        //====================================================================//
        // Fetch Server Informations
        $serverInfos = $this->getServerInfos();
        //====================================================================//
        // Build Server Url
        $host = $serverInfos['ServerHost'];
        if ((false !== strpos($host, 'http://')) || (false !== strpos($host, 'https://'))) {
            return $host.$serverInfos['ServerPath'];
        }

        return $this->getServerScheme().'://'.$host.$serverInfos['ServerPath'];
    }

    /**
     * Build WebService Client Debug Html Link
     *
     * @return string
     */
    private function getClientDebugLink()
    {
        //====================================================================//
        // Compute target client debug url
        $url = $this->getClientUrl();
        $params = '?node='.$this->id;

        return '<a href="'.$url.$params.'" target="_blank" >'.$url.'</a>';
    }
}
