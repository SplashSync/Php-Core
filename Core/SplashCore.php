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
 * @abstract    Simple & Core Functions for Splash & Slaves Classes
 * @author      B. Paquier <contact@splashsync.com>
 */

namespace   Splash\Core;

use ArrayObject;

use Splash\Components\FileManager;
use Splash\Components\Translator;
use Splash\Components\Validator;
use Splash\Components\Logger;
use Splash\Components\Webservice;
use Splash\Components\XmlManager;
use Splash\Components\Router;
use Splash\Models\CommunicationInterface;
use Splash\Models\Objects\ObjectInterface;

use Splash\Local\Local;

//====================================================================//
//********************************************************************//
//====================================================================//
//  SPLASH REMOTE FRAMEWORK CORE CLASS
//====================================================================//
//********************************************************************//
//====================================================================//

class SplashCore
{
    /**
     * @var Static Class Storage
     */
    protected static $instance;

    /**
     *      @abstract   Class Constructor
     *
     *      @param      bool    $debug      Force Debug Flag
     *
     *      @return     bool    False if KO, True if OK
     */
    public function __construct($debug = false)
    {
        self::$instance = $this;
 
        //====================================================================//
        // Include Splash Constants Definitions
        require_once(dirname(dirname(__FILE__)) . "/inc/defines.inc.php");
        
        //====================================================================//
        // Include Splash Constants Definitions
        if (!defined('SPL_PROTOCOL')) {
            require_once(dirname(dirname(__FILE__)) . "/inc/Splash.Inc.php");
        }
        
        //====================================================================//
        // Notice internal routines we are in server request mode
        if (!defined('SPLASH_SERVER_MODE')) {
            define("SPLASH_SERVER_MODE", 0);
        }

        //====================================================================//
        // Init Logger with Debug Mode
        if ($debug || SPLASH_DEBUG) {
            //====================================================================//
            // Initialize Log & Debug
            self::$instance->log        = new Logger($debug);
        }
        
        return true;
    }
   
    //====================================================================//
    //  STATIC CLASS ACCESS
    //  Creation & Acces to all subclasses Instances
    //====================================================================//
    
