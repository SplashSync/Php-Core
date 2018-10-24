<?php
/*
 * Copyright (C) 2011-2014  Bernard Paquier       <bernard.paquier@gmail.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 *
 *
 *  \Id 	$Id: osws-local-Main.class.php 136 2014-10-12 22:33:28Z Nanard33 $
 *  \version    $Revision: 136 $
 *  \date       $LastChangedDate: 2014-10-13 00:33:28 +0200 (lun. 13 oct. 2014) $
 *  \ingroup    Splash - OpenSource Synchronisation Service
 *  \brief      Core Local Server Definition Class
 *  \class      SplashLocal
 *  \remarks    Designed for Splash Module - Dolibar ERP Version
*/

namespace Splash\Local;

use ArrayObject;
use Splash\Models\LocalClassInterface;
use Splash\Core\SplashCore      as Splash;

/**
 * @abstract    Local System Core Management Class
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
     *      @abstract       Class Constructor (Used only if localy necessary)
     *      @return         int                     0 if KO, >0 if OK
     */
    public function __construct()
    {
        //====================================================================//
        // Place Here Any SPECIFIC Initialisation Code
        //====================================================================//
        
        return true;
    }

//====================================================================//
// *******************************************************************//
//  MANDATORY CORE MODULE LOCAL FUNCTIONS
// *******************************************************************//
//====================================================================//
    
    /**
     *  @abstract       Return Local Server Parameters as Array
     *
     *      THIS FUNCTION IS MANDATORY
     *
     *      This function called on each initialization of the module
     *
     *      Result must be an array including mandatory parameters as strings
     *         ["WsIdentifier"]         =>>  Name of Module Default Language
     *         ["WsEncryptionKey"]      =>>  Name of Module Default Language
     *         ["DefaultLanguage"]      =>>  Name of Module Default Language
     *
     * @return  array   $parameters
     */
    public function parameters()
    {
    }
    
    /**
     * @abstract       Include Local Includes Files
     *
     *      Include here any local files required by local functions.
     *      This Function is called each time the module is loaded
     *
     *      There may be differents scenarios depending if module is
     *      loaded as a library or as a NuSOAP Server.
     *
     *      This is triggered by global constant SPLASH_SERVER_MODE.
     *
     * @return         bool
     */
    public function includes()
    {

        //====================================================================//
        // When Library is called in server mode ONLY
        //====================================================================//
        if (SPLASH_SERVER_MODE) {
            // NOTHING TO DO
        //====================================================================//
        // When Library is called in client mode ONLY
        //====================================================================//
        } else {
            // NOTHING TO DO
        }

        //====================================================================//
        // When Library is called in both client & server mode
        //====================================================================//

        return true;
    }
           
    /**
     * @abstract       Return Local Server Self Test Result
     *
     *      THIS FUNCTION IS MANDATORY
     *
     *      This function called during Server Validation Process
     *
     *      We recommand using this function to validate all functions or parameters
     *      that may be required by Objects, Widgets or any other module specific action.
     *
     *      Use Module Logging system & translation tools to return test results Logs
     *
     * @return         bool    global test result
     */
    public function selfTest()
    {
        return true;
    }
    
    /**
     *  @abstract   Update Server Informations with local Data
     *
     *      THIS FUNCTION IS MANDATORY
     *
     *      This function return Remote Server Informatiosn to display on Server Profile
     *
     *  @param     ArrayObject  $Informations   Informations Inputs
     *
     *  @return     ArrayObject
     */
    public function informations($Informations)
    {
        //====================================================================//
        // Init Response Object
        $Response = $Informations;

        //====================================================================//
        // Company Informations
        $Response->company          =   "...";
        $Response->address          =   "...";
        $Response->zip              =   "...";
        $Response->town             =   "...";
        $Response->country          =   "...";
        $Response->www              =   "...";
        $Response->email            =   "...";
        $Response->phone            =   "...";
        
        //====================================================================//
        // Server Logo & Images
        $Response->icoraw           =   Splash::file()->readFileContents(
            dirname(dirname(__DIR__)) . "/img/Splash-ico.png"
        );
        $Response->logourl          =   "https://www.splashsync.com/bundles/theme/img/splash-logo.png";
        
        //====================================================================//
        // Server Informations
        $Response->servertype       =   "Splash Php Core";
        $Response->serverurl        =   "https://www.splashsync.com";
        
        //====================================================================//
        // Current Module Version
        $Response->moduleversion    =   SPLASH_VERSION;
        
        return $Response;
    }
    
//====================================================================//
// *******************************************************************//
//  OPTIONNAl CORE MODULE LOCAL FUNCTIONS
// *******************************************************************//
//====================================================================//
    
    /**
     * @abstract       Return Local Server Test Sequences as Aarray
     *
     *      THIS FUNCTION IS OPTIONNAL - USE IT ONLY IF REQUIRED
     *
     *      This function called on each initialization of module's tests sequences.
     *      It's aim is to list different configurations for testing on local system.
     *
     *      If Name = List, Result must be an array including list of Sequences Names.
     *
     *      If Name = ASequenceName, Function will Setup Sequence on Local System.
     *
     * @return         array       $Sequences
     */
    public function testSequences($Name = null)
    {
        switch ($Name) {
            case "Sequence1":
                // DO SEQUENCE SETUP
                return array();
                
            case "Sequence2":
                // DO SEQUENCE SETUP
                return array();
                
            case "List":
                return array("Sequence1", "Sequence2" );
        }
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
        $Parameters       =     array();

        // CHANGE SOMETHING
        
        return $Parameters;
    }
    
//====================================================================//
// *******************************************************************//
// Place Here Any SPECIFIC or COMMON Local Functions
// *******************************************************************//
//====================================================================//
}
