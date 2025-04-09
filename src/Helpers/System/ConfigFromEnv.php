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

/**
 * Detect Splash Connexion Configuration from Url String & Environment
 */
class ConfigFromEnv
{
    /**
     * @var string
     */
    const CONNEXION = "SPLASH_CONNEXION";

    /**
     * @var string
     */
    const ENDPOINT = "SPLASH_ENDPOINT";

    /**
     * Detect Splash Connexion & Endpoint Url, & Complete Server Parameters Array
     *
     * @param array       $params    Splash Parameters Array
     * @param null|string $connexion Force Connexion Url or Loaded from ENV
     * @param null|string $endpoint  Force Endpoint Url or Loaded from ENV
     *
     * @return bool Configuration Updated
     */
    public static function complete(array &$params, ?string $connexion = null, ?string $endpoint = null): bool
    {
        $connexion = self::completeConnexion($params, $connexion);
        $endpoint = self::completeEndpoint($params, $endpoint);

        return $connexion || $endpoint;
    }

    /**
     * Detect Splash Connexion Url & Complete Server Parameters Array
     *
     * @param array       $params    Splash Parameters Array
     * @param null|string $connexion Force input Url or Loaded from ENV
     *
     * @return bool Configuration Updated
     */
    private static function completeConnexion(array &$params, ?string $connexion = null): bool
    {
        $updated = false;
        //====================================================================//
        // Detect Splash Connexion Url
        if (empty($parsedUrl = self::parseConnexionUrl($connexion))) {
            return false;
        }
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
            $params['WsHost'] = sprintf(
                "%s://%s%s%s",
                $parsedUrl["scheme"],
                $parsedUrl["host"],
                empty($parsedUrl["port"]) ? "" : ":".$parsedUrl["port"],
                $parsedUrl["path"] ?? ""
            );
            $updated = true;
        }

        return $updated;
    }

    /**
     * Detect Splash Endpoint Url & Complete Server Parameters Array
     *
     * @param array       $params   Splash Parameters Array
     * @param null|string $endpoint Force Server Endpoint Url or Loaded from ENV
     *
     * @return bool Configuration Updated
     */
    private static function completeEndpoint(array &$params, ?string $endpoint = null): bool
    {
        $updated = false;
        //====================================================================//
        // Detect Splash Endpoint Url
        if (empty($parsedUrl = self::parseEndpointUrl($endpoint))) {
            return false;
        }
        //====================================================================//
        // Server Host Parameters
        if (empty($params['ServerHost'])) {
            $params['ServerHost'] = sprintf(
                "%s://%s%s",
                $parsedUrl["scheme"],
                $parsedUrl["host"],
                empty($parsedUrl["port"]) ? "" : ":".$parsedUrl["port"]
            );
            $updated = true;
        }
        if (empty($params['ServerPath'])) {
            $params['ServerPath'] = (string) $parsedUrl["path"];
            $updated = true;
        }

        return $updated;
    }

    /**
     * Detect & Parse Splash Endpoint Url
     *
     * @param null|string $input Force input Url or Loaded from ENV
     *
     * @return null|array Exploded Connexion Url
     */
    private static function parseConnexionUrl(?string $input = null): ?array
    {
        $required = array("scheme", "host", "user", "pass", "path");
        //====================================================================//
        // Load url from ENV if Necessary
        $input = $input ?? Splash::input(self::CONNEXION, INPUT_ENV);
        //====================================================================//
        // Parse Url
        if (empty($input) || !is_array($parsedUrl = parse_url($input))) {
            return null;
        }
        //====================================================================//
        // Safety Check - Verify All Data are Present
        $intersection = array_intersect_key($parsedUrl, array_flip($required));
        if (count($intersection) != count($required)) {
            return null;
        }

        return $parsedUrl;
    }

    /**
     * Detect & Parse Splash Connexion Url
     *
     * @param null|string $input Force input Url or Loaded from ENV
     *
     * @return null|array Exploded Connexion Url
     */
    private static function parseEndpointUrl(?string $input = null): ?array
    {
        $required = array("scheme", "host", "path");
        //====================================================================//
        // Load url from ENV if Necessary
        $input = $input ?? Splash::input(self::ENDPOINT, INPUT_ENV);
        //====================================================================//
        // Parse Url
        if (empty($input) || !is_array($parsedUrl = parse_url($input))) {
            return null;
        }
        //====================================================================//
        // Safety Check - Verify All Data are Present
        $intersection = array_intersect_key($parsedUrl, array_flip($required));
        if (count($intersection) != count($required)) {
            return null;
        }

        return $parsedUrl;
    }
}