    /**
     *      @abstract   Get a singleton Core Class
     *                  Acces to all most commons Module Functions
     *      @return     Splash
     */
    public static function core()
    {
        if (!isset(self::$instance)) {
            //====================================================================//
            //  Load SplashCore Class
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     *      @abstract   Get Configuration Array
     *      @return     Array
     */
    public static function configuration()
    {
        //====================================================================//
        // Configuration Array Already Exists
        //====================================================================//
        if (isset(self::core()->conf)) {
            return self::core()->conf;
        }

        //====================================================================//
        // Load Module Core Configuration
        //====================================================================//

        //====================================================================//
        // Initialize Empty Configuration Array
        self::core()->conf  =  new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        $Conf = &self::core()->conf;

        //====================================================================//
        // Load Module Core Configuration from Definition File
        //====================================================================//
        // Translations Parameters
        $Conf->DefaultLanguage      =   SPLASH_DF_LANG;
        //====================================================================//
        // WebService Core Parameters
        $Conf->WsMethod             =   SPLASH_WS_METHOD;
        $Conf->WsTimout             =   SPLASH_TIMEOUT;
        $Conf->WsCrypt              =   SPLASH_CRYPT_METHOD;
        $Conf->WsEncode             =   SPLASH_ENCODE;
        $Conf->WsHost               =   "www.splashsync.com/ws/soap";
        //====================================================================//
        // Activity Logging Parameters
        $Conf->Logging              =   SPLASH_LOGGING;
        $Conf->TraceIn              =   SPLASH_TRACE_IN;
        $Conf->TraceOut             =   SPLASH_TRACE_OUT;
        $Conf->TraceTasks           =   SPLASH_TRACE_TASKS;
        
        //====================================================================//
        // Server Requests Configuration
        $Conf->server               =   array();

        //====================================================================//
        // Load Module Local Configuration (In Safe Mode)
        //====================================================================//
        if (is_null(self::local())) {
            return self::core()->conf;
        }
        $LocalConf  =   self::local()->Parameters();
        //====================================================================//
        // Validate Local Parameters
        if (self::validate()->isValidLocalParameterArray($LocalConf)) {
            //====================================================================//
            // Import Local Parameters
            foreach ($LocalConf as $key => $value) {
                $Conf->$key =   trim($value);
            }
        }
        return self::core()->conf;
    }

    /**
     *      @abstract   Get a singleton Log Class
     *                  Acces to Module Logging Functions
     *      @return     \Splash\Components\Logger
     */
    public static function log()
    {
        if (!isset(self::core()->log)) {
            //====================================================================//
            // Initialize Log & Debug
            self::core()->log        = new Logger();

            //====================================================================//
            //  Define Standard Messages Prefix if Not Overiden
            if (isset(self::configuration()->localname)) {
                self::core()->log->setPrefix(self::configuration()->localname);
            }
        }
        return self::core()->log;
    }
    
    /**
     *      @abstract   Get a singleton Communication Class
     *
     *      @return     CommunicationInterface
     */
    public static function com()
    {
        if (isset(self::core()->Com)) {
            return self::core()->Com;
        }
        
        switch (self::configuration()->WsMethod) {
            case "SOAP":
                self::log()->deb("Selected SOAP PHP Protocol for Communication");
                self::core()->Com           = new \Splash\Components\SOAP\SOAPInterface();
                break;

            case "NuSOAP":
            default:
                self::log()->deb("Selected NuSOAP PHP Librarie for Communication");
                self::core()->Com           = new \Splash\Components\NuSOAP\NuSOAPInterface();
                break;
        }

        return self::core()->Com;
    }
    
    /**
     *      @abstract   Get a singleton WebService Class
     *                  Acces to NuSOAP WebService Communication Functions
     *      @return     \Splash\Components\Webservice
     */
    public static function ws()
    {
        if (!isset(self::core()->ws)) {
            //====================================================================//
            // WEBSERVICE INITIALISATION
            //====================================================================//
            // Initialize SOAP WebServices Class
            self::core()->ws           = new Webservice();
            
            //====================================================================//
            // Initialize WebService Configuration Array
            self::core()->ws->setup();
            
            //====================================================================//
            //  Load Translation File
            self::translator()->load("ws");
        }
        return self::core()->ws;
    }
    
    /**
     *      @abstract   Get a singleton Router Class
     *                  Acces to Server Tasking Management Functions
     *      @return     \Splash\Components\Router
     */
    public static function router()
    {
        if (isset(self::core()->router)) {
            return self::core()->router;
        }
        
        //====================================================================//
        // Initialize Tasks List
        self::core()->router        = new Router();
        
        return self::core()->router;
    }
    
    /**
     *      @abstract   Get a singleton File Class
     *                  Acces to File Management Functions
     *      @return     \Splash\Components\FileManager
     */
    public static function file()
    {
        if (!isset(self::core()->file)) {
            //====================================================================//
            // Initialize Tasks List
            self::core()->file        = new FileManager();
            
            //====================================================================//
            //  Load Translation File
            self::translator()->load("file");
        }
        return self::core()->file;
    }
    
    /**
     *      @abstract   Get a singleton Validate Class
     *
     *                  Acces to Module Validation Functions
     *
     *      @return     \Splash\Components\Validator
     */
    public static function validate()
    {
        if (isset(self::core()->valid)) {
            return self::core()->valid;
        }
            
        //====================================================================//
        // Initialize Tasks List
        self::core()->valid        = new Validator();
            
        //====================================================================//
        //  Load Translation File
        self::translator()->load("validate");
            
        return self::core()->valid;
    }
    
    /**
     *      @abstract   Get a singleton Xml Parser Class
     *
     *                  Acces to Module Xml Parser Functions
     *
     *      @return     \Splash\Components\XmlManager
     */
    public static function xml()
    {
        if (isset(self::core()->xml)) {
            return self::core()->xml;
        }
        
        //====================================================================//
        // Initialize Tasks List
        self::core()->xml        = new XmlManager();
            
        return self::core()->xml;
    }

    /**
     *      @abstract   Get a singleton Translator Class
     *                  Acces to Translation Functions
     *      @return     \Splash\Components\Translator
     */
    public static function translator()
    {
        if (!isset(self::core()->translator)) {
            //====================================================================//
            // Initialize Tasks List
            self::core()->translator        = new Translator();
        }
        
        return self::core()->translator;
    }
    
    /**
     *      @abstract   Acces Server Local Class
     *
     *      @return     \Splash\Local
     */
    public static function local()
    {
        //====================================================================//
        // Initialize Local Core Management Class
        if (isset(self::core()->localcore)) {
            return self::core()->localcore;
        }

        //====================================================================//
        // Verify Local Core Class Exist
        if (self::validate()->isValidLocalClass() == true) {
            //====================================================================//
            // Initialize Class
            self::core()->localcore        = new Local();

            //====================================================================//
            //  Load Translation File
            self::translator()->load("local");
            
            //====================================================================//
            // Load Local Includes
            self::core()->localcore->Includes();

            return self::core()->localcore;
        }

        return null;
    }
        
    /**
     *      @abstract   Get Specific Object Class
     *                  This function is a router for all local object classes & functions
     *
     *      @params     $type       Specify Object Class Name
     *
     *      @return     ObjectInterface
     */
    public static function object($ObjectType)
    {
        //====================================================================//
        // First Access to Local Objects
        if (!isset(self::core()->objects)) {
            //====================================================================//
            // Initialize Local Objects Class Array
            self::core()->objects = array();
        }
        
        //====================================================================//
        // Check in Cache
        if (array_key_exists($ObjectType, self::core()->objects)) {
            return self::core()->objects[$ObjectType];
        }

        //====================================================================//
        // Verify if Object Class is Valid
        if (!self::validate()->isValidObject($ObjectType)) {
            return null;
        }
        
        //====================================================================//
        // Check if Object Manager is Overriden
        if (self::validate()->isValidLocalOverride("Object")) {
            //====================================================================//
            // Initialize Local Object Manager
            self::core()->objects[$ObjectType] = self::local()->object($ObjectType);
        } else {
            //====================================================================//
            // Initialize Standard Class
            $ClassName = SPLASH_CLASS_PREFIX . "\Objects\\" . $ObjectType;
            self::core()->objects[$ObjectType]        = new $ClassName();
        }
        
        
        //====================================================================//
        //  Load Translation File
        self::translator()->load("objects");
            
        return self::core()->objects[$ObjectType];
    }
    
    /**
     *      @abstract   Get Specific Widget Class
     *                  This function is a router for all local widgets classes & functions
     *
     *      @params     $WidgetType         Specify Widget Class Name
     *
     *      @return     \Splash\Models\WidgetBase
     */
    public static function widget($WidgetType)
    {
        //====================================================================//
        // First Access to Local Objects
        if (!isset(self::core()->widgets)) {
            //====================================================================//
            // Initialize Local Widget Class Array
            self::core()->widgets = array();
        }
        
        //====================================================================//
        // Check in Cache
        if (array_key_exists($WidgetType, self::core()->widgets)) {
            return self::core()->widgets[$WidgetType];
        }

        //====================================================================//
        // Verify if Widget Class is Valid
        if (!self::validate()->isValidWidget($WidgetType)) {
            return null;
        }
        
        //====================================================================//
        // Check if Widget Manager is Overriden
        if (self::validate()->isValidLocalOverride("Object")) {
            //====================================================================//
            // Initialize Local Widget Manager
            self::core()->widgets[$WidgetType]      = self::local()->widget($WidgetType);
        } else {
            //====================================================================//
            // Initialize Class
            $ClassName = SPLASH_CLASS_PREFIX . "\Widgets\\" . $WidgetType;
            self::core()->widgets[$WidgetType]      = new $ClassName();
        }
        
        
        //====================================================================//
        //  Load Translation File
        self::translator()->load("widgets");
            
        return self::core()->widgets[$WidgetType];
    }
    
    /**
     *      @abstract   Fully Restart Splash Module
     *
     *      @return     void
     */
    public static function reboot()
    {
        //====================================================================//
        // Clear Module Configuration Array
        if (isset(self::core()->conf)) {
            unset(self::core()->conf);
        }
        //====================================================================//
        // Clear Webservice Configuration
        if (isset(self::core()->ws)) {
            unset(self::core()->ws);
        }
        //====================================================================//
        // Clear Module Local Core Class
//        if (isset(self::Core()->localcore)) {
//            unset(self::Core()->localcore);
//        }
        //====================================================================//
        // Clear Module Local Objects Classes
        if (isset(self::core()->objects)) {
            unset(self::core()->objects);
        }
        //====================================================================//
        // Clear Module Log
        self::log()->cleanLog();
        self::log()->deb("Splash Module Rebooted");
    }
    
    //====================================================================//
    //  COMMON CLASS INFORMATIONS
    //====================================================================//
    
    /**
     *      @abstract   Return name of this library
     *      @return  string    Name of logger
     */
    public static function getName()
    {
        return SPLASH_NAME;
    }

    /**
     *      @abstract   Return Description of this library
     *      @return  string    Name of logger
     */
    public static function getDesc()
    {
        return SPLASH_DESC;
    }
    
    /**
     *      @abstract   Version of the module ('x.y.z' or 'dolibarr' or 'experimental' or 'development')
     *      @return string
     */
    public static function getVersion()
    {
        return SPLASH_VERSION;
    }
  
    /**
     *      @abstract   Version of the module ('x.y.z' or 'dolibarr' or 'experimental' or 'development')
     *      @return string
     */
    public static function getLocalPath()
    {
        //====================================================================//
        // Safety Check => Verify Local Class is Valid
        if (self::local() == null) {
            return null;
        }
        
        //====================================================================//
        // Create A Reflection Class of Local Class
        $reflector = new \ReflectionClass(get_class(self::local()));
        
        //====================================================================//
        // Return Class Local Path
        return dirname($reflector->getFileName());
    }

    /**
     * @abstract   Secured reading of Server SuperGlobals
     *
     * @param   string      $Name
     * @param   string      $Type
     *
     * @return string
     */
    public static function input($Name, $Type = INPUT_SERVER)
    {
        //====================================================================//
        // Standard Safe Reading
        $Result =   filter_input($Type, $Name);
        if ($Result !== null) {
            return $Result;
        }
        //====================================================================//
        // Fallback Reading
        if (($Type === INPUT_SERVER) && isset($_SERVER[$Name])) {
            return $_SERVER[$Name];
        }
        if (($Type === INPUT_GET) && isset($_GET[$Name])) {
            return $_GET[$Name];
        }
        return null;
    }
    
    //====================================================================//
    //  TRANSLATIONS MANAGEMENT
    //====================================================================//
   
    /**
     *      @abstract   Return text translated of text received as parameter (and encode it into HTML)
     *
     *      @param  string  $key        Key to translate
     *      @param  string  $param1     chaine de param1
     *      @param  string  $param2     chaine de param2
     *      @param  string  $param3     chaine de param3
     *      @param  string  $param4     chaine de param4
     *      @param  string  $param5     chaine de param5
     *      @param  int     $maxsize    Max length of text
     *      @return string              Translated string (encoded into HTML entities and UTF8)
     */
    public static function trans(
        $key,
        $param1 = '',
        $param2 = '',
        $param3 = '',
        $param4 = '',
        $param5 = '',
        $maxsize = 0
    ) {
        return self::translator()->translate($key, $param1, $param2, $param3, $param4, $param5, $maxsize);
    }

    //--------------------------------------------------------------------//
    //--------------------------------------------------------------------//
    //----  ADMIN WEBSERVICE FUNCTIONS                                ----//
    //--------------------------------------------------------------------//
    //--------------------------------------------------------------------//
    
    /**
     *      @abstract      Ask for Server System Informations
     *                     Informations may be overwritten by Local Module Class
     *
     *      @return     ArrayObject             Array including all server informations
     *
     **********************************************************************************
     *******    General Parameters
     **********************************************************************************
     *
    *                   $r->Name            =   $this->name;
    *                   $r->Id              =   $this->id;
    *
    *******         Server Infos
    *                   $r->php             =   phpversion();
    *                   $r->Self            =   $_SERVER["PHP_SELF"];
    *                   $r->Server          =   $_SERVER["SERVER_NAME"];
    *                   $r->ServerAddress   =   $_SERVER["SERVER_ADDR"];
    *                   $r->Port            =   $_SERVER["SERVER_PORT"];
    *                   $r->UserAgent       =   $_SERVER["HTTP_USER_AGENT"];
    *
    */
    public static function informations()
    {
        //====================================================================//
        // Init Response Object
        $Response = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        
        //====================================================================//
        // Server General Description
        $Response->shortdesc        =   SPLASH_NAME . " " .  SPLASH_VERSION;
        $Response->longdesc         =   SPLASH_DESC;
        
        //====================================================================//
        // Company Informations
        $Response->company          =   null;
        $Response->address          =   null;
        $Response->zip              =   null;
        $Response->town             =   null;
        $Response->country          =   null;
        $Response->www              =   null;
        $Response->email            =   null;
        $Response->phone            =   null;
        
        //====================================================================//
        // Server Logo & Ico
        $Response->icoraw           =   self::file()->readFileContents(
            dirname(dirname(__FILE__))  . "/img/Splash-ico.png"
        );
        $Response->logourl          =   null;
        $Response->logoraw          =   self::file()->readFileContents(
            dirname(dirname(__FILE__)) . "/img/Splash-ico.jpg"
        );
        
        //====================================================================//
        // Server Informations
        $Response->servertype       =   SPLASH_NAME;
        $Response->serverurl        =   filter_input(INPUT_SERVER, "SERVER_NAME");

        //====================================================================//
        // Module Informations
        $Response->moduleauthor     =   SPLASH_AUTHOR;
        $Response->moduleversion    =   SPLASH_VERSION;

        //====================================================================//
        // Verify Local Module Class Is Valid
        if (!self::validate()->isValidLocalClass()) {
            return $Response;
        }
        
        //====================================================================//
        // Merge Informations with Local Module Informations
        $LocalArray = self::local()->informations($Response);
        if (!is_array($LocalArray) && !is_a($LocalArray, "ArrayObject")) {
            $Response   =   $LocalArray;
        }
        
        return $Response;
    }
    
    /**
     *      @abstract   Build list of Available Objects
     *
     *      @return     array       $list           list array including all available Objects Type
     *
     */
    public static function objects()
    {
        //====================================================================//
        // Check if Overriding Functions Exist
        if (self::validate()->isValidLocalOverride("Objects")) {
            return self::local()->objects();
        }
        
        $ObjectsList = array();
        
        //====================================================================//
        // Safety Check => Verify Objects Folder Exists
        $path    =   self::getLocalPath() . "/Objects";
        if (!is_dir($path)) {
            return $ObjectsList;
        }
        
        //====================================================================//
        // Scan Local Objects Folder
        $scan = array_diff(scandir($path, 1), array('..', '.', 'index.php', 'index.html'));
        if ($scan == false) {
            return $ObjectsList;
        }
            
        //====================================================================//
        // Scan Each File in Folder
        foreach ($scan as $filename) {
            //====================================================================//
            // Verify Filename is a File (Not a Directory)
            if (!is_file($path . "/" . $filename)) {
                continue;
            }
            //====================================================================//
            // Extract Class Name
            $ClassName = pathinfo($path . "/" . $filename, PATHINFO_FILENAME);
            //====================================================================//
            // Verify ClassName is a Valid Object File
            if (self::validate()->isValidObject($ClassName) == false) {
                continue;
            }
            $ObjectsList[] = $ClassName;
        }
        return $ObjectsList;
    }
   
    /**
     *      @abstract   Perform Local Module Self Test
     *
     *      @return     bool    Test Result
     */
    public static function selfTest()
    {
        //====================================================================//
        //  Perform Local Core Class Test
        if (!self::validate()->isValidLocalClass()) {
            return false;
        }
        
        //====================================================================//
        //  Read Local Objects List
        $ObjectsList   =   self::objects();
        if (is_array($ObjectsList)) {
            foreach ($ObjectsList as $ObjectType) {
                if (!self::validate()->isValidObject($ObjectType)) {
                    return false;
                }
            }
        }
        
        //====================================================================//
        //  Perform Local SelfTest
        if (!self::local()->selfTest()) {
            return false;
        }
        
        //====================================================================//
        //  Verify Detected Server Informations
        if (!self::validate()->isValidServerInfos()) {
            return false;
        }
        
        //====================================================================//
        //  No HTTP Calls on SERVER MODE, nor in TRAVIS tests
        if (SPLASH_SERVER_MODE || !empty(self::input("SPLASH_TRAVIS"))) {
            return true;
        }

        //====================================================================//
        //  Verify Server Webservice Connection
        return self::ws()->selfTest();
    }

    /**
     *      @abstract   Build list of Available Widgets
     *
     *      @return     array       $list           list array including all available Widgets Type
     *
     */
    public static function widgets()
    {
        //====================================================================//
        // Check if Overriding Functions Exist
        if (self::validate()->isValidLocalOverride("Widgets")) {
            return self::local()->widgets();
        }
        
        $WidgetsList = array();
        
        //====================================================================//
        // Safety Check => Verify Objects Folder Exists
        $path    =   self::getLocalPath() . "/Widgets";
        if (!is_dir($path)) {
            return $WidgetsList;
        }
        
        //====================================================================//
        // Scan Local Objects Folder
        $scan = array_diff(scandir($path, 1), array('..', '.', 'index.php', 'index.html'));
        if ($scan == false) {
            return $WidgetsList;
        }
            
        //====================================================================//
        // Scan Each File in Folder
        foreach ($scan as $filename) {
            $ClassName = pathinfo($path . "/" . $filename, PATHINFO_FILENAME);

            //====================================================================//
            // Verify ClassName is a Valid Object File
            if (self::validate()->isValidWidget($ClassName) == false) {
                continue;
            }
            $WidgetsList[] = $ClassName;
        }
        return $WidgetsList;
    }
}
