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

use Splash\Components\FileManager;
use Splash\Components\Logger;
use Splash\Components\NuSOAP\NuSOAPInterface;
use Splash\Components\Router;
use Splash\Components\SOAP\SOAPInterface;
use Splash\Components\Translator;
use Splash\Components\Validator;
use Splash\Components\Webservice;
use Splash\Components\XmlManager;
use Splash\Models\CommunicationInterface;

//====================================================================//
//********************************************************************//
//====================================================================//
//  SPLASH REMOTE FRAMEWORK CORE CLASS
//====================================================================//
//********************************************************************//
//====================================================================//

/**
 * Simple & Core Functions for Services Classes
 */
trait ServicesCoreTrait
{
    /**
     * Splash Webservice Component
     *
     * @var Logger
     */
    protected Logger $log;

    /**
     * Module Communication Component
     *
     * @var CommunicationInterface
     */
    protected CommunicationInterface $com;

    /**
     * Module Webservice Component
     *
     * @var null|Webservice
     */
    protected ?Webservice $soap;

    /**
     * Module Tasks Routing Component
     *
     * @var Router
     */
    protected Router $router;

    /**
     * Module Files Manager Component
     *
     * @var FileManager
     */
    protected FileManager $file;

    /**
     * Validation Component
     *
     * @var Validator
     */
    protected Validator $valid;

    /**
     * Splash Xml Manager Component
     *
     * @var XmlManager
     */
    protected XmlManager $xml;

    /**
     * Splash Text Translator Component
     *
     * @var Translator
     */
    protected Translator $translator;

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
     *
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
}
