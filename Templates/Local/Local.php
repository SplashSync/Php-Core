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
     * Class Constructor (Used only if locally necessary)
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
     * {@inheritDoc}
     */
    public function parameters(): array
    {
        return array();
    }

    /**
     * {@inheritDoc}
     */
    public function includes(): bool
    {
        //====================================================================//
        // When Library is called in server mode ONLY
        //====================================================================//
        /** @phpstan-ignore-next-line */
        if (defined('SPLASH_SERVER_MODE') && SPLASH_SERVER_MODE) {
            //====================================================================//
            // When Library is called in client mode ONLY
            //====================================================================//
            // NOTHING TO DO
        }

        //====================================================================//
        // When Library is called in both client & server mode
        //====================================================================//
        // NOTHING TO DO

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function selfTest(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function informations(ArrayObject $informations): ArrayObject
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
    //  OPTIONAl CORE MODULE LOCAL FUNCTIONS
    // *******************************************************************//
    //====================================================================//

    /**
     * {@inheritDoc}
     */
    public function testSequences(string $name = null): array
    {
        switch ($name) {
            case "Sequence1":
                // DO SEQUENCE SETUP
                return array();
            case "List":
                return array("Sequence1", "Sequence2" );
        }

        return array();
    }

    /**
     * {@inheritDoc}
     */
    public function testParameters(): array
    {
        //====================================================================//
        // Init Parameters Array
        return array();
        // CHANGE SOMETHING
    }
}
