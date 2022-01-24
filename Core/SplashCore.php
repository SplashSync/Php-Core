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
use ReflectionClass;
use Splash\Components\CommitsManager;
use Splash\Components\FileManager;
use Splash\Components\Logger;
use Splash\Components\NuSOAP\NuSOAPInterface;
use Splash\Components\Router;
use Splash\Components\SOAP\SOAPInterface;
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
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SplashCore
{
    /**
     * Static Class Storage
     *
     * @var null|SplashCore
     */
    protected static $instance;

    /**
     * Module Configuration
     *
     * @var null|ArrayObject
     */
    protected $conf;

    /**
     * Splash Webservice Component
     *
     * @var Logger
     */
    protected $log;

    /**
     * Module Communication Component
     *
     * @var CommunicationInterface
     */
    protected $com;

    /**
     * Module Webservice Component
     *
     * @var null|Webservice
     */
    protected $soap;

    /**
     * Module Tasks Routing Component
     *
     * @var Router
     */
    protected $router;

    /**
     * Module Files Manager Component
     *
     * @var FileManager
     */
    protected $file;

    /**
     * Validation Component
     *
     * @var Validator
     */
    protected $valid;

    /**
     * Splash Xml Manager Component
     *
     * @var XmlManager
     */
    protected $xml;

    /**
     * Splash Text Translator Component
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
     * @var array<string, ObjectInterface>
     */
    protected $objects = array();

    /**
     * Splash Widgets Class Buffer
     *
     * @var array<string, WidgetInterface>
     */
    protected $widgets = array();

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
    public function __construct(bool $verbose = false)
    {
        self::$instance = $this;

        //====================================================================//
        // Include Splash Constants Definitions
        require_once dirname(__FILE__, 2).'/inc/defines.inc.php';

        //====================================================================//
        // Include Splash Constants Definitions
        if (!defined('SPL_PROTOCOL')) {
            require_once dirname(__FILE__, 2).'/inc/Splash.Inc.php';
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
    //  Creation & Access to all subclasses Instances
    //====================================================================//

    /**
     * Get a singleton Core Class
     *
     * Access to all most commons Module Functions
     *
     * @return self
     */
    public static function core(): self
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
    public static function configuration(): ArrayObject
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
        $config->WsPostCommit = true;

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
        try {
            $localConf = self::local()->Parameters();
        } catch (Exception $e) {
            $localConf = array();
        }
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
     *
     * Access to Module Logging Functions
     *
     * @return Logger
     */
    public static function log(): Logger
    {
        if (!isset(self::core()->log)) {
            //====================================================================//
            // Initialize Log & Debug
            self::core()->log = new Logger();

            //====================================================================//
            //  Define Standard Messages Prefix if No Override
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
    public static function com(): CommunicationInterface
    {
        if (isset(self::core()->com)) {
            return self::core()->com;
        }

        switch (self::configuration()->WsMethod) {
            case 'SOAP':
                if (!class_exists("SoapClient")) {
                    self::log()->err('Switched NuSOAP PHP Library because Php Soap Ext. is Missing.');
                    self::core()->com = new NuSOAPInterface();

                    break;
                }
                self::log()->deb('Selected SOAP PHP Protocol for Communication');
                self::core()->com = new SOAPInterface();

                break;
            case 'NuSOAP':
            default:
                self::log()->deb('Selected NuSOAP PHP Library for Communication');
                self::core()->com = new NuSOAPInterface();

                break;
        }

        return self::core()->com;
    }

    /**
     * Get a singleton WebService Class
     *
     * Access to NuSOAP WebService Communication Functions
     *
     * @return Webservice
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    public static function ws(): Webservice
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
     *
     * Access to Server Tasking Management Functions
     *
     * @return Router
     */
    public static function router(): Router
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
     *
     * Access to File Management Functions
     *
     * @return FileManager
     */
    public static function file(): FileManager
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
     *
     * Access to Module Validation Functions
     *
     * @return Validator
     */
    public static function validate(): Validator
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
     *
     * Access to Module Xml Parser Functions
     *
     * @return XmlManager
     */
    public static function xml(): XmlManager
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
     *
     * Access to Translation Functions
     *
     * @return Translator
     */
    public static function translator(): Translator
    {
        if (!isset(self::core()->translator)) {
            //====================================================================//
            // Initialize Tasks List
            self::core()->translator = new Translator();
        }

        return self::core()->translator;
    }

    /**
     * Access Server Local Class
     *
     * @throws Exception
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
    public static function object(string $objectType): ObjectInterface
    {
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
        // Check if Object Manager has Override
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
    public static function widget(string $widgetType): WidgetInterface
    {
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
        // Check if Widget Manager is Override
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
    public static function reboot(): void
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
            self::core()->objects = array();
        }
        //====================================================================//
        // Reset Commits Manager
        CommitsManager::reset();
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
    public static function getName(): string
    {
        return SPLASH_NAME;
    }

    /**
     * Return Description of this library
     *
     * @return string
     */
    public static function getDesc(): string
    {
        return SPLASH_DESC;
    }

    /**
     * Version of the module ('x.y.z')
     *
     * @return string
     */
    public static function getVersion(): string
    {
        return SPLASH_VERSION;
    }

    /**
     * Detect Real Path of Current Module Local Class
     *
     * @throws Exception
     *
     * @return null|string
     */
    public static function getLocalPath(): ?string
    {
        //====================================================================//
        // Safety Check => Verify Local Class is Valid
        if (null == self::local()) {
            return null;
        }
        //====================================================================//
        // Create A Reflection Class of Local Class
        $reflector = new ReflectionClass(get_class(self::local()));
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
    public static function input(string $name, int $type = INPUT_SERVER): ?string
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
    public static function count($value): int
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
     * Check if Framework Instanced in Debug Mode
     * Used for PhpUnit Tests
     *
     * @return bool
     */
    public static function isDebugMode(): bool
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
        string $key,
        string $param1 = '',
        string $param2 = '',
        string $param3 = '',
        string $param4 = '',
        int $maxsize = 0
    ): string {
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
     * @throws Exception
     *
     * @return ArrayObject Array including all server information
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
    public static function informations(): ArrayObject
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
            dirname(__FILE__, 2).'/img/Splash-ico.png'
        );
        $response->logourl = null;
        $response->logoraw = self::file()->readFileContents(
            dirname(__FILE__, 2).'/img/Splash-ico.jpg'
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
     * @throws Exception
     *
     * @return string[]
     */
    public static function objects(): array
    {
        //====================================================================//
        // Check if Object Manager has Overrides
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
     * @throws Exception
     *
     * @return bool
     */
    public static function selfTest(): bool
    {
        //====================================================================//
        //  Perform Local Core Class Test
        if (!self::validate()->isValidLocalClass()) {
            return false;
        }
        //====================================================================//
        //  Read Local Objects List
        foreach (self::objects() as $objectType) {
            if (!self::validate()->isValidObject($objectType)) {
                return false;
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
        //  Commits Manager Self-Tests
        CommitsManager::selfTest();
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
     * @throws Exception
     *
     * @return string[]
     */
    public static function widgets(): array
    {
        //====================================================================//
        // Check if Widget Manager has Overrides
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
