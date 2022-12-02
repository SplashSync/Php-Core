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

namespace   Splash\Models\Helpers;

use Splash\Client\Splash;

/**
 * Detect Splash Connexion Configuration from Url String & Environment
 */
class SplashUrlHelper
{
    /**
     * @var string
     */
    const KEY = "SPLASH_CONNEXION";

    /**
     * @var string[]
     */
    const REQUIRED = array("scheme", "host", "user", "pass", "path");

    /**
     * Detect Splash Connexion Url & Complete Splash Parameters Array
     *
     * @param array       $params Splash Parameters Array
     * @param null|string $input  Force input Url or Loaded from ENV
     *
     * @return bool Configuration Updated
     */
    public static function completeParameters(array &$params, ?string $input = null): bool
    {
        $updated = false;
        //====================================================================//
        // Detect Splash Url
        if (empty($parsedUrl = self::parseUrl($input))) {
            return false;
        }
        //====================================================================//
        // Override Parameters
        //====================================================================//

        //====================================================================//
        // Server Identification Parameters
        if (empty($params['WsIdentifier'])) {
            $params['WsIdentifier'] = (string) $parsedUrl["user"];
            $updated = true;
        }
        if (empty($params['WsEncryptionKey'])) {
            $params['WsEncryptionKey'] = (string) $parsedUrl["pass"];
            $updated = true;
        }

        //====================================================================//
        // Server Host Address
        if (empty($params['WsHost'])) {
            $params['WsHost'] =
                $parsedUrl["scheme"]."://".$parsedUrl["host"]
                .(empty($parsedUrl["port"]) ? "" : ":".$parsedUrl["port"])
                ."/".$parsedUrl["path"]
            ;
            $updated = true;
        }

        return $updated;
    }

    /**
     * Detect & Parse Splash Connexion Url
     *
     * @param null|string $input Force input Url or Loaded from ENV
     *
     * @return null|array Exploded Connexion Url
     */
    public static function parseUrl(?string $input = null): ?array
    {
        //====================================================================//
        // Load url from ENV if Necessary
        $input = $input ?? Splash::input(self::KEY, INPUT_ENV);
        //====================================================================//
        // Parse Url
        if (empty($input) || !is_array($parsedUrl = parse_url($input))) {
            return null;
        }
        //====================================================================//
        // Safety Check - Verify All Data are Present
        $intersection = array_intersect_key($parsedUrl, array_flip(self::REQUIRED));
        if (count($intersection) != count(self::REQUIRED)) {
            return null;
        }

        return $parsedUrl;
    }
}
