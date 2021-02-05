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

namespace Splash\Templates\Local;

use ArrayObject;
use Splash\Core\SplashCore      as Splash;
use Splash\Models\LocalClassInterface;

/**
 * Local System Core Management Class
 */
class Local implements LocalClassInterface
{
    //====================================================================//
    // General Class Variables
    // Place Here Any SPECIFIC Variable for your Core Module Class
    //====================================================================//

    //====================================================================//
    // Class Constructor
    //====================================================================//

    /**
     * Class Constructor (Used only if localy necessary)
     *
     * @return int 0 if KO, >0 if OK
     */
    public function __construct()
    {
        //====================================================================//
        // Place Here Any SPECIFIC Initialisation Code
        //====================================================================//
    }

    //====================================================================//
    // *******************************************************************//
    //  MANDATORY CORE MODULE LOCAL FUNCTIONS
    // *******************************************************************//
    //====================================================================//

    /**
     * Return Local Server Parameters as Array
     *
     * THIS FUNCTION IS MANDATORY
     *
     * This function called on each initialization of the module
     *
     *      Result must be an array including mandatory parameters as strings
     *         ["WsIdentifier"]         =>>  Name of Module Default Language
     *         ["WsEncryptionKey"]      =>>  Name of Module Default Language
     *         ["DefaultLanguage"]      =>>  Name of Module Default Language
     *
     * @return array $parameters
     */
    public function parameters()
    {
        return array();
    }

    /**
     * Include Local Includes Files
     *
     * Include here any local files required by local functions.
     * This Function is called each time the module is loaded
     *
     * There may be differents scenarios depending if module is
     * loaded as a library or as a NuSOAP Server.
     *
     * This is triggered by global constant SPLASH_SERVER_MODE.
     *
     * @return bool
     */
    public function includes()
    {
        //====================================================================//
        // When Library is called in server mode ONLY
        //====================================================================//
        if (defined('SPLASH_SERVER_MODE') && !empty(SPLASH_SERVER_MODE)) {
            // NOTHING TO DO
        //====================================================================//
        // When Library is called in client mode ONLY
        //====================================================================//
        }
        // NOTHING TO DO

        //====================================================================//
        // When Library is called in both client & server mode
        //====================================================================//

        return true;
    }

    /**
     * Return Local Server Self Test Result
     *
     * THIS FUNCTION IS MANDATORY
     *
     * This function called during Server Validation Process
     *
     * We recommand using this function to validate all functions or parameters
     * that may be required by Objects, Widgets or any other module specific action.
     *
     * Use Module Logging system & translation tools to return test results Logs
     *
     * @return bool global test result
     */
    public function selfTest()
    {
        return true;
    }

    /**
     * Update Server Informations with local Data
     *
     * THIS FUNCTION IS MANDATORY
     *
     * This function return Remote Server Informatiosn to display on Server Profile
     *
     * @param ArrayObject $informations Informations Inputs
     *
     * @return ArrayObject
     */
    public function informations($informations)
    {
        //====================================================================//
        // Init Response Object
        $response = $informations;

        //====================================================================//
        // Company Informations
        $response->company = "...";
        $response->address = "...";
        $response->zip = "...";
        $response->town = "...";
        $response->country = "...";
        $response->www = "...";
        $response->email = "...";
        $response->phone = "...";

        //====================================================================//
        // Server Logo & Images
        $response->icoraw = Splash::file()->readFileContents(
            dirname(dirname(__DIR__))."/img/Splash-ico.png"
        );
        $response->logourl = "https://www.splashsync.com/bundles/theme/img/splash-logo.png";

        //====================================================================//
        // Server Informations
        $response->servertype = "Splash Php Core";
        $response->serverurl = "https://www.splashsync.com";

        //====================================================================//
        // Current Module Version
        $response->moduleversion = SPLASH_VERSION;

        return $response;
    }

    //====================================================================//
    // *******************************************************************//
    //  OPTIONNAl CORE MODULE LOCAL FUNCTIONS
    // *******************************************************************//
    //====================================================================//

    /**
     * Return Local Server Test Sequences as Aarray
     *
     * THIS FUNCTION IS OPTIONNAL - USE IT ONLY IF REQUIRED
     *
     * This function called on each initialization of module's tests sequences.
     * It's aim is to list different configurations for testing on local system.
     *
     * If Name = List, Result must be an array including list of Sequences Names.
     *
     * If Name = ASequenceName, Function will Setup Sequence on Local System.
     *
     * @param null|mixed $name
     *
     * @return array $Sequences
     */
    public function testSequences($name = null)
    {
        switch ($name) {
            case "Sequence1":
                // DO SEQUENCE SETUP
                return array();
            case "Sequence2":
                // DO SEQUENCE SETUP
                return array();
            case "List":
                return array("Sequence1", "Sequence2" );
        }

        return array();
    }

    /**
     *  @abstract       Return Local Server Test Parameters as Array
     *
     *      THIS FUNCTION IS OPTIONNAL - USE IT ONLY IF REQUIRED
     *
     *      This function called on each initialisation of module's tests sequences.
     *      It's aim is to overide general Tests settings to be adjusted to local system.
     *
     *      Result must be an array including parameters as strings or array.
     *
     *  @see Splash\Tests\Tools\ObjectsCase::settings for objects tests settings
     *
     *  @return         array       $parameters
     */
    public function testParameters()
    {
        //====================================================================//
        // Init Parameters Array
        return array();
        // CHANGE SOMETHING
    }

    //====================================================================//
// *******************************************************************//
// Place Here Any SPECIFIC or COMMON Local Functions
// *******************************************************************//
//====================================================================//
}
