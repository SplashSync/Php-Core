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

namespace Splash\Models\Logger;

/**
 * Splash Logger - Console Exports Managment
 */
trait ConsoleExporterTrait
{
    /**
     * Return All WebServer current Log WebServer in Console Colored format
     *
     * @param bool $clean true if messages needs to be cleaned after reading
     *
     * @return string
     */
    public function getConsoleLog(bool $clean = false): string
    {
        $result = null;
        //====================================================================//
        // Read All Messages as Html
        $result .= $this->getConsole($this->err, ' - Error    => ', self::CMD_COLOR_ERR);
        $result .= $this->getConsole($this->war, ' - Warning  => ', self::CMD_COLOR_WAR);
        $result .= $this->getConsole($this->msg, ' - Messages => ', self::CMD_COLOR_MSG);
        $result .= $this->getConsole($this->deb, ' - Debug    => ', self::CMD_COLOR_DEB);
        $result .= "\e[0m";
        //====================================================================//
        // Clear Log Buffer If Requiered
        if ($clean) {
            $this->cleanLog();
        }

        return $result;
    }

    /**
     * Return Text in Console Colored format
     *
     * @param string $text  Raw Console Text
     * @param string $title Displayed Title
     * @param int    $color Display Color has INT
     *
     * @return string
     */
    public static function getConsoleLine(string $text, string $title = '', int $color = 0): string
    {
        return PHP_EOL."\e[".$color.'m'.$title.html_entity_decode($text)."\e[0m";
    }

    /**
     * Return All WebServer current Log WebServer Console Colored format
     *
     * @param null|array $msgArray
     * @param string     $title
     * @param int        $color
     *
     * @return string
     */
    private function getConsole(?array $msgArray, string $title = '', int $color = 0): string
    {
        $result = '';
        if (!empty($msgArray)) {
            //====================================================================//
            // Add Messages
            foreach ($msgArray as $txt) {
                $result .= self::getConsoleLine($txt, $title, $color);
            }
        }

        return $result;
    }
}
