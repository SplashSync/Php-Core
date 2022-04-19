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

namespace Splash\Client;

use Splash\Components\CommitsManager;
use Splash\Core\SplashCore;

/**
 * Main User Client Class for Using Splash Webservice Module
 */
class Splash extends SplashCore
{
    //--------------------------------------------------------------------//
    //--------------------------------------------------------------------//
    //----  PING WEBSERVICE FUNCTIONS                                 ----//
    //--------------------------------------------------------------------//
    //--------------------------------------------------------------------//

    /**
     * Check Connexion with NuSOAP Client
     *
     * @param bool $silent No message display if non errors
     *
     * @return bool
     */
    public static function ping(bool $silent = false): bool
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();
        //====================================================================//
        // Initiate Performance Timer
        $intTimer = microtime(true);
        //====================================================================//
        // Run NuSOAP Call
        $result = self::ws()->call(SPL_S_PING, null, true);
        //====================================================================//
        //  Messages Debug Information
        //====================================================================//
        if (self::configuration()->TraceTasks) {
            $total = sprintf('%.2f %s', 1000 * (microtime(true) - $intTimer), ' ms');
            self::log()->war('===============================================');
            self::log()->war('Splash - Ping : '.$total);
        }
        //====================================================================//
        // Analyze NuSOAP results
        if ($result && !empty($result['result'] ?? false) && ($silent)) {
            self::log()->cleanLog();

            return true;
        }
        //====================================================================//
        // If Not Silent, Display result
        if ($result && !empty($result['result'] ?? false)) {
            return self::log()->msg('Remote Client Ping Passed ('.self::ws()->url.')');
        }

        return self::log()->err('Remote Client Ping Failed ('.self::ws()->url.')');
    }

    //--------------------------------------------------------------------//
    //--------------------------------------------------------------------//
    //----  CONNECT WEBSERVICE FUNCTIONS                              ----//
    //--------------------------------------------------------------------//
    //--------------------------------------------------------------------//

    /**
     * Check Connexion with NuSOAP Client
     *
     * @param bool $silent No message display if non errors
     *
     * @return bool
     */
    public static function connect(bool $silent = false): bool
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();
        //====================================================================//
        // Initiate Performance Timer
        $initTimer = microtime(true);
        //====================================================================//
        // Run NuSOAP Call
        $result = self::ws()->call(SPL_S_CONNECT);
        //====================================================================//
        //  Messages Debug Information
        //====================================================================//
        if (self::configuration()->TraceTasks) {
            $total = sprintf('%.2f %s', 1000 * (microtime(true) - $initTimer), ' ms');
            self::log()->war('===============================================');
            self::log()->war('Splash - Connect : '.$total);
        }
        //====================================================================//
        // Analyze NuSOAP results
        if (!$result || empty($result['result'] ?? false)) {
            return self::log()->err('Remote Client Connection Failed ('.self::ws()->url.')');
        }
        //====================================================================//
        // If Not Silent, Display result
        if ($silent) {
            self::log()->cleanLog();
        }

        return true;
    }

    //--------------------------------------------------------------------//
    //--------------------------------------------------------------------//
    //---- USER MAIN FUNCTIONS                                        ----//
    //--------------------------------------------------------------------//
    //--------------------------------------------------------------------//

    /**
     * Submit an Update for a Local Object
     *
     * @param string           $objectType Object Type Name
     * @param array|int|string $local      Local Object Ids or Array of Local ID
     * @param string           $action     Action Type (SPL_A_UPDATE, or SPL_A_CREATE, or SPL_A_DELETE)
     * @param string           $user       User Name
     * @param string           $comment    Operation Comment for Logs
     *
     * @return bool
     */
    public static function commit(
        string $objectType,
        $local,
        string $action,
        string $user = '',
        string $comment = ''
    ): bool {
        //====================================================================//
        // Forward to Commit Manager
        return CommitsManager::commit($objectType, $local, $action, $user, $comment);
    }
}
