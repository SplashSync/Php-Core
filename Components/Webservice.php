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

//====================================================================//
//   INCLUDES 
//====================================================================//

//====================================================================//
// NuSOAP WebService Classes
require_once( dirname(dirname(__FILE__)) . "/inc/nusoap.php");

//====================================================================//
//  CLASS DEFINITION
//====================================================================//

class Webservice 
{
    //====================================================================//
    // WebService Parameters	
    //====================================================================//
    
    //====================================================================//
    // Remote Server Address
    private $host   = "www.splashsync.com/ws/soap";                           
    //====================================================================//
    // Unik Client Identifier ( 1 to 8 Char)
    private $id     = "";               
    //====================================================================//
    // Unik Key for encrypt data transmission with this Server
    private $key    = "";              
    //====================================================================//
    // Webservice tasks     
    private $tasks;
    //====================================================================//
    // Webservice Call Url     
    public $url;    
    //====================================================================//
    // Webservice buffers     
    private $_In;                   // Input Buffer
    private $_Out;                  // Output Buffer
    
    /**
     *      @abstract     Initialise Class with empty webservice parameters
     */
    function __construct() {
        //====================================================================//
        // Initialize Tasks List
        $this->tasks        = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        //====================================================================//
        // Initialize I/O Data Buffers
        $this->_In          = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        $this->_Out         = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
    }

//====================================================================//
//  WEBSERVICE PARAMETERS MANAGEMENT
//====================================================================//
    
    
//====================================================================//
//  WEBSERVICE PARAMETERS MANAGEMENT
//====================================================================//

    /**
     *      @abstract   Initialise Class with Empty webservice parameters
     *      @return     int     $result        	<0 if KO, >0 if OK
     */
    public function Clear() {
        
        //====================================================================//
        // Clear WebService Parameters
        $this->id   =   "";
        $this->key  =   "";
        Splash::Log()->Deb("MsgWsClearParams");
        
        return True;
    }

    /**
     *      @abstract   Initialise Class with webservice parameters
     * 
     *      @return     bool
     */
    public function Setup() {
        
        //====================================================================//
        // Read Parameters
        $this->id   =   Splash::Configuration()->WsIdentifier;
        $this->key  =   Splash::Configuration()->WsEncryptionKey;
        
        //====================================================================//
        // If Another Host is Defined => Allow Overide of Server Host Address
        if ( !empty(Splash::Configuration()->WsHost) ) {
            $this->host     =   Splash::Configuration()->WsHost;
        }

        //====================================================================//
        // Safety Check 
        if ( !$this->Verify() ) {
            return False;
        }
        return Splash::Log()->Deb("MsgWsSetParams");
    }

    /**
     *     @abstract    Verify Webservice parameters  
     * 
     *     @return      bool
     */
    public function Verify() {
        
        //====================================================================//
        // Verify host address is present
        if (empty($this->host)) {   
            return Splash::Log()->Err("ErrWsNoHost");
        }
        
        //====================================================================//
        // Verify Server Id not empty
        if (empty($this->id))   {   
            return Splash::Log()->Err("ErrWsNoId");  
        }

        //====================================================================//
        // Verify Server Id not empty
        if (empty($this->key))   {   
            return Splash::Log()->Err("ErrWsNoKey");  
        }

        return True;
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
    public function Crypt($act, $In, $key, $iv) {
        //====================================================================//
        // Safety Check 
        //====================================================================//
        // Verify Crypt Direction
        if ($act == "encrypt")      {   Splash::Log()->Deb("MsgWsEnCrypt");
        } elseif ($act == "decrypt"){   Splash::Log()->Deb("MsgWsDeCrypt");
        } else {                        return $this->Err("ErrWsCryptAction");
        }
        //====================================================================//
        // Verify All Parameters are given
        if (empty($In) || empty($key) || empty($iv)) {
            return Splash::Log()->Err("ErrParamMissing",__FUNCTION__);
        }
        //====================================================================//
        // Init output as error value
        $Out = False;
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
            $Out = base64_encode(openssl_encrypt($In, Splash::Configuration()->WsCrypt, $CryptKey, 0, $CryptIv));
        }
        //====================================================================//
        // Open SSL Decryption
        else if ($act == 'decrypt') {
            $Out = openssl_decrypt(base64_decode($In), Splash::Configuration()->WsCrypt, $CryptKey, 0, $CryptIv);
        }
        //====================================================================//
        //  Debug Informations
//        Splash::Log()->Deb("OsWs Crypt - Secret Key : " . $secret_key . " ==> " . $key );
//        Splash::Log()->Deb("OsWs Crypt - Secret IV : " . $secret_iv . " ==> " . $iv );
//        Splash::Log()->Deb("OsWs Crypt - Result : " . $Out);
        return $Out;
    }

