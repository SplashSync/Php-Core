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

namespace Splash\Core\Models\Client;

use Exception;
use Splash\Core\Client\Splash;
use Splash\Core\Helpers\ScopesHelper;
use Splash\Core\Interfaces\Scopes\ScopeInterface;

/**
 * Manage Access to Scopes for Splash Base Client
 */
trait ScopesTrait
{
    /**
     * Get All Available Scopes in Local System
     *
     * @return ScopeInterface[]
     */
    public static function scopes(): array
    {
        return array_filter(array_map(
            fn ($scope) => ScopesHelper::fromCode($scope),
            self::getScopesCodes()
        ));
    }

    /**
     * Get Codes of All Available Scopes
     *
     * @return string[]
     */
    public static function getScopesCodes(): array
    {
        //====================================================================//
        // Fetch Server Information
        try {
            $scopes = Splash::informations()['scopes'] ?? null;
        } catch (Exception $e) {
            $scopes = array();
        }
        //====================================================================//
        // No Scopes Defined
        if (empty($scopes) || !is_array($scopes)) {
            return array();
        }

        return array_filter(array_map(
            fn ($scope) => is_string($scope) ? $scope : null,
            $scopes
        ));
    }
}
