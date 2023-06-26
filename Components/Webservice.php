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

namespace   Splash\Components;

use ArrayObject;
use Splash\Core\SplashCore      as Splash;
use Splash\Server\SplashServer;

/**
 * This Class Manage Low Level SOAP & NUSOAP WebService Requests
 */
class Webservice
{
    //====================================================================//
    // WebService Parameters
    //====================================================================//

    /**
     * Default Url for Splash Sync Server
     *
     * @var string
     */
    const SPLASHHOST = 'www.splashsync.com/ws/soap';

    /**
     * Webservice Call Url
     *
     * @var string
     */
    public string $url = self::SPLASHHOST;

    /**
     * Remote Server Address
     *
     * @var string
     */
    protected string $host = self::SPLASHHOST;

    /**
     * Unique Client Identifier (+8 Char)
     *
     * @var null|string
     */
    protected ?string $id = null;

    /**
     * Unique Key for encrypt data transmission with this Server
     *
     * @var null|string
     */
    protected ?string $key = null;

    /**
     * Enable Http Authentification
     *
     * @var bool
     */
    protected bool $httpAuth = false;

    /**
     * Http Authentification User
     *
     * @var null|string
     */
    protected ?string $httpUser = null;

    /**
     * Http Authentification Pwd
     *
     * @var null|string
     */
    protected ?string $httpPassword = null;

    /**
     * Webservice Tasks Buffer
     *
     * @var array
     */
    private array $tasks = array();

    /**
     * Webservice Input Buffer
     *
     * @var null|array
     */
    private ?array $inputs = array();

    /**
     * Webservice Output Buffer
     *
     * @var array
     */
    private array $outputs = array();

    /**
     * Raw Call Input Buffer
     *
     * @var null|string
     */
    private ?string $rawIn = null;

    //====================================================================//
    //  WEBSERVICE PARAMETERS MANAGEMENT
    //====================================================================//