    /**
     *      @abstract    Prepare Data Packets for transmit.
     *      @param       Array   $In         Input Data ArrayObject
     *      @param       bool    $Uncrypted  Force no encrypt on message.	
     *      @return      string  $Out        Output Packet Data ( Encrypted or not )
     */
    public function Pack($In, $Uncrypted = False) {
        //====================================================================//
        // Debug Log
        Splash::Log()->Deb("MsgWsPack");
        
        //====================================================================//
        // Encode Data Buffer
        //====================================================================//
        if (Splash::Configuration()->WsEncode == "XML")    {   
            //====================================================================//
            // Convert Data Buffer To XML
            $Serial = Splash::Xml()->ObjectToXml($In);
        } else {
            //====================================================================//
            // Serialize Data Buffer
            $Serial = serialize($In);
        }
        
        //====================================================================//
        // Encrypt serialized data buffer
        //====================================================================//
        if ( !$Uncrypted ) {
            $Out = $this->Crypt("encrypt", $Serial, $this->key, $this->id);
        }
        //====================================================================//
        // Else, switch to base64
        else {
            $Out = base64_encode($Serial);
        }
        
        //====================================================================//
        //  Debug Informations
        //====================================================================//
        if ((!SPLASH_SERVER_MODE) && (Splash::Configuration()->TraceOut)) {
            Splash::Log()->War("MsgWsFinalPack",print_r($Serial,true));
        }
        return $Out;
    }

    /**
     *      @abstract   Unpack received Data Packets.
     *      @param      string   $In         Input Data
     *      @param      bool     $Uncrypted  Force no encrypt on message.
     *      @return     Array    $Out        Output Packet Data
     */
    public function unPack($In, $Uncrypted = False) {
        //====================================================================//
        // Debug Log
        Splash::Log()->Deb("MsgWsunPack");
        
        //====================================================================//
        // Decrypt response
        //====================================================================//
        if (!empty($In) && !$Uncrypted ) {
            $Decode = $this->Crypt("decrypt", $In, $this->key, $this->id);
        }
        //====================================================================//
        // Else, switch from base64
        else {
            $Decode = base64_decode($In,TRUE);
        }
        
        //====================================================================//
        // Decode Data Response
        //====================================================================//
        // Convert Data Buffer To XML
        if (Splash::Configuration()->WsEncode == "XML")    {
            if (strpos($Decode,'<SPLASH>') !== FALSE)   {
                $Out = Splash::Xml()->XmlToArrayObject($Decode);
            } 
        //====================================================================//
        // Unserialize Data buffer
        } else {
            if (!empty($Decode)) {  $Out = unserialize($Decode);    }        
        }
        
        //====================================================================//
        // Trow Exception if fails
        if (empty($Out)) {            
            Splash::Log()->Err("ErrWsunPack");
        }
        
        //====================================================================//
        //  Messages Debug Informations
        //====================================================================//
        //  Data Decoded (PHP Serialized Objects or XML) 
        if ( (!SPLASH_SERVER_MODE) && (Splash::Configuration()->TraceIn) ) {
            Splash::Log()->War("Splash unPack - Data Decode : " . print_r($Decode,true));
        }
        //  Final Decoded Data (ArrayObject Structure) 
        //  Splash::Log()->Deb("OsWs unPack - Data unSerialized : " . print_r($Out,true) );
  
        //====================================================================//
        // Return Result or False
        return empty($Out)?False:$Out;
    }

