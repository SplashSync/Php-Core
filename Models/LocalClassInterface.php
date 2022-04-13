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

namespace Splash\Models;

use ArrayObject;

/**
 * Local System Core Management Class Interface
 */
interface LocalClassInterface
{
    //====================================================================//
    // *******************************************************************//
    //  MANDATORY CORE MODULE LOCAL FUNCTIONS
    // *******************************************************************//
    //====================================================================//

    /**
     *  Return Local Server Parameters as Array
     *
     * THIS FUNCTION IS MANDATORY
     *
     * This function called on each initialization of the module
     *
     * Result must be an array including mandatory parameters as strings
     *      ["WsIdentifier"]         =>>  Name of Module Default Language
     *      ["WsEncryptionKey"]      =>>  Name of Module Default Language
     *      ["DefaultLanguage"]      =>>  Name of Module Default Language
     *
     * @return array $parameters
     */
    public function parameters(): array;

    /**
     * Include Local Includes Files
     *
     * Include here any local files required by local functions.
     * This Function is called each time the module is loaded
     *
     * There may be different scenarios depending on if module is
     * loaded as a library or as a NuSOAP Server.
     *
     * This is triggered by global constant SPLASH_SERVER_MODE.
     *
     * @return bool
     */
    public function includes(): bool;

    /**
     * Return Local Server Self Test Result
     *
     * THIS FUNCTION IS MANDATORY
     *
     * This function called during Server Validation Process
     *
     * We recommend using this function to validate all functions or parameters
     * that may be required by Objects, Widgets or any other module specific action.
     *
     * Use Module Logging system & translation tools to return test results Logs
     *
     * @return bool global test result
     */
    public function selfTest(): bool;

    /**
     * Update Server Information with local Data
     *
     * THIS FUNCTION IS MANDATORY
     *
     * This function return Remote Server Information to display on Server Profile
     *
     * @param ArrayObject $informations Information Inputs
     *
     * @return ArrayObject
     */
    public function informations(ArrayObject $informations): ArrayObject;

    //====================================================================//
    // *******************************************************************//
    //  OPTIONAl CORE MODULE LOCAL FUNCTIONS
    // *******************************************************************//
    //====================================================================//

    /**
     * Return Local Server Test Sequences as Array
     *
     * THIS FUNCTION IS OPTIONAL - USE IT ONLY IF REQUIRED
     *
     * This function called on each initialization of module's tests sequences.
     * Its aim is to list different configurations for testing on local system.
     *
     * If Name = List, Result must be an array including list of Sequences Names.
     *
     * If Name = ASequenceName, Function will Set up Sequence on Local System.
     *
     * @param null|string $name
     *
     * @return array $Sequences
     */
    public function testSequences(string $name = null): array;

    /**
     * Return Local Server Test Parameters as Array
     *
     * THIS FUNCTION IS OPTIONAL - USE IT ONLY IF REQUIRED
     *
     * This function called on each initialisation of module's tests sequences.
     * Its aim is to override general Tests settings to be adjusted to local system.
     *
     * Result must be an array including parameters as strings or array.
     *
     * @see Splash\Tests\Tools\ObjectsCase::settings for objects tests settings
     *
     * @return array $parameters
     */
    public function testParameters(): array;
}
