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

namespace Splash\Core\Models\Logger;

use Splash\Core\Client\Splash;

/**
 * Splash Logger - File Exports Management
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
    protected static function addLogToFile(string $message, string $logType = 'Unknown'): bool
    {
        //====================================================================//
        // Safety Check
        if (0 == Splash::configuration()->Logging) {
            return true;
        }
        //====================================================================//
        // Detect Log File Directory
        $logfile = Splash::getLocalPath().'/splash.log';
        //====================================================================//
        // Open Log File
        $fileFd = @fopen($logfile, 'a+');
        //====================================================================//
        // Write Log File
        if ($fileFd) {
            $message = date('Y-m-d H:i:s').' '.sprintf('%-15s', $logType).$message;
            fwrite($fileFd, $message."\n");
            fclose($fileFd);
            @chmod($logfile, 0604);
        }

        return true;
    }

    /**
     * Add a Messages Block to Log File
     *
     * @param null|array $msgArray Array of Message text to log
     * @param string     $logType  Message Type
     *
     * @return true
     */
    protected static function addLogBlockToFile(?array $msgArray, string $logType = 'Unknown'): bool
    {
        //====================================================================//
        // Safety Check
        if (empty(Splash::configuration()->Logging)) {
            return true;
        }
        //====================================================================//
        // Run a Messages List
        if (!empty($msgArray)) {
            foreach ($msgArray as $message) {
                //====================================================================//
                // Add Message To Log File
                self::addLogToFile(utf8_decode(html_entity_decode($message)), $logType);
            }
        }

        return true;
    }
}
