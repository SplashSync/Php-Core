<?php

namespace Splash\Core\Helpers;

use Splash\Core\Interfaces\Scopes\ScopeInterface;

/**
 * Helper to Work with Server Features Scopes
 */
class ScopesHelper
{
    /**
     * Get Scope Definition from Code
     *
     * @param string $scopeCode Server Scope Code
     */
    public static function fromCode(string $scopeCode): ?ScopeInterface
    {
        //====================================================================//
        // Convert Code into PHP Class Name
        $className = self::toClass($scopeCode);
        //====================================================================//
        // Ensure Class Exists
        if(!class_exists($className) || !is_subclass_of($className, ScopeInterface::class)) {
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
        //====================================================================//
        // Convert PHP Class Name to Code
        $codeParts = array_map(
            fn(string $part) => strtolower($part),
            explode("\\", $scopeClass)
        );

        return implode(".", $codeParts);
    }

    /**
     * Get Scope Definition from Class
     *
     * @param class-string $scopeClass Server Scope Class
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

    /**
     * Convert Scope Code to PHP Classname
     *
     * @param string $scopeCode Server Scope Code
     */
    public static function toClass(string $scopeCode): ?string
    {
        $classParts = array_map(
            fn(string $part) => ucfirst($part),
            explode(".", $scopeCode)
        );

        return implode("\\", $classParts) ?: null;
    }
}