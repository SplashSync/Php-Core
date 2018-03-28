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

use Splash\Local\Local;

//====================================================================//
//   INCLUDES
//====================================================================//

//====================================================================//
// Include Splash Constants Definitions
require_once(dirname(dirname(__FILE__)) . "/inc/defines.inc.php");

//====================================================================//
// Include Objects Base Class
//require_once("SplashObject.php");
//====================================================================//
// Include Widgets Base Class
//require_once("SplashWidget.php");

//====================================================================//
//********************************************************************//
//====================================================================//
//  SPLASH BASE CLASS
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
    public function __construct($debug = SPLASH_DEBUG)
    {
        self::$instance = $this;
        
        //====================================================================//
        // Init Logger with Debug Mode
        if ($debug) {
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
    public static function Core()
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
    public static function Configuration()
    {
        //====================================================================//
        // Configuration Array Already Exists
        //====================================================================//
        if (isset(self::Core()->conf)) {
            return self::Core()->conf;
        }

        //====================================================================//
        // Load Module Core Configuration
        //====================================================================//

        //====================================================================//
        // Initialize Empty Configuration Array
        self::Core()->conf  =  new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        $Conf = &self::Core()->conf;

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
        if (is_null(self::Local())) {
            return self::Core()->conf;
        }
        $LocalConf  =   self::Local()->Parameters();
        //====================================================================//
        // Validate Local Parameters
        if (self::Validate()->isValidLocalParameterArray($LocalConf)) {
            
            //====================================================================//
            // Import Local Parameters
            foreach ($LocalConf as $key => $value) {
                $Conf->$key =   trim($value);
            }
        }
        return self::Core()->conf;
    }

    /**
     *      @abstract   Get a singleton Log Class
     *                  Acces to Module Logging Functions
     *      @return     \Splash\Components\Logger
     */
    public static function Log()
    {
        if (!isset(self::Core()->log)) {
            //====================================================================//
            // Initialize Log & Debug
            self::Core()->log        = new Logger();

            //====================================================================//
            //  Define Standard Messages Prefix if Not Overiden
            if (isset(self::Configuration()->localname)) {
                self::Core()->log->SetPrefix(self::Configuration()->localname);
            }
        }
        return self::Core()->log;
    }
    
    /**
     *      @abstract   Get a singleton Communication Class
     *
     *      @return     CommunicationInterface
     */
    public static function Com()
    {
        if (isset(self::Core()->Com)) {
            return self::Core()->Com;
        }
        
        switch (self::Configuration()->WsMethod) {
            case "SOAP":
                self::Log()->Deb("Selected SOAP PHP Protocol for Communication");
                self::Core()->Com           = new \Splash\Components\SOAP\SOAPInterface();
                break;

            case "NuSOAP":
            default:
                self::Log()->Deb("Selected NuSOAP PHP Librarie for Communication");
                self::Core()->Com           = new \Splash\Components\NuSOAP\NuSOAPInterface();
                break;
        }

        return self::Core()->Com;
    }
    
    /**
     *      @abstract   Get a singleton WebService Class
     *                  Acces to NuSOAP WebService Communication Functions
     *      @return     \Splash\Components\Webservice
     */
    public static function Ws()
    {
        if (!isset(self::Core()->ws)) {
            
            //====================================================================//
            // WEBSERVICE INITIALISATION
            //====================================================================//
            // Initialize SOAP WebServices Class
            self::Core()->ws           = new Webservice();
            
            //====================================================================//
            // Initialize WebService Configuration Array
            self::Core()->ws->Setup();
            
            //====================================================================//
            //  Load Translation File
            self::Translator()->Load("ws");
        }
        return self::Core()->ws;
    }
    
    /**
     *      @abstract   Get a singleton Router Class
     *                  Acces to Server Tasking Management Functions
     *      @return     \Splash\Components\Router
     */
    public static function Router()
    {
        if (isset(self::Core()->router)) {
            return self::Core()->router;
        }
        
        //====================================================================//
        // Initialize Tasks List
        self::Core()->router        = new Router();
        
        return self::Core()->router;
    }
    
    /**
     *      @abstract   Get a singleton File Class
     *                  Acces to File Management Functions
     *      @return     \Splash\Components\FileManager
     */
    public static function File()
    {
        if (!isset(self::Core()->file)) {
            
            //====================================================================//
            // Initialize Tasks List
            self::Core()->file        = new FileManager();
            
            //====================================================================//
            //  Load Translation File
            self::Translator()->Load("file");
        }
        return self::Core()->file;
    }
    
    /**
     *      @abstract   Get a singleton Validate Class
     *
     *                  Acces to Module Validation Functions
     *
     *      @return     \Splash\Components\Validator
     */
    public static function Validate()
    {
        if (isset(self::Core()->valid)) {
            return self::Core()->valid;
        }
            
        //====================================================================//
        // Initialize Tasks List
        self::Core()->valid        = new Validator();
            
        //====================================================================//
        //  Load Translation File
        self::Translator()->Load("validate");
            
        return self::Core()->valid;
    }
    
    /**
     *      @abstract   Get a singleton Xml Parser Class
     *
     *                  Acces to Module Xml Parser Functions
     *
     *      @return     \Splash\Components\XmlManager
     */
    public static function Xml()
    {
        if (isset(self::Core()->xml)) {
            return self::Core()->xml;
        }
        
        //====================================================================//
        // Initialize Tasks List
        self::Core()->xml        = new XmlManager();
            
        return self::Core()->xml;
    }

    /**
     *      @abstract   Get a singleton Translator Class
     *                  Acces to Translation Functions
     *      @return     \Splash\Components\Translator
     */
    public static function Translator()
    {
        if (!isset(self::Core()->translator)) {
            
            //====================================================================//
            // Initialize Tasks List
            self::Core()->translator        = new Translator();
        }
        
        return self::Core()->translator;
    }
    
    /**
     *      @abstract   Acces Server Local Class
     *
     *      @return     \Splash\Local
     */
    public static function Local()
    {
        //====================================================================//
        // Initialize Local Core Management Class
        if (isset(self::Core()->localcore)) {
            return self::Core()->localcore;
        }

        //====================================================================//
        // Verify Local Core Class Exist
        if (self::Validate()->isValidLocalClass() == true) {
            
            //====================================================================//
            // Initialize Class
            self::Core()->localcore        = new Local();

            //====================================================================//
            //  Load Translation File
            self::Translator()->Load("local");
            
            //====================================================================//
            // Load Local Includes
            self::Core()->localcore->Includes();

            return self::Core()->localcore;
        }

        return null;
    }
        
    /**
     *      @abstract   Get Specific Object Class
     *                  This function is a router for all local object classes & functions
     *
     *      @params     $type       Specify Object Class Name
     *
     *      @return     \Splash\Models\ObjectBase
     */
    public static function Object($ObjectType)
    {
        //====================================================================//
        // First Access to Local Objects
        if (!isset(self::Core()->objects)) {
            //====================================================================//
            // Initialize Local Objects Class Array
            self::Core()->objects = array();
        }
        
        //====================================================================//
        // Check in Cache
        if (array_key_exists($ObjectType, self::Core()->objects)) {
            return self::Core()->objects[$ObjectType];
        }

        //====================================================================//
        // Verify if Object Class is Valid
        if (!self::Validate()->isValidObject($ObjectType)) {
            return null;
        }
        
        //====================================================================//
        // Check if Object Manager is Overriden
        if (self::Validate()->isValidLocalOverride("Object")) {
            //====================================================================//
            // Initialize Local Object Manager
            self::Core()->objects[$ObjectType] = self::Local()->Object($ObjectType);
        } else {
            //====================================================================//
            // Initialize Standard Class
            $ClassName = SPLASH_CLASS_PREFIX . "\Objects\\" . $ObjectType;
            self::Core()->objects[$ObjectType]        = new $ClassName();
        }
        
        
        //====================================================================//
        //  Load Translation File
        self::Translator()->Load("objects");
            
        return self::Core()->objects[$ObjectType];
    }
    
    /**
     *      @abstract   Get Specific Widget Class
     *                  This function is a router for all local widgets classes & functions
     *
     *      @params     $WidgetType         Specify Widget Class Name
     *
     *      @return     \Splash\Models\WidgetBase
     */
    public static function Widget($WidgetType)
    {
        //====================================================================//
        // First Access to Local Objects
        if (!isset(self::Core()->widgets)) {
            //====================================================================//
            // Initialize Local Widget Class Array
            self::Core()->widgets = array();
        }
        
        //====================================================================//
        // Check in Cache
        if (array_key_exists($WidgetType, self::Core()->widgets)) {
            return self::Core()->widgets[$WidgetType];
        }

        //====================================================================//
        // Verify if Widget Class is Valid
        if (!self::Validate()->isValidWidget($WidgetType)) {
            return null;
        }
        
        //====================================================================//
        // Check if Widget Manager is Overriden
        if (self::Validate()->isValidLocalOverride("Object")) {
            //====================================================================//
            // Initialize Local Widget Manager
            self::Core()->widgets[$WidgetType]      = self::Local()->Widget($WidgetType);
        } else {
            //====================================================================//
            // Initialize Class
            $ClassName = SPLASH_CLASS_PREFIX . "\Widgets\\" . $WidgetType;
            self::Core()->widgets[$WidgetType]      = new $ClassName();
        }
        
        
        //====================================================================//
        //  Load Translation File
        self::Translator()->Load("widgets");
            
        return self::Core()->widgets[$WidgetType];
    }
    
    /**
     *      @abstract   Fully Restart Splash Module
     *
     *      @return     void
     */
    public static function Reboot()
    {
        //====================================================================//
        // Clear Module Configuration Array
        if (isset(self::Core()->conf)) {
            unset(self::Core()->conf);
        }
        //====================================================================//
        // Clear Webservice Configuration
        if (isset(self::Core()->ws)) {
            unset(self::Core()->ws);
        }
        //====================================================================//
        // Clear Module Local Core Class
//        if (isset(self::Core()->localcore)) {
//            unset(self::Core()->localcore);
//        }
        //====================================================================//
        // Clear Module Local Objects Classes
        if (isset(self::Core()->objects)) {
            unset(self::Core()->objects);
        }
        //====================================================================//
        // Clear Module Log
        self::Log()->CleanLog();
        self::Log()->Deb("Splash Module Rebooted");
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
        if (self::Local() == null) {
            return null;
        }
        
        //====================================================================//
        // Create A Reflection Class of Local Class
        $reflector = new \ReflectionClass(get_class(self::Local()));
        
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
    public static function Input($Name, $Type = INPUT_SERVER)
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
     *      @param  string	$key        Key to translate
     *      @param  string	$param1     chaine de param1
     *      @param  string	$param2     chaine de param2
     *      @param  string	$param3     chaine de param3
     *      @param  string	$param4     chaine de param4
     *      @param  string	$param5     chaine de param5
     *      @param  int		$maxsize    Max length of text
     *      @return string      		Translated string (encoded into HTML entities and UTF8)
     */
    public static function Trans($key, $param1='', $param2='', $param3='', $param4='', $param5='', $maxsize=0)
    {
        return self::Translator()->Translate($key, $param1, $param2, $param3, $param4, $param5, $maxsize);
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
     *      @return     ArrayObject         	Array including all server informations
     *
     **********************************************************************************
     *******    General Parameters
     **********************************************************************************
     *
    *					$r->Name            =   $this->name;
    *					$r->Id              =   $this->id;
    *
    *******			Server Infos
    *					$r->php             =   phpversion();
    *					$r->Self            =   $_SERVER["PHP_SELF"];
    *					$r->Server          =   $_SERVER["SERVER_NAME"];
    *					$r->ServerAddress   =   $_SERVER["SERVER_ADDR"];
    *					$r->Port            =   $_SERVER["SERVER_PORT"];
    *					$r->UserAgent       =   $_SERVER["HTTP_USER_AGENT"];
    *
    */
    public static function Informations()
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
        $Response->icoraw           =   self::File()->ReadFileContents(dirname(dirname(__FILE__))  . "/img/Splash-ico.png");
        $Response->logourl          =   null;
        $Response->logoraw          =   self::File()->ReadFileContents(dirname(dirname(__FILE__)) . "/img/Splash-ico.jpg");
        
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
        if (!self::Validate()->isValidLocalClass()) {
            return $Response;
        }
        
        //====================================================================//
        // Merge Informations with Local Module Informations
        $LocalArray = self::Local()->Informations($Response);
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
    public static function Objects()
    {
        //====================================================================//
        // Check if Overriding Functions Exist
        if (self::Validate()->isValidLocalOverride("Objects")) {
            return self::Local()->Objects();
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
            if (self::Validate()->isValidObject($ClassName) == false) {
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
    public static function SelfTest()
    {
        //====================================================================//
        //  Perform Local Core Class Test
        if (!self::Validate()->isValidLocalClass()) {
            return false;
        }
        
        //====================================================================//
        //  Read Local Objects List
        $ObjectsList   =   self::Objects();
        if (is_array($ObjectsList)) {
            foreach ($ObjectsList as $ObjectType) {
                if (!self::Validate()->isValidObject($ObjectType)) {
                    return false;
                }
            }
        }
        
        //====================================================================//
        //  Perform Local SelfTest
        if (!self::Local()->Selftest()) {
            return false;
        }
        
        //====================================================================//
        //  Verify Detected Server Informations
        if (!self::Validate()->isValidServerInfos()) {
            return false;
        }
        
        //====================================================================//
        //  No HTTP Calls on SERVER MODE, nor in TRAVIS tests
        if (SPLASH_SERVER_MODE || !empty(self::Input("SPLASH_TRAVIS"))) {
            return true;
        }

        //====================================================================//
        //  Verify Server Webservice Connection
        return self::Ws()->SelfTest();
    }

    /**
     *      @abstract   Build list of Available Widgets
     *
     *      @return     array       $list           list array including all available Widgets Type
     *
     */
    public static function Widgets()
    {
        //====================================================================//
        // Check if Overriding Functions Exist
        if (self::Validate()->isValidLocalOverride("Widgets")) {
            return self::Local()->Widgets();
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
            if (self::Validate()->isValidWidget($ClassName) == false) {
                continue;
            }
            $WidgetsList[] = $ClassName;
        }
        return $WidgetsList;
    }
}
