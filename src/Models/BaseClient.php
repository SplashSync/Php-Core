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

namespace Splash\Core\Models;

use ArrayObject;
use Exception;
use Splash\Core\Components\CommitsManager;
use Splash\Core\Components\ExtensionsManager;
use Splash\Core\Components\Logger;
use Splash\Core\Configurator\JsonConfigurator;
use Splash\Core\Configurator\NullConfigurator;
use Splash\Core\Dictionary\SplDefinition;
use Splash\Core\Helpers\System\ConfigFromEnv;
use Splash\Core\Interfaces\ConfiguratorInterface;

/**
 * Foundation Class for Splash Client & Server
 */
class BaseClient
{
    use Client\LocalClassTrait;
    use Client\ObjectsAccessTrait;
    use Client\WidgetsAccessTrait;
    use Client\ScopesTrait;
    use Client\ServicesTrait;
    use Client\SystemTrait;
    use Client\ToolsTrait;

    /**
     * Static Class Storage
     *
     * @var null|BaseClient
     */
    protected static ?BaseClient $instance;

    /**
     * Module Configuration
     *
     * @var null|ArrayObject
     */
    protected ?ArrayObject $conf;

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
        $config->DefaultLanguage = SplDefinition::DF_LANG;

        //====================================================================//
        // WebService Core Parameters
        $config->WsMethod = SplDefinition::WS_METHOD;
        $config->WsTimout = SplDefinition::TIMEOUT;
        $config->WsCrypt = SplDefinition::CRYPT_METHOD;
        $config->WsEncode = SplDefinition::ENCODE;
        $config->WsHost = SplDefinition::HOST;
        $config->WsPostCommit = true;

        //====================================================================//
        // Activity Logging Parameters
        $config->Logging = SplDefinition::LOGGING;
        $config->TraceIn = SplDefinition::TRACE_IN;
        $config->TraceOut = SplDefinition::TRACE_OUT;
        $config->TraceTasks = SplDefinition::TRACE_TASKS;
        $config->SmartNotify = SplDefinition::SMART_NOTIFY;

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
            self::log()->err($e->getMessage());
        }
        //====================================================================//
        // Complete Local Configuration with ENV Variables
        ConfigFromEnv::complete($localConf);
        //====================================================================//
        // Validate Local Parameters
        if (self::validate()->isValidParameterArray($localConf)) {
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

    //====================================================================//
    // WEBSERVICE FUNCTIONS
    //====================================================================//

    /**
     * Ask for Server System Information
     * May be overwritten by Local Module Class
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
        $response->shortdesc = SplDefinition::NAME.' '.SplDefinition::VERSION;
        $response->longdesc = SplDefinition::DESC;

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
        // Available Scopes
        $response->scopes = array();

        //====================================================================//
        // Server Information
        $response->servertype = SplDefinition::NAME;
        $response->serverurl = filter_input(INPUT_SERVER, 'SERVER_NAME');

        //====================================================================//
        // Module Information
        $response->moduleauthor = SplDefinition::AUTHOR;
        $response->moduleversion = SplDefinition::VERSION;

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
    //  COMMON CLASS INFORMATION
    //====================================================================//

    /**
     * Return Name of this library
     *
     * @return string
     */
    public static function getName(): string
    {
        return SplDefinition::NAME;
    }

    /**
     * Return Description of this library
     *
     * @return string
     */
    public static function getDesc(): string
    {
        return SplDefinition::DESC;
    }

    /**
     * Version of the module ('x.y.z')
     *
     * @return string
     */
    public static function getVersion(): string
    {
        return SplDefinition::VERSION;
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
