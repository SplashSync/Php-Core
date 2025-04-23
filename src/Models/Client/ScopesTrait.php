<?php

namespace Splash\Core\Models\Client;

use Exception;
use PHPUnit\Framework\Assert;
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
            fn($scope) => ScopesHelper::fromCode($scope),
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
            fn($scope) => is_string($scope) ? $scope : null,
            $scopes
        ));
    }
}