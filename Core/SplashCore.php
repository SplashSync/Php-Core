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

namespace   Splash\Core;

use ArrayObject;
use Exception;
use ReflectionClass;
use Splash\Components\CommitsManager;
use Splash\Components\ExtensionsManager;
use Splash\Components\Logger;
use Splash\Components\Webservice;
use Splash\Configurator\JsonConfigurator;
use Splash\Configurator\NullConfigurator;
use Splash\Local\Local;
use Splash\Models\ConfiguratorInterface;
use Splash\Models\Helpers\SplashUrlHelper;
use Splash\Models\LocalClassInterface;

//====================================================================//
//********************************************************************//
//====================================================================//
//  SPLASH REMOTE FRAMEWORK CORE CLASS
//====================================================================//
//********************************************************************//
//====================================================================//

/**
 * Simple & Core Functions for Splash & Slaves Classes
 */
class SplashCore
{
    use ObjectsCoreTrait;
    use WidgetsCoreTrait;
    use ServicesCoreTrait;
    use ToolsCoreTrait;

    /**
     * Static Class Storage
     *
     * @var null|SplashCore
     */
    protected static ?SplashCore $instance;

    /**
     * Module Configuration
     *
     * @var null|ArrayObject
     */
    protected ?ArrayObject $conf;

    /**
     * Splash Local Core Class
     *
     * @var LocalClassInterface
     */
    protected LocalClassInterface $localcore;

    /**
     * Splash Configurator Class Instance
     *
     * @var null|ConfiguratorInterface
     */
    protected ?ConfiguratorInterface $configurator;

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
     * Get Configurator Parser Instance
     *
     * @return ConfiguratorInterface
     */
    public static function configurator(): ConfiguratorInterface
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
        if (!class_exists($className) || !is_subclass_of($className, ConfiguratorInterface::class)) {
            return new NullConfigurator();
        }
        //====================================================================//
        // Initialize Configurator
        self::core()->configurator = new $className();

        return self::core()->configurator;
    }

    //====================================================================//
    //  LOCAL CLASS MANAGEMENT
    //====================================================================//

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

    //====================================================================//
    //  MICRO-FRAMEWORK CONFIGURATION
    //====================================================================//

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
        // Custom Objects Extensions
        $config->ExtensionsPath = null;

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
            $localConf = self::local()->parameters();
        } catch (Exception $e) {
            $localConf = array();
        }
        //====================================================================//
        // Complete Local Configuration with ENV Variables
        SplashUrlHelper::completeParameters($localConf);
        //====================================================================//
        // Validate Local Parameters
        if (self::validate()->isValidLocalParameterArray($localConf)) {
            //====================================================================//
            // Import Local Parameters
            foreach ($localConf as $key => $value) {
                $config->{$key} = is_scalar($value) ? trim((string) $value) : $value;
            }
        }

        //====================================================================//
        // Load Module Local Custom Configuration (from Configurator)
        //====================================================================//
        $customConf = self::configurator()->getParameters();
        //====================================================================//
        // Import Local Parameters
        foreach ($customConf as $key => $value) {
            $config->{$key} = is_scalar($value) ? trim((string) $value) : $value;
        }

        return self::core()->conf;
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

    /**
     * Check if Framework Instanced in Travis CI/CD Mode
     *
     * @return bool
     */
    public static function isTravisMode(): bool
    {
        return !empty(self::input("SPLASH_TRAVIS"));
    }

    /**
     * Check if Framework Instanced in Server Mode
     *
     * @return bool
     */
    public static function isServerMode(): bool
    {
        /** @phpstan-ignore-next-line */
        return (defined('SPLASH_SERVER_MODE') && !empty(SPLASH_SERVER_MODE));
    }

    //====================================================================//
    // WEBSERVICE FUNCTIONS
    //====================================================================//

    /**
     * Ask for Server System Information
     * Information may be overwritten by Local Module Class
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
        // Company Information
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
        // Server Information
        $response->servertype = SPLASH_NAME;
        $response->serverurl = filter_input(INPUT_SERVER, 'SERVER_NAME');

        //====================================================================//
        // Module Information
        $response->moduleauthor = SPLASH_AUTHOR;
        $response->moduleversion = SPLASH_VERSION;

        //====================================================================//
        // Verify Local Module Class Is Valid
        if (!self::validate()->isValidLocalClass()) {
            return $response;
        }

        //====================================================================//
        // Merge Information with Local Module Information
        return self::local()->informations($response);
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
        ExtensionsManager::selfTest();
        //====================================================================//
        //  Commits Manager Self-Tests
        CommitsManager::selfTest();
        //====================================================================//
        //  No HTTP Calls on SERVER MODE, nor in TRAVIS tests
        if (self::isServerMode() || !empty(self::input('SPLASH_TRAVIS'))) {
            return true;
        }
        //====================================================================//
        //  Verify Server Webservice Connection
        return self::ws()->selfTest();
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

    //====================================================================//
    //  VARIOUS TOOLING METHODS
    //====================================================================//

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
}