    /**
     *      @abstract   Clean Ws Input Buffer before Call Request
     *      @return     int     $result     0 if KO, 1 if OK
     */
    public function CleanIn() {
        //====================================================================//
        //  Free current output buffer
        unset($this->_In);
        //====================================================================//
        //  Initiate a new input buffer
        $this->_In = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        return True;
    }

    /**
     *      @abstract   Clean parameters of Ws Call Request
     *      @return     int     $result     0 if KO, 1 if OK
     */
    public function CleanOut() {
        //====================================================================//
        //  Free current tasks list
        unset($this->tasks);
        //====================================================================//
        //  Initiate a new tasks list
        $this->tasks    =  new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        
        //====================================================================//
        //  Free current output buffer
        unset($this->_Out);
        //====================================================================//
        //  Initiate a new output buffer
        $this->_Out     = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        return True;
    }
    
//====================================================================//
//  CORE WEBSERVICE FUNCTIONS
//====================================================================//

    /**
     *      @abstract   Perform operation with WebService Client
     *      @param      string      $service        server method to use
     *      @param      array       $tasks          list of task to perform inside this request. is NULL, internal task list is used.
     *      @param      bool        $Uncrypted      force message not to be crypted (Used for Ping Only)
     *      @param      bool        $clean          Clean task buffer at the end of this function
     *      @return     int         $result         0 if KO, 1 if OK
     */
    public function Call($service, $tasks = NULL, $Uncrypted = 0, $clean = 1) {
        
        //====================================================================//
        // WebService Call =>> Initialisation 
        if ( !$this->Call_Init($service) ) {
            return False;
        }

        //====================================================================//
        // WebService Call =>> Add Tasks 
        if ( !$this->Call_AddTasks($tasks) ) {
            return False;
        }

        //====================================================================//
        // Prepare Raw Request Data 
        //====================================================================//
        $this->RawOut = array(
            'id' => $this->id , 
            'data' => $this->Pack($this->_Out, $Uncrypted));

        //====================================================================//
        // Prepare NuSOAP Client 
        //====================================================================//
        if ( !$this->Call_BuildClient() ) {
            return False;
        }
        
        //====================================================================//
        // Log NuSOAP Call Informations in debug buffer
        Splash::Log()->Deb("[NuSOAP] Call Url= '" . $this->url . "' Service='" . $service . "'");

        //====================================================================//
        // Execute NuSOAP Call
        //====================================================================//
        // Call Execution
        $this->RawIn = $this->client->call($service, $this->RawOut);
        
        //====================================================================//
        // Analyze & Decode NuSOAP Response
        //====================================================================//
        if ( !$this->Call_DecodeResponse($Uncrypted) ) {
            return False;
        }

        //====================================================================//
        // If required, lean _Out buffer parameters before exit
        if ($clean) {
            $this->CleanOut();
        }
        
        return $this->_In;
    }

    /**
     *      @abstract   Init WebService Call
     * 
     *      @param      string      $service        server method to use
     * 
     *      @return     bool
     */
    public function Call_Init($service) {
        
        //====================================================================//
        // Debug 
        Splash::Log()->Deb("MsgWsCall");
        
        //====================================================================//
        // Safety Check 
        if ( !$this->Verify() ) {
            return Splash::Log()->Err("ErrWsInValid");
        }
        
        //====================================================================//
        // Clean Data Input Buffer 
        $this->CleanIn();
        
        //====================================================================//
        // Prepare Data Output Buffer
        //====================================================================//
        // Fill buffer with Server Core infos
        $this->_Out->server     = $this->getServerInfos();
        // Remote Service to call
        $this->_Out->service    = $service;               
        // Share Debug Flag with Server
        $this->_Out->debug      = (int) SPLASH_DEBUG;   
        
        return True;
    }
    
    
    /**
     *      @abstract   Add Tasks to WebService Request
     * 
     *      @param      array       $tasks          list of task to perform inside this request. is NULL, internal task list is used.
     * 
     *      @return     bool
     */
    public function Call_AddTasks($tasks = NULL) {
        
        //====================================================================//
        // No tasks to Add
        if ( is_null($tasks) && empty($this->tasks) ) {
            return True;
        } 

        //====================================================================//
        // Prepare Tasks To Perform
        //====================================================================//
        
        //====================================================================//
        // Add Internal Tasks to buffer 
        if ( !empty($this->tasks) ) {
            $this->_Out->tasks      = $this->tasks;
            $this->_Out->taskscount = count ($this->_Out->tasks);
            Splash::Log()->Deb("[NuSOAP] Call Loaded " . $this->_Out->tasks->count() . " Internal tasks");
        } else {
            $this->_Out->tasks  =   new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);   
        }
        
