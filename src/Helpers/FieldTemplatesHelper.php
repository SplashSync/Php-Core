<?php

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