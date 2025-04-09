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

namespace Splash\Core\Helpers\System;

use Splash\Core\Client\Splash;
use Splash\Core\Dictionary\SplDefinition;

class ServerInfos
{
    /**
     * Detect Server Information
     *
     * @return array
     */
    public static function getInfos(): array
    {
        //====================================================================//
        // Init Result Array
        $response = array();

        //====================================================================//
        // INFO - Server Language Type
        $response['ServerType'] = 'PHP';
        // INFO - Server Language Version
        $response['ServerVersion'] = PHP_VERSION;
        // INFO - Server Protocol Version
        $response['ProtocolVersion'] = SplDefinition::PROTOCOL;
        //====================================================================//
        // INFO - Current Url
        $response['Self'] = Splash::input('PHP_SELF');
        // INFO - Server IP Address
        $response['ServerAddress'] = Splash::input('SERVER_ADDR');
        // Read System Folder without symlinks
        $response['ServerRoot'] = realpath((string) Splash::input('DOCUMENT_ROOT'));
        // INFO - Browser User Agent
        $response['UserAgent'] = Splash::input('HTTP_USER_AGENT');
        // Current Splash WebService Component
        $response['WsMethod'] = Splash::configuration()->WsMethod;

        //====================================================================//
        // CRITICAL - Server Host Name
        $response['ServerHost'] = self::getServerName();
        //====================================================================//
        // Server IPv4 Address
        $response['ServerIP'] = Splash::input('SERVER_ADDR');
        //====================================================================//
        // Server WebService Path
        $response['ServerPath'] = self::getServerPath();

        return $response;
    }

    /**
     * Safe Get Client Server Url
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function getServerName(): string
    {
        //====================================================================//
        // Check if Server Name is Override by Application Module
        if (isset(Splash::configuration()->ServerHost)) {
            return Splash::configuration()->ServerHost;
        }
        //====================================================================//
        // Check if Available with Secured Reading
        if (!empty(Splash::input('SERVER_NAME'))) {
            return Splash::input('SERVER_NAME');
        }
        //====================================================================//
        // Fallback to Unsecured Mode (Required for Phpunit)
        if (isset($_SERVER['SERVER_NAME'])) {
            return $_SERVER['SERVER_NAME'];
        }

        return '';
    }

    /**
     * Safe Get Client Server Path
     */
    public static function getServerPath(): ?string
    {
        //====================================================================//
        // Server WebService Path from Local Configuration
        $configPath = Splash::configuration()->ServerPath ?? null;
        if ($configPath && is_string($configPath)) {
            return Splash::configuration()->ServerPath;
        }

        //====================================================================//
        // Guess WebService Path from Server Globals
        //====================================================================//

        //====================================================================//
        // Read System Folder without symlinks
        if (!$serverRoot = realpath((string) Splash::input('DOCUMENT_ROOT'))) {
            return null;
        }
        //====================================================================//
        // Detect Core Module Installation Folder
        $modulePath = self::getModulePath();
        //====================================================================//
        // Detect Core Module Installation Folder
        $relPath = explode($serverRoot, $modulePath);
        if (is_array($relPath) && isset($relPath[1])) {
            return $relPath[1].'/soap.php';
        }

        return null;
    }

    /**
     * Get Client Server Schema (http or https)
     */
    public static function getScheme(): string
    {
        if ((!empty(Splash::input('REQUEST_SCHEME')) && ('https' == Splash::input('REQUEST_SCHEME'))) ||
            (!empty(Splash::input('HTTPS')) && ('on' == Splash::input('HTTPS'))) ||
            (!empty(Splash::input('SERVER_PORT')) && ('443' == Splash::input('SERVER_PORT')))) {
            return 'https';
        }

        return 'http';
    }

    /**
     * Safe Get Module Server Path
     */
    public static function getModulePath(): string
    {
        return dirname(__DIR__, 3);
    }
}
