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
     * Get Field Template from Class
     *
     * @param class-string $template Field Template Class
     */
    public static function fromClass(string $template): ?FieldTemplateInterface
    {
        //====================================================================//
        // Safety Check
        if (!class_exists($template) || !is_subclass_of($template, FieldTemplateInterface::class)) {
            return null;
        }

        return new $template();
    }
}
