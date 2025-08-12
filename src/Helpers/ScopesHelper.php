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

namespace Splash\Core\Helpers;

use Splash\Core\Interfaces\Scopes\ScopeInterface;

/**
 * Helper to Work with Server Features Scopes
 */
class ScopesHelper
{
    /**
     * Get Scope Definition from Code or Class
     *
     * @param string $scopeCodeOrClass Feature Scope Code or Class
     */
    public static function resolve(string $scopeCodeOrClass): ?ScopeInterface
    {
        return self::fromClass($scopeCodeOrClass)
            ?? self::fromCode($scopeCodeOrClass)
        ;
    }

    /**
     * Get Scope Definition from Code
     *
     * @param string $scopeCode Server Scope Code
     */
    public static function fromCode(string $scopeCode): ?ScopeInterface
    {
        //====================================================================//
        // Convert Code into PHP Class Name
        $className = StringConverter::toClass($scopeCode);
        //====================================================================//
        // Ensure Class Exists
        if (!$className || !class_exists($className) || !is_subclass_of($className, ScopeInterface::class)) {
            return null;
        }

        return new $className();
    }

    /**
     * Get Scope Identification Code
     *
     * @param class-string $scopeClass Server Scope Class
     */
    public static function getCode(string $scopeClass): string
    {
        return StringConverter::getCode($scopeClass);
    }

    /**
     * Get Scope Definition from Class
     *
     * @param string $scopeClass Server Scope Class
     */
    public static function fromClass(string $scopeClass): ?ScopeInterface
    {
        //====================================================================//
        // Safety Check
        if (!class_exists($scopeClass) || !is_subclass_of($scopeClass, ScopeInterface::class)) {
            return null;
        }

        return new $scopeClass();
    }
}
