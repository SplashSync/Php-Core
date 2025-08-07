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

use Splash\Core\Interfaces\Fields\FieldTemplateInterface;

/**
 * Helper to Work with Object Field templates
 */
class FieldTemplatesHelper
{
    /**
     * Get Field Template Identification Code
     *
     * @param class-string $templateClass Field Template Class
     */
    public static function getCode(string $templateClass): string
    {
        return StringConverter::getCode($templateClass);
    }

    /**
     * Get Field Template Definition from Code
     *
     * @param string $templateCode Field Template Code
     */
    public static function fromCode(string $templateCode): ?FieldTemplateInterface
    {
        //====================================================================//
        // Convert Code into PHP Class Name
        $className = StringConverter::toClass($templateCode);
        //====================================================================//
        // Ensure Class Exists
        if (!$className || !class_exists($className) || !is_subclass_of($className, FieldTemplateInterface::class)) {
            return null;
        }

        return new $className();
    }

    /**
     * Get Field Template from Class
     *
     * @param string $templateClass Field Template Class
     */
    public static function fromClass(string $templateClass): ?FieldTemplateInterface
    {
        //====================================================================//
        // Safety Check
        if (!class_exists($templateClass) || !is_subclass_of($templateClass, FieldTemplateInterface::class)) {
            return null;
        }

        return new $templateClass();
    }
}
