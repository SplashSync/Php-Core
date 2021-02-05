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

namespace Splash\Tests\Core;

use Splash\Components\Logger;
use Splash\Core\SplashCore     as Splash;
use Splash\Tests\Tools\AbstractBaseCase;
use Splash\Tests\Tools\TestCase;

/**
 * Core Test Suite - Raw Folders & Class Structure Verifications
 *
 * @author SplashSync <contact@splashsync.com>
 */
class C00StartupTest extends TestCase
{
    const   SPLIT = "----------------------------------------------------------------";

    const   SPLASH1 = " ______     ______   __         ______     ______     __  __    ";
    const   SPLASH2 = "/\\  ___\\   /\\  == \\ /\\ \\       /\\  __ \\   /\\  ___\\   /\\ \\_\\ \\   ";
    const   SPLASH3 = "\\ \\___  \\  \\ \\  _-/ \\ \\ \\____  \\ \\  __ \\  \\ \\___  \\  \\ \\  __ \\  ";
    const   SPLASH4 = " \\/\\_____\\  \\ \\_\\    \\ \\_____\\  \\ \\_\\ \\_\\  \\/\\_____\\  \\ \\_\\ \\_\\ ";
    const   SPLASH5 = "  \\/_____/   \\/_/     \\/_____/   \\/_/\\/_/   \\/_____/   \\/_/\\/_/ ";
    const   SPLASH6 = "                                                                ";

    /**
     * Display of Tested Sequences | Objects | Fields
     *
     * @return void
     */
    public function testDisplayTestContext()
    {
        //====================================================================//
        //   SPLASH SCREEN
        //====================================================================//

        echo PHP_EOL;

        echo Logger::getConsoleLine("", self::SPLIT, Logger::CMD_COLOR_MSG);
        echo Logger::getConsoleLine("", self::SPLASH1, Logger::CMD_COLOR_WAR);
        echo Logger::getConsoleLine("", self::SPLASH2, Logger::CMD_COLOR_WAR);
        echo Logger::getConsoleLine("", self::SPLASH3, Logger::CMD_COLOR_WAR);
        echo Logger::getConsoleLine("", self::SPLASH4, Logger::CMD_COLOR_WAR);
        echo Logger::getConsoleLine("", self::SPLASH5, Logger::CMD_COLOR_WAR);
        echo Logger::getConsoleLine("", self::SPLASH6, Logger::CMD_COLOR_WAR);
        echo Logger::getConsoleLine("", self::SPLIT, Logger::CMD_COLOR_MSG);

        //====================================================================//
        //   TEST MAIN INFORMATIONS
        //====================================================================//

        $this->displayTestedObjects();
        $this->displayTestedSequences();
        $this->displayFilteredFields();

        echo Logger::getConsoleLine("", self::SPLIT, Logger::CMD_COLOR_MSG);
        echo PHP_EOL.".";

        $this->assertTrue(true);
    }

    /**
     * Display of Tested Objects List
     *
     * @return void
     */
    private function displayTestedObjects()
    {
        //====================================================================//
        //   TESTED OBJECTS
        //====================================================================//
        $objectTypes = Splash::objects();
        if (!is_array($objectTypes)) {
            echo Logger::getConsoleLine(" !! Invalid Objects List !! ", " - Tested Objects ", Logger::CMD_COLOR_DEB);

            return;
        }
        foreach ($objectTypes as $key => $objectType) {
            //====================================================================//
            //   Filter Tested Object Types  =>> Skip
            if (!AbstractBaseCase::isAllowedObjectType($objectType)) {
                unset($objectTypes[$key]);
            }
        }
        echo Logger::getConsoleLine(implode(" | ", $objectTypes), "- Tested Objects: ", Logger::CMD_COLOR_DEB);
    }

    /**
     * Display of Tested Sequences List
     *
     * @return void
     */
    private function displayTestedSequences()
    {
        //====================================================================//
        //   TESTED SEQUENCES
        //====================================================================//

        //====================================================================//
        // Check if Local Tests Sequences are defined
        $testSequences = "None";
        if (!empty(Splash::local()->testSequences("List"))) {
            $testSequences = Splash::local()->testSequences("List");
        }
        if ("None" === $testSequences) {
            return;
        }
        foreach ($testSequences as $key => $testSequence) {
            //====================================================================//
            //   Filter Tested Sequence  =>> Skip
            if (!AbstractBaseCase::isAllowedSequence($testSequence)) {
                unset($testSequences[$key]);
            }
        }

        echo Logger::getConsoleLine(implode(" | ", $testSequences), "- Test Sequences: ", Logger::CMD_COLOR_DEB);
    }

    /**
     * Display of Filter on Objets Fields
     *
     * @return void
     */
    private function displayFilteredFields()
    {
        //====================================================================//
        //   FILTERED FIELDS
        //====================================================================//

        //====================================================================//
        //   Filter Tested Object Fields  =>> Skip
        if (defined("SPLASH_FIELDS") && is_scalar(SPLASH_FIELDS) && !empty(explode(",", (string) SPLASH_FIELDS))) {
            echo Logger::getConsoleLine((string) SPLASH_FIELDS, "- Fields Filter: ", Logger::CMD_COLOR_DEB);
            echo Logger::getConsoleLine("!! TEST WILL FOCUS ON SPECIFIC FIELDS !!", "", Logger::CMD_COLOR_DEB);
        }
    }
}