        //====================================================================//
        // Add External Tasks to the request 
        if ( !empty($tasks) ) {
            $this->_Out->tasks->append($tasks);
            $this->_Out->taskscount = count ($tasks);
            Splash::Log()->Deb("[NuSOAP] Call Loaded " . count ($tasks) . " External tasks");
        }
        
        return True;
    }   
    
    /**
     *      @abstract   Create & Setup WebService Client
     * 
     *      @return     NuSOAP_Client
     */
    private function Call_BuildClient() {    
        
        //====================================================================//
        // Compute target client url
        if (strpos($this->host, "http://") === FALSE) {
            $this->url = 'http://' . $this->host;
        } else {
            $this->url = $this->host;
        }        
        
        //====================================================================//
        // Initiate new NuSoap Client
        $this->client = new \nusoap_client($this->url);
        
        //====================================================================//
        // Setup NuSOAP Debug Level
        if (SPLASH_DEBUG) {
            $this->client->setDebugLevel(2);
        }
        
        //====================================================================//
        // Setup NuSOAP Curl Option if Possible
        if  (in_array  ('curl', get_loaded_extensions())) {
            $this->client->setUseCURL(true);
        }
        
        //====================================================================//
        // Enable NuSOAP PersistentConnection
        $this->client->useHTTPPersistentConnection();
        
        //====================================================================//
        // Define Timeout for client response
        $this->client->response_timeout = Splash::Configuration()->WsTimout;
        
        return True;
    }
    
    
    /**
     *      @abstract   Create & Setup WebService Client
     * 
     *      @param      bool        $Uncrypted      force message not to be crypted (Used for Ping Only)
     * 
     *      @return     NuSOAP_Client
     */
    private function Call_DecodeResponse($Uncrypted) {
        
        //====================================================================//
        // Analyze NuSOAP Response
        //====================================================================//
                
        //====================================================================//
        // Decode & Store NuSOAP Errors if present
        if ($this->client->fault) {
            
            //====================================================================//
            //  Debug Informations            
//            Splash::Log()->Deb("[NuSOAP] Data='"            . print_r($this->_Out, TRUE) . "'");
//            Splash::Log()->Deb("[NuSOAP] Response='"        . print_r($this->RawIn, TRUE) . "'");
            Splash::Log()->Deb("[NuSOAP] Fault Details='"   . $this->client->faultdetail . "'");
            
            //====================================================================//
            //  Errro Message
            return Splash::Log()->Err("ErrWsNuSOAPFault",$this->client->faultcode ,$this->client->faultstring);
            
        }
        
        //====================================================================//
        //  Debug Informations   
//        if (SPLASH_DEBUG) {            
//            Splash::Log()->Deb("[NuSOAP] Debug => '" . $this->client->getDebugAsXMLComment() . "'");
//        }
        
        //====================================================================//
        // Unpack NuSOAP Answer
        //====================================================================//        
        if (!empty($this->RawIn)) {
            
            //====================================================================//
            // Unpack Data from Raw packet
            $this->_In = $this->unPack($this->RawIn, $Uncrypted);
            
            //====================================================================//
            // Merge Logging Messages from remote with current class messages
            if (isset($this->_In->log)) {
                Splash::Log()->Merge($this->_In->log);
            }
            
        } else {
            
            //====================================================================//
            //  Add Information to Debug Log            
            Splash::Log()->Deb("[NuSOAP] Id='"          . print_r($this->id, TRUE) . "'");
//            Splash::Log()->Deb("[NuSOAP] Data='"        . print_r($this->_Out,TRUE) . "'");
//            Splash::Log()->Deb("[NuSOAP] Raw='"         . print_r($this->RawOut, TRUE) . "'");
//            Splash::Log()->Deb("[NuSOAP] Response='"    . print_r($this->RawIn, TRUE) . "'");
            
            //====================================================================//
            //  Error Message            
            return Splash::Log()->Err("ErrWsNoResponse",$this->_Out->service,$this->url);
        }        
        
        return True;
    }   
    
