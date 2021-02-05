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

namespace   Splash\Core;

use ArrayObject;
use Exception;
use Splash\Components\FileManager;
use Splash\Components\Logger;
use Splash\Components\Router;
use Splash\Components\Translator;
use Splash\Components\Validator;
use Splash\Components\Webservice;
use Splash\Components\XmlManager;
use Splash\Configurator\JsonConfigurator;
use Splash\Configurator\NullConfigurator;
use Splash\Local\Local;
use Splash\Models\CommunicationInterface;
use Splash\Models\ConfiguratorInterface;
use Splash\Models\LocalClassInterface;
use Splash\Models\Objects\ObjectInterface;
use Splash\Models\ObjectsProviderInterface;
use Splash\Models\Widgets\WidgetInterface;
use Splash\Models\WidgetsProviderInterface;

//====================================================================//
//********************************************************************//
//====================================================================//
//  SPLASH REMOTE FRAMEWORK CORE CLASS
//====================================================================//
//********************************************************************//
//====================================================================//

/**
 * Simple & Core Functions for Splash & Slaves Classes
 *
 * @author      B. Paquier <contact@splashsync.com>
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SplashCore
{
    /**
     * Static Class Storage
     *
     * @var SplashCore
     */
    protected static $instance;

    /**
     * Module Configuration
     *
     * @var null|ArrayObject
     */
    protected $conf;

    /**
     * Splash Webservice Componant
     *
     * @var Logger
     */
    protected $log;

    /**
     * Module Communication Componant
     *
     * @var CommunicationInterface
     */
    protected $com;

    /**
     * Module Webservice Componant
     *
     * @var null|Webservice
     */
    protected $soap;

    /**
     * Module Tasks Routing Componant
     *
     * @var Router
     */
    protected $router;

    /**
     * Module Files Manager Componant
     *
     * @var FileManager
     */
    protected $file;

    /**
     * Validation Componant
     *
     * @var Validator
     */
    protected $valid;

    /**
     * Splash Xml Manager Componant
     *
     * @var XmlManager
     */
    protected $xml;

    /**
     * Splash Text Translator Componant
     *
     * @var Translator
     */
    protected $translator;

    /**
     * Splash Local Core Class
     *
     * @var LocalClassInterface
     */
    protected $localcore;

    /**
     * Splash Objects Class Buffer
     *
     * @var null|array
     */
    protected $objects;

    /**
     * Splash Widgets Class Buffer
     *
     * @var null|array
     */
    protected $widgets;

    /**
     * Splash Configurator Class Instance
     *
     * @var null|ConfiguratorInterface
     */
    protected $configurator;

    /**
     * Class Constructor
     *
     * @param bool $verbose Enable Log of Debug Messages
     */
    public function __construct($verbose = false)
    {
        self::$instance = $this;

        //====================================================================//
        // Include Splash Constants Definitions
        require_once dirname(dirname(__FILE__)).'/inc/defines.inc.php';

        //====================================================================//
        // Include Splash Constants Definitions
        if (!defined('SPL_PROTOCOL')) {
            require_once dirname(dirname(__FILE__)).'/inc/Splash.Inc.php';
        }

        //====================================================================//
        // Notice internal routines we are in server request mode
        if (!defined('SPLASH_SERVER_MODE')) {
            define('SPLASH_SERVER_MODE', 0);
        }

        //====================================================================//
        // Initialize Log & Debug
        self::$instance->log = new Logger($verbose);
    }

    //====================================================================//
    //  STATIC CLASS ACCESS
    //  Creation & Acces to all subclasses Instances
    //====================================================================//

    /**
     * Get a singleton Core Class
     * Acces to all most commons Module Functions
     *
     * @return self
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
     * Get Configuration Array
     *
     * @return ArrayObject
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
        // Initialize Empty Configuration
        self::core()->conf = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        $config = &self::core()->conf;

        //====================================================================//
        // Load Module Core Configuration from Definition File
        //====================================================================//
        // Translations Parameters
        $config->DefaultLanguage = SPLASH_DF_LANG;

        //====================================================================//
        // WebService Core Parameters
        $config->WsMethod = SPLASH_WS_METHOD;
        $config->WsTimout = SPLASH_TIMEOUT;
        $config->WsCrypt = SPLASH_CRYPT_METHOD;
        $config->WsEncode = SPLASH_ENCODE;
        $config->WsHost = 'www.splashsync.com/ws/soap';

        //====================================================================//
        // Activity Logging Parameters
        $config->Logging = SPLASH_LOGGING;
        $config->TraceIn = SPLASH_TRACE_IN;
        $config->TraceOut = SPLASH_TRACE_OUT;
        $config->TraceTasks = SPLASH_TRACE_TASKS;
        $config->SmartNotify = SPLASH_SMART_NOTIFY;

        //====================================================================//
        // Custom Parameters Configurator
        $config->Configurator = JsonConfigurator::class;

        //====================================================================//
        // Server Requests Configuration
        $config->server = array();

        //====================================================================//
        // Load Module Local Configuration (In Safe Mode)
        //====================================================================//
        $localConf = self::local()->Parameters();
        //====================================================================//
        // Validate Local Parameters
        if (self::validate()->isValidLocalParameterArray($localConf)) {
            //====================================================================//
            // Import Local Parameters
            foreach ($localConf as $key => $value) {
                $config->{$key} = trim($value);
            }
        }

        //====================================================================//
        // Load Module Local Custom Configuration (from Configurator)
        //====================================================================//
        $customConf = self::configurator()->getParameters();
        //====================================================================//
        // Import Local Parameters
        foreach ($customConf as $key => $value) {
            $config->{$key} = trim($value);
        }

        return self::core()->conf;
    }

    /**
     * Get a singleton Log Class
     * Acces to Module Logging Functions
     *
     * @return Logger
     */
    public static function log()
    {
        if (!isset(self::core()->log)) {
            //====================================================================//
            // Initialize Log & Debug
            self::core()->log = new Logger();

            //====================================================================//
            //  Define Standard Messages Prefix if Not Overiden
            if (isset(self::configuration()->localname)) {
                self::core()->log->setPrefix(self::configuration()->localname);
            }
        }

        return self::core()->log;
    }

    /**
     * Get a singleton Communication Class
     *
     * @return CommunicationInterface
     */
    public static function com()
    {
        if (isset(self::core()->com)) {
            return self::core()->com;
        }

        switch (self::configuration()->WsMethod) {
            case 'SOAP':
                if (!class_exists("SoapClient")) {
                    self::log()->err('Switched NuSOAP PHP Librarie because Php Soap Ext. is Missing.');
                    self::core()->com = new \Splash\Components\NuSOAP\NuSOAPInterface();

                    break;
                }
                self::log()->deb('Selected SOAP PHP Protocol for Communication');
                self::core()->com = new \Splash\Components\SOAP\SOAPInterface();

                break;
            case 'NuSOAP':
            default:
                self::log()->deb('Selected NuSOAP PHP Librarie for Communication');
                self::core()->com = new \Splash\Components\NuSOAP\NuSOAPInterface();

                break;
        }

        return self::core()->com;
    }

    /**
     * Get a singleton WebService Class
     * Acces to NuSOAP WebService Communication Functions
     *
     * @return Webservice
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    public static function ws()
    {
        if (!isset(self::core()->soap)) {
            //====================================================================//
            // WEBSERVICE INITIALISATION
            //====================================================================//
            // Initialize SOAP WebServices Class
            self::core()->soap = new Webservice();

            //====================================================================//
            // Initialize WebService Configuration Array
            self::core()->soap->setup();

            //====================================================================//
            //  Load Translation File
            self::translator()->load('ws');
        }

        return self::core()->soap;
    }

    /**
     * Get a singleton Router Class
     * Acces to Server Tasking Management Functions
     *
     * @return Router
     */
    public static function router()
    {
        if (isset(self::core()->router)) {
            return self::core()->router;
        }

        //====================================================================//
        // Initialize Tasks List
        self::core()->router = new Router();

        return self::core()->router;
    }

    /**
     * Get a singleton File Class
     * Acces to File Management Functions
     *
     * @return FileManager
     */
    public static function file()
    {
        if (!isset(self::core()->file)) {
            //====================================================================//
            // Initialize Tasks List
            self::core()->file = new FileManager();

            //====================================================================//
            //  Load Translation File
            self::translator()->load('file');
        }

        return self::core()->file;
    }

    /**
     * Get a singleton Validate Class
     * Acces to Module Validation Functions
     *
     * @return Validator
     */
    public static function validate()
    {
        if (isset(self::core()->valid)) {
            return self::core()->valid;
        }

        //====================================================================//
        // Initialize Tasks List
        self::core()->valid = new Validator();

        //====================================================================//
        //  Load Translation File
        self::translator()->load('ws');
        self::translator()->load('validate');

        return self::core()->valid;
    }

    /**
     * Get a singleton Xml Parser Class
     * Acces to Module Xml Parser Functions
     *
     * @return XmlManager
     */
    public static function xml()
    {
        if (isset(self::core()->xml)) {
            return self::core()->xml;
        }

        //====================================================================//
        // Initialize Tasks List
        self::core()->xml = new XmlManager();

        return self::core()->xml;
    }

    /**
     * Get a singleton Translator Class
     * Acces to Translation Functions
     *
     * @return Translator
     */
    public static function translator()
    {
        if (!isset(self::core()->translator)) {
            //====================================================================//
            // Initialize Tasks List
            self::core()->translator = new Translator();
        }

        return self::core()->translator;
    }

    /**
     * Acces Server Local Class
     *
     * @return LocalClassInterface
     */
    public static function local()
    {
        //====================================================================//
        // Initialize Local Core Management Class
        if (isset(self::core()->localcore)) {
            return self::core()->localcore;
        }
        //====================================================================//
        // Verify Local Core Class Exist & is Valid
        if (!self::validate()->isValidLocalClass()) {
            throw new Exception('You requested access to Local Class, but it is Invalid...');
        }
        //====================================================================//
        // Initialize Class
        self::core()->localcore = new Local();
        //====================================================================//
        //  Load Translation File
        self::translator()->load('local');
        //====================================================================//
        // Load Local Includes
        self::core()->localcore->Includes();
        //====================================================================//
        // Return Local Class
        return self::core()->localcore;
    }

    /**
     * Force Server Local Class
     *
     * @param LocalClassInterface $localClass Name of New Local Class to Use
     *
     * @return void
     */
    public static function setLocalClass(LocalClassInterface $localClass)
    {
        //====================================================================//
        // Force Local Core Management Class
        self::core()->localcore = $localClass;
    }

    /**
     * Get Specific Object Class
     * This function is a router for all local object classes & functions
     *
     * @param string $objectType Local Object Class Name
     *
     * @throws Exception
     *
     * @return ObjectInterface
     */
    public static function object($objectType)
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
        if (array_key_exists($objectType, self::core()->objects)) {
            return self::core()->objects[$objectType];
        }

        //====================================================================//
        // Verify if Object Class is Valid
        if (!self::validate()->isValidObject($objectType)) {
            throw new Exception('You requested access to an Invalid Object Type : '.$objectType);
        }

        //====================================================================//
        // Check if Object Manager is Overriden
        if (self::local() instanceof ObjectsProviderInterface) {
            //====================================================================//
            // Initialize Local Object Manager
            self::core()->objects[$objectType] = self::local()->object($objectType);
        } else {
            //====================================================================//
            // Initialize Standard Class
            $className = SPLASH_CLASS_PREFIX.'\\Objects\\'.$objectType;
            self::core()->objects[$objectType] = new $className();
        }

        //====================================================================//
        //  Load Translation File
        self::translator()->load('objects');

        return self::core()->objects[$objectType];
    }

    /**
     * Get Specific Widget Class
     * This function is a router for all local widgets classes & functions
     *
     * @param string $widgetType Local Widget Class Name
     *
     * @throws Exception
     *
     * @return WidgetInterface
     */
    public static function widget($widgetType)
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
        if (array_key_exists($widgetType, self::core()->widgets)) {
            return self::core()->widgets[$widgetType];
        }

        //====================================================================//
        // Verify if Widget Class is Valid
        if (!self::validate()->isValidWidget($widgetType)) {
            throw new Exception('You requested access to an Invalid Widget Type : '.$widgetType);
        }

        //====================================================================//
        // Check if Widget Manager is Overriden
        if (self::local() instanceof WidgetsProviderInterface) {
            //====================================================================//
            // Initialize Local Widget Manager
            self::core()->widgets[$widgetType] = self::local()->widget($widgetType);
        } else {
            //====================================================================//
            // Initialize Class
            $className = SPLASH_CLASS_PREFIX.'\\Widgets\\'.$widgetType;
            self::core()->widgets[$widgetType] = new $className();
        }

        //====================================================================//
        //  Load Translation File
        self::translator()->load('widgets');

        return self::core()->widgets[$widgetType];
    }

    /**
     * Get Configurator Parser Instance
     *
     * @return ConfiguratorInterface
     */
    public static function configurator()
    {
        //====================================================================//
        // Configuration Array Already Exists
        //====================================================================//
        if (isset(self::core()->configurator)) {
            return self::core()->configurator;
        }

        //====================================================================//
        // Load Configurator Class Name from Configuration
        $className = self::configuration()->Configurator;
        //====================================================================//
        // No Configurator Defined
        if (!is_string($className) || empty($className)) {
            return new NullConfigurator();
        }
        //====================================================================//
        // Validate Configurator Class Name
        if (false == self::validate()->isValidConfigurator($className)) {
            return new NullConfigurator();
        }

        //====================================================================//
        // Initialize Configurator
        self::core()->configurator = new $className();

        return self::core()->configurator;
    }

    /**
     * Fully Restart Splash Module
     *
     * @return void
     */
    public static function reboot()
    {
        //====================================================================//
        // Clear Module Configuration Array
        if (isset(self::core()->conf)) {
            self::core()->conf = null;
        }
        //====================================================================//
        // Clear Webservice Configuration
        if (isset(self::core()->soap)) {
            self::core()->soap = null;
        }
        //====================================================================//
        // Clear Module Local Objects Classes
        if (isset(self::core()->objects)) {
            self::core()->objects = null;
        }
        //====================================================================//
        // Clear Module Log
        self::log()->cleanLog();
        self::log()->deb('Splash Module Rebooted');
    }

    //====================================================================//
    //  COMMON CLASS INFORMATIONS
    //====================================================================//

    /**
     * Return Name of this library
     *
     * @return string
     */
    public static function getName()
    {
        return SPLASH_NAME;
    }

    /**
     * Return Description of this library
     *
     * @return string
     */
    public static function getDesc()
    {
        return SPLASH_DESC;
    }

    /**
     * Version of the module ('x.y.z')
     *
     * @return string
     */
    public static function getVersion()
    {
        return SPLASH_VERSION;
    }

    /**
     * Detect Real Path of Current Module Local Class
     *
     * @return null|string
     */
    public static function getLocalPath()
    {
        //====================================================================//
        // Safety Check => Verify Local Class is Valid
        if (null == self::local()) {
            return null;
        }
        //====================================================================//
        // Create A Reflection Class of Local Class
        $reflector = new \ReflectionClass(get_class(self::local()));
        //====================================================================//
        // Return Class Local Path
        return dirname((string) $reflector->getFileName());
    }

    /**
     * Secured reading of Server SuperGlobals
     *
     * @param string $name
     * @param int    $type
     *
     * @return null|string
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function input($name, $type = INPUT_SERVER)
    {
        //====================================================================//
        // Standard Safe Reading
        $result = filter_input($type, $name);
        if (null !== $result) {
            return $result;
        }
        //====================================================================//
        // Fallback Reading
        if ((INPUT_SERVER === $type) && isset($_SERVER[$name])) {
            return $_SERVER[$name];
        }
        if ((INPUT_GET === $type) && isset($_GET[$name])) {
            return $_GET[$name];
        }

        return null;
    }

    /**
     * Secured counting of Mixed Values
     *
     * @param mixed $value
     *
     * @return int
     */
    public static function count($value)
    {
        if (is_null($value)) {
            return 0;
        }
        if (is_scalar($value)) {
            return 1;
        }

        return count($value);
    }

    /**
     * Check if Framework Instanciated in Debug Mode
     * Used for PhpUnit Tests
     *
     * @return bool
     */
    public static function isDebugMode()
    {
        return (defined('SPLASH_DEBUG') && !empty(SPLASH_DEBUG));
    }

    //====================================================================//
    //  TRANSLATIONS MANAGEMENT
    //====================================================================//

    /**
     * Return text translated of text received as parameter (and encode it into HTML)
     *
     * @param string $key     Key to translate
     * @param string $param1  chaine de param1
     * @param string $param2  chaine de param2
     * @param string $param3  chaine de param3
     * @param string $param4  chaine de param4
     * @param int    $maxsize Max length of text
     *
     * @return string Translated string (encoded into HTML entities and UTF8)
     */
    public static function trans(
        $key,
        $param1 = '',
        $param2 = '',
        $param3 = '',
        $param4 = '',
        $maxsize = 0
    ) {
        return self::translator()->translate($key, $param1, $param2, $param3, $param4, $maxsize);
    }

    //--------------------------------------------------------------------//
    //--------------------------------------------------------------------//
    //----  ADMIN WEBSERVICE FUNCTIONS                                ----//
    //--------------------------------------------------------------------//
    //--------------------------------------------------------------------//

    /**
     * Ask for Server System Informations
     * Informations may be overwritten by Local Module Class
     *
     * @return ArrayObject Array including all server informations
     *
     * General Parameters
     *
     * $r->Name            =   $this->name;
     * $r->Id              =   $this->id;
     *
     * Server Infos
     *
     * $r->php             =   phpversion();
     * $r->Self            =   $_SERVER["PHP_SELF"];
     * $r->Server          =   $_SERVER["SERVER_NAME"];
     * $r->ServerAddress   =   $_SERVER["SERVER_ADDR"];
     * $r->Port            =   $_SERVER["SERVER_PORT"];
     * $r->UserAgent       =   $_SERVER["HTTP_USER_AGENT"];
     */
    public static function informations()
    {
        //====================================================================//
        // Init Response Object
        $response = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);

        //====================================================================//
        // Server General Description
        $response->shortdesc = SPLASH_NAME.' '.SPLASH_VERSION;
        $response->longdesc = SPLASH_DESC;

        //====================================================================//
        // Company Informations
        $response->company = null;
        $response->address = null;
        $response->zip = null;
        $response->town = null;
        $response->country = null;
        $response->www = null;
        $response->email = null;
        $response->phone = null;

        //====================================================================//
        // Server Logo & Ico
        $response->icoraw = self::file()->readFileContents(
            dirname(dirname(__FILE__)).'/img/Splash-ico.png'
        );
        $response->logourl = null;
        $response->logoraw = self::file()->readFileContents(
            dirname(dirname(__FILE__)).'/img/Splash-ico.jpg'
        );

        //====================================================================//
        // Server Informations
        $response->servertype = SPLASH_NAME;
        $response->serverurl = filter_input(INPUT_SERVER, 'SERVER_NAME');

        //====================================================================//
        // Module Informations
        $response->moduleauthor = SPLASH_AUTHOR;
        $response->moduleversion = SPLASH_VERSION;

        //====================================================================//
        // Verify Local Module Class Is Valid
        if (!self::validate()->isValidLocalClass()) {
            return $response;
        }

        //====================================================================//
        // Merge Informations with Local Module Informations
        $localArray = self::local()->informations($response);
        if (!($localArray instanceof ArrayObject)) {
            $response = $localArray;
        }

        return $response;
    }

    /**
     * Build list of Available Objects
     *
     * @return array
     */
    public static function objects()
    {
        //====================================================================//
        // Check if Object Manager is Overriden
        if (self::local() instanceof ObjectsProviderInterface) {
            return self::local()->objects();
        }
        $objectsList = array();
        //====================================================================//
        // Safety Check => Verify Objects Folder Exists
        $path = self::getLocalPath().'/Objects';
        if (!is_dir($path)) {
            return $objectsList;
        }
        //====================================================================//
        // Scan Local Objects Folder
        $scan = scandir($path, 1);
        if (false == $scan) {
            return $objectsList;
        }
        //====================================================================//
        // Scan Each File in Folder
        $files = array_diff($scan, array('..', '.', 'index.php', 'index.html'));
        foreach ($files as $filename) {
            //====================================================================//
            // Verify Filename is a File (Not a Directory)
            if (!is_file($path.'/'.$filename)) {
                continue;
            }
            //====================================================================//
            // Extract Class Name
            $className = pathinfo($path.'/'.$filename, PATHINFO_FILENAME);
            //====================================================================//
            // Verify ClassName is a Valid Object File
            if (false == self::validate()->isValidObject($className)) {
                continue;
            }
            $objectsList[] = $className;
        }

        return $objectsList;
    }

    /**
     * Perform Local Module Self Test
     *
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
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
        $objectTypes = self::objects();
        if (is_array($objectTypes)) {
            foreach ($objectTypes as $objectType) {
                if (!self::validate()->isValidObject($objectType)) {
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
        //  Check If a Custom Configuration is Active
        if (!empty(self::configurator()->getConfiguration())) {
            self::log()->msg("HasCustomCfg");
        }

        //====================================================================//
        //  No HTTP Calls on SERVER MODE, nor in TRAVIS tests
        if (!empty(SPLASH_SERVER_MODE) || !empty(self::input('SPLASH_TRAVIS'))) {
            return true;
        }
        //====================================================================//
        //  Verify Server Webservice Connection
        return self::ws()->selfTest();
    }

    /**
     * Build list of Available Widgets
     *
     * @return array
     */
    public static function widgets()
    {
        //====================================================================//
        // Check if Widget Manager is Overriden
        if (self::local() instanceof WidgetsProviderInterface) {
            return self::local()->widgets();
        }
        $widgetTypes = array();
        //====================================================================//
        // Safety Check => Verify Objects Folder Exists
        $path = self::getLocalPath().'/Widgets';
        if (!is_dir($path)) {
            return $widgetTypes;
        }
        //====================================================================//
        // Scan Local Objects Folder
        $scan = scandir($path, 1);
        if (false == $scan) {
            return $widgetTypes;
        }
        //====================================================================//
        // Scan Each File in Folder
        $files = array_diff($scan, array('..', '.', 'index.php', 'index.html'));
        foreach ($files as $filename) {
            $className = pathinfo($path.'/'.$filename, PATHINFO_FILENAME);
            //====================================================================//
            // Verify ClassName is a Valid Object File
            if (false == self::validate()->isValidWidget($className)) {
                continue;
            }
            $widgetTypes[] = $className;
        }

        return $widgetTypes;
    }
}
