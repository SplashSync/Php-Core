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

namespace Splash\Models\Logger;

use ArrayObject;
use Countable;
use Splash\Core\SplashCore      as Splash;

/**
 * Splash Logger - File Exports Managment
 */
trait FileExporterTrait
{
    /**
     * Add a message to Log File
     *
     * @param string $message Message text to log
     * @param string $logType Message Type
     *
     * @return true
     */
    protected static function addLogToFile($message, $logType = 'Unknown')
    {
        //====================================================================//
        // Safety Check
        if (0 == Splash::configuration()->Logging) {
            return true;
        }
        //====================================================================//
        // Detect Log File Directory
        $logfile = dirname(__DIR__).'/splash.log';
        if (defined('SPLASH_DIR') && realpath(SPLASH_DIR)) {
            $logfile = realpath(SPLASH_DIR).'/splash.log';
        }
        //====================================================================//
        // Open Log File
        $filefd = @fopen($logfile, 'a+');
        //====================================================================//
        // Write Log File
        if ($filefd) {
            $message = date('Y-m-d H:i:s').' '.sprintf('%-15s', $logType).$message;
            fwrite($filefd, $message."\n");
            fclose($filefd);
            @chmod($logfile, 0604);
        }

        return true;
    }

    /**
     * Add a Messages Block to Log File
     *
     * @param null|array|ArrayObject $msgArray Array of Message text to log
     * @param string                 $logType  Message Type
     *
     * @return true
     */
    protected static function addLogBlockToFile($msgArray, $logType = 'Unknown')
    {
        //====================================================================//
        // Safety Check
        if (false == Splash::configuration()->Logging) {
            return true;
        }
        //====================================================================//
        // Run a Messages List
        if ((is_array($msgArray) || $msgArray instanceof Countable) && count($msgArray)) {
            foreach ($msgArray as $message) {
                //====================================================================//
                // Add Message To Log File
                self::addLogToFile(utf8_decode(html_entity_decode($message)), $logType);
            }
        }

        return true;
    }
}