//====================================================================//
//  TASKS STORAGE MANAGEMENT
//====================================================================//
   
    /**
     *      \brief      Add a new task for NuSOAP Call Request
     *      \param      string      $name       Task Identifier Name (Listed in OsWs.inc.php)
     *      \param      array       $params     Task Parameters
     *      \param      string      $desc    	Task Name/Description
     *      \return     SplashWs
     */
    function AddTask($name,$params,$desc = "No Description")
    {
        //====================================================================//
        // Create a new task
        $task 	= 	new ArrayObject(array(),  ArrayObject::ARRAY_AS_PROPS);
        //====================================================================//
        // Prepare Task Id
        $Id                     =       $this->tasks->count() + 1;
        //====================================================================//
        // Fill task with informations
        $task["id"] 		= 	$Id;
        $task["name"] 		= 	$name;
        $task["desc"] 		= 	$desc;
        $task["params"]         = 	$params;
        //====================================================================//
        // Add Task to Tasks list
        $this->tasks[$Id]       = $task;
        //====================================================================//
        // Debug
        Splash::Log()->Deb("TasksAdd", $task["name"],$task["desc"]); 

        return $this;
    }
    
//====================================================================//
//  INFORMATION RETRIEVAL
//====================================================================//

    /**
     * @abstract     Return Server Informations
     * @return       array   $result
     */
    public function getServerInfos() {
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
        $r->Self            = filter_input(INPUT_SERVER, "PHP_SELF");           // INFO - Current Url 
        $r->ServerAddress   = filter_input(INPUT_SERVER, "SERVER_ADDR");        // INFO - Server IP Address
        // Read System Folder without symlinks
        $r->ServerRoot      = realpath(filter_input(INPUT_SERVER, "DOCUMENT_ROOT") );  
        $r->UserAgent       = filter_input(INPUT_SERVER, "HTTP_USER_AGENT");    // INFO - Browser User Agent 

        //====================================================================//
        // Server Urls
        //====================================================================//
        // CRITICAL - Server Host Name 
        if ( isset(Splash::Configuration()->ServerHost) ) {
            $r->ServerHost      =   Splash::Configuration()->ServerHost;
        } else {
            $r->ServerHost      = filter_input(INPUT_SERVER, "SERVER_NAME");         
        }
        //====================================================================//
        // Server IPv4 Address 
        $r->ServerIP        = filter_input(INPUT_SERVER, "SERVER_ADDR");        
        //====================================================================//
        // Server WebService Path 
        if ( isset(Splash::Configuration()->ServerPath) ) {
            $r->ServerPath      =   Splash::Configuration()->ServerPath;
        } else {
            $FullPath           =   dirname(__DIR__);
            $RelativePath       =   explode($r->ServerRoot,$FullPath);
            $r->ServerPath      =   (isset($RelativePath[1])?$RelativePath[1]:"") . "/soap.php";
        }
        
        $r->setFlags(ArrayObject::STD_PROP_LIST);
        return $r;
    }

    /**
     * @abstract     Return Server Outputs Buffer
     * @return       array   $result
     */
    public function getOutputBuffer() {
        return $this->_Out;
    }
    
}