    /**
     * Initialise Class with webservice parameters
     *
     * @return bool
     */
    public function setup(): bool
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
     * @param array $data         Input Data ArrayObject
     * @param bool  $noEncryption Force NO encryption on message
     *
     * @return null|string Output Packet Data (Encrypted or not)
     */
    public function pack(array $data, bool $noEncryption = false): ?string
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
            $serial = Splash::xml()->arrayToXml($data);
        } else {
            //====================================================================//
            // Serialize Data Buffer
            $serial = serialize($data);
        }

        //====================================================================//
        // Encrypt serialized data buffer
        //====================================================================//
        if (!$noEncryption) {
            $out = $this->crypt('encrypt', $serial, (string) $this->key, (string) $this->id);
        } else {
            //====================================================================//
            // Else, switch to base64
            $out = base64_encode($serial);
        }

        //====================================================================//
        //  Debug Informations
        //====================================================================//
        if (Splash::isServerMode() && (Splash::configuration()->TraceOut)) {
            Splash::log()->war('MsgWsFinalPack', print_r($serial, true));
        }

        return $out;
    }

    /**
     * Unpack received Data Packets.
     *
     * @param string $data         Input Data
     * @param bool   $noEncryption Force NO encryption on message
     *
     * @return null|array
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function unPack(string $data, bool $noEncryption = false): ?array
    {
        //====================================================================//
        // Debug Log
        Splash::log()->deb('MsgWsunPack');

        //====================================================================//
        // Decrypt response
        //====================================================================//
        if (!empty($data) && !$noEncryption) {
            $decode = $this->crypt('decrypt', $data, (string) $this->key, (string) $this->id);
        } else {
            //====================================================================//
            // Else, switch from base64
            $decode = base64_decode($data, true);
        }

        //====================================================================//
        // Decode Data Response
        //====================================================================//
        // Convert Data Buffer To XML
        $out = null;
        if ('XML' == Splash::configuration()->WsEncode) {
            if ($decode && false !== strpos($decode, '<SPLASH>')) {
                $out = Splash::xml()->xmlToArray($decode);
            }
        } else {
            //====================================================================//
            // Deserialize Data buffer
            if (!empty($decode)) {
                $decoded = unserialize($decode);
                $out = is_array($decoded) ? $decoded : null;
            }
        }

        //====================================================================//
        // Trow Exception if fails
        if (empty($out)) {
            Splash::log()->err('ErrWsunPack');
        }

        //====================================================================//
        // Return Result or False
        return $out ?: null;
    }

    //====================================================================//
    //  CORE WEBSERVICE FUNCTIONS
    //====================================================================//

    /**
     * Perform operation with WebService Client
     *
     * @param string     $service      server method to use
     * @param null|array $tasks        List of task to perform inside this request.
     *                                 If NULL, internal task list is used.
     * @param bool       $noEncryption Force message not to be encrypted (Used for Ping Only)
     * @param bool       $clean        Clean task buffer at the end of this function
     *
     * @return null|array<string, null|array<string, null|array|scalar>|scalar>
     */
    public function call(
        string $service,
        array $tasks = null,
        bool $noEncryption = false,
        bool $clean = true
    ): ?array {
        //====================================================================//
        // WebService Call =>> Initialisation
        if (!$this->init($service)) {
            return null;
        }
        //====================================================================//
        // WebService Call =>> Add Tasks
        if (!$this->addTasks($tasks)) {
            return null;
        }
        //====================================================================//
        // Prepare Raw Request Data
        //====================================================================//
        $rawOut = array(
            'id' => $this->id,
            'data' => $this->pack($this->outputs, $noEncryption),
        );
        //====================================================================//
        // Prepare Webservice Client
        //====================================================================//
        if (false === $this->buildClient()) {
            return null;
        }
        //====================================================================//
        // Call Execution
        $this->rawIn = Splash::com()->call($this->outputs['service'], $rawOut);

        //====================================================================//
        // Analyze & Decode Response
        //====================================================================//
        if (!$this->decodeResponse($noEncryption)) {
            return null;
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
     * @param string     $service      server method to use
     * @param null|array $tasks        List of task to perform inside this request.
     *                                 If NULL, internal task list is used.
     * @param bool       $noEncryption Force message not to be encrypted (Used for Ping Only)
     * @param bool       $clean        Clean task buffer at the end of this function
     *
     * @return null|string
     */
    public function simulate(
        string $service,
        array $tasks = null,
        bool $noEncryption = false,
        bool $clean = true
    ): ?string {
        //====================================================================//
        // WebService Call =>> Initialisation
        if (!$this->init($service)) {
            return null;
        }
        //====================================================================//
        // WebService Call =>> Add Tasks
        if (!$this->addTasks($tasks)) {
            return null;
        }
        //====================================================================//
        //   Execute Action From Splash Server to Module
        $response = SplashServer::$service(
            Splash::configuration()->WsIdentifier,
            $this->pack($this->outputs, $noEncryption)
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
     * @param string $name   Task Identifier Name (Listed in OsWs.inc.php)
     * @param array  $params Task Parameters
     * @param string $desc   Task Name/Description
     *
     * @return $this
     */
    public function addTask(string $name, array $params, string $desc = 'No Description'): self
    {
        //====================================================================//
        // Prepare Task Id
        $taskId = count($this->tasks) + 1;
        //====================================================================//
        // Fill task with informations
        $task = array(
            'id' => $taskId,
            'name' => $name,
            'desc' => $desc,
            'params' => $params,
        );
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
     * @param null|array $response Webservice Call Response
     *
     * @return null|array
     */
    public static function getNextResult(?array &$response): ?array
    {
        //====================================================================//
        // Extract Next Task From Buffer
        $task = self::getNextTask($response);
        //====================================================================//
        // Return Task Data
        return $task['data'] ?? null;
    }

    /**
     * Get Next Task Available in Response Tasks Buffer
     *
     * @param null|array[] $response Webservice Call Response
     *
     * @return null|array
     */
    public static function getNextTask(?array &$response): ?array
    {
        //====================================================================//
        // Analyze SOAP Results
        if (!$response || empty($response['result'] ?? false)) {
            return null;
        }
        //====================================================================//
        // Check if Tasks Buffer is Empty
        if (empty($response['tasks'] ?? null)) {
            return null;
        }
        //====================================================================//
        // Shift Task Array
        return array_shift($response['tasks']);
    }

    //====================================================================//
    //  INFORMATION RETRIEVAL
    //====================================================================//

    /**
     * Return Server Information
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function getServerInfos(): array
    {
        //====================================================================//
        // Init Result Array
        $response = array();

        //====================================================================//
        // INFO - Server Language Type
        $response['ServerType'] = 'PHP';
        // INFO - Server Language Version
        $response['ServerVersion'] = PHP_VERSION;
        // INFO - Server Protocol Version
        $response['ProtocolVersion'] = SPL_PROTOCOL;
        //====================================================================//
        // INFO - Current Url
        $response['Self'] = Splash::input('PHP_SELF');
        // INFO - Server IP Address
        $response['ServerAddress'] = Splash::input('SERVER_ADDR');
        // Read System Folder without symlinks
        $response['ServerRoot'] = realpath((string) Splash::input('DOCUMENT_ROOT'));
        // INFO - Browser User Agent
        $response['UserAgent'] = Splash::input('HTTP_USER_AGENT');
        // Current Splash WebService Component
        $response['WsMethod'] = Splash::configuration()->WsMethod;

        //====================================================================//
        // Server Urls
        //====================================================================//
        // CRITICAL - Server Host Name
        $response['ServerHost'] = $this->getServerName();

        //====================================================================//
        // Server IPv4 Address
        $response['ServerIP'] = Splash::input('SERVER_ADDR');

        //====================================================================//
        // Server WebService Path
        if (isset(Splash::configuration()->ServerPath)) {
            $response['ServerPath'] = Splash::configuration()->ServerPath;
        } elseif (!empty($response['ServerRoot'])) {
            $fullPath = dirname(__DIR__);
            $relPath = explode((string) $response['ServerRoot'], $fullPath);
            if (is_array($relPath) && isset($relPath[1])) {
                $response['ServerPath'] = $relPath[1].'/soap.php';
            } else {
                $response['ServerPath'] = null;
            }
        } else {
            $response['ServerPath'] = null;
        }

        return $response;
    }

    /**
     * Return Server Outputs Buffer
     *
     * @return array
     */
    public function getOutputBuffer(): array
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
    public function getServerName(): string
    {
        //====================================================================//
        // Check if Server Name is Overriden by Application Module
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
    public function getServerScheme(): string
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
    public function selfTest(): bool
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
        if (empty($ping) || empty($ping['result'] ?? false)) {
            Splash::log()->err(Splash::trans('ErrReversePing', $testClient->host));

            return Splash::log()->err(Splash::trans('ErrReverseDebug', $this->getClientDebugLink()));
        }

        //====================================================================//
        // Run SOAP Call - Reverse Connect
        $connect = $testClient->call(SPL_S_CONNECT, array());
        if (empty($connect) || empty($connect['result'] ?? false)) {
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
    private function verify(): bool
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
     * @param string $data         Input Data
     * @param string $sharedKey    Encoding Shared Key
     * @param string $sharedVector Encoding Shared IV (Initialisation Vector)
     *
     * @return null|string
     */
    private function crypt(string $action, string $data, string $sharedKey, string $sharedVector): ?string
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
            return Splash::log()->errNull('ErrWsCryptAction');
        }
        //====================================================================//
        // Verify All Parameters are given
        if (empty($data) || empty($sharedKey) || empty($sharedVector)) {
            return Splash::log()->errNull('ErrParamMissing', __FUNCTION__);
        }
        //====================================================================//
        // Init output as error value
        $out = null;
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
        } else {
            //====================================================================//
            // Open SSL Decryption
            $out = openssl_decrypt(
                (string) base64_decode($data, true),
                Splash::configuration()->WsCrypt,
                $cryptKey,
                0,
                $cryptIv
            );
        }

        return $out ?: null;
    }

    /**
     * Clean Ws Input Buffer before Call Request
     *
     * @return void
     */
    private function cleanIn(): void
    {
        //====================================================================//
        //  Initiate a new input buffer
        $this->inputs = array();
    }

    /**
     * Clean parameters of Ws Call Request
     *
     * @return void
     */
    private function cleanOut(): void
    {
        //====================================================================//
        //  Initiate a new tasks list
        $this->tasks = array();
        //====================================================================//
        //  Initiate a new output buffer
        $this->outputs = array();
    }

    /**
     * Init WebService Call
     *
     * @param string $service server method to use
     *
     * @return bool
     */
    private function init(string $service): bool
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
        $this->outputs['server'] = $this->getServerInfos();
        // Remote Service to call
        $this->outputs['service'] = $service;
        // Share Debug Flag with Server
        $this->outputs['debug'] = (int) Splash::isDebugMode();
        // Share Verbose Flag with Server
        $this->outputs['verbose'] = (int) Splash::log()->isDebugMode();

        return true;
    }

    /**
     * Add Tasks to WebService Request
     *
     * @param null|array $tasks List of task to perform inside this request.
     *                          If NULL, internal task list is used.
     *
     * @return bool
     */
    private function addTasks(?array $tasks): bool
    {
        //====================================================================//
        // No tasks to Add
        if (is_null($tasks) && (0 == count($this->tasks))) {
            return true;
        }

        //====================================================================//
        // Prepare Tasks To Perform
        //====================================================================//

        //====================================================================//
        // Add Internal Tasks to buffer
        if (count($this->tasks)) {
            $this->outputs['tasks'] = $this->tasks;
            $this->outputs['taskscount'] = count($this->tasks);
            Splash::log()->deb('[WS] Call Loaded '.count($this->tasks).' Internal tasks');
        } else {
            $this->outputs['tasks'] = array();
        }

        //====================================================================//
        // Add External Tasks to the request
        if (!empty($tasks)) {
            $this->outputs['tasks'] = array_merge($this->outputs['tasks'], $tasks);
            $this->outputs['taskscount'] = count($this->outputs['tasks']);
            Splash::log()->deb('[WS] Call Loaded '.count($tasks).' External tasks');
        }

        return true;
    }

    /**
     * Create & Setup WebService Client
     *
     * @return bool
     */
    private function buildClient(): bool
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
     * @param bool $noEncryption Force message not to be encrypted (Used for Ping Only)
     *
     * @return bool
     */
    private function decodeResponse(bool $noEncryption): bool
    {
        //====================================================================//
        // Unpack NuSOAP Answer
        //====================================================================//
        if (!empty($this->rawIn)) {
            //====================================================================//
            // Unpack Data from Raw packet
            $this->inputs = $this->unPack($this->rawIn, $noEncryption);
            //====================================================================//
            // Merge Logging Messages from remote with current class messages
            Splash::log()->merge($this->inputs['log'] ?? array());
        } else {
            //====================================================================//
            //  Add Information to Debug Log
            Splash::log()->deb("[WS] Id='".print_r($this->id, true)."'");
            //====================================================================//
            //  Error Message
            return Splash::log()->err('ErrWsNoResponse', $this->outputs['service'] ?? "", $this->url);
        }

        return true;
    }

    /**
     * Build WebService Client Url
     *
     * @return string
     */
    private function getClientUrl(): string
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
    private function getClientDebugLink(): string
    {
        //====================================================================//
        // Compute target client debug url
        $url = $this->getClientUrl();
        $params = '?node='.$this->id;

        return '<a href="'.$url.$params.'" target="_blank" >'.$url.'</a>';
    }
}
