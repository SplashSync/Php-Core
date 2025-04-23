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

namespace Splash\Core\Models\Components\FieldsFactory;

use Splash\Core\Client\Splash;
use Splash\Core\Dictionary\SplFields;
use Splash\Core\Helpers\FieldTemplatesHelper;
use Splash\Core\Interfaces\Fields\FieldTemplateInterface;

trait FactoryTemplateTrait
{
    /**
     * Create a new Field Definition from Template
     *
     * @param string       $fieldId   Local Data Identifier (Shall be unique on local machine)
     * @param class-string $template  Field Template Class
     * @param null|string  $fieldName Data Name (Will Be Translated by Splash if Possible)
     */
    public function createFromTemplate(string $fieldId, string $template, string $fieldName = null): self
    {
        $this
            ->create(SplFields::VARCHAR, $fieldId)
            ->template($template)
        ;
        //====================================================================//
        // Set Field Name
        if ($fieldName) {
            $this->name($fieldName);
        }

        return $this;
    }

    /**
     * Setup Currently Edited Field From Field Template
     *
     * @param class-string $template Field Template Class
     */
    public function template(string $template): self
    {
        //====================================================================//
        // Safety Check - Template Exists
        if (!$fieldTemplate = FieldTemplatesHelper::fromClass($template)) {
            Splash::log()->err(
                sprintf("Unable to apply Field Template %s: Wrong Class", $template)
            );

            return $this;
        }

        //====================================================================//
        // Apply Template Configuration
        if ($current = $this->current()) {
            $current->update($fieldTemplate->getConfiguration($this->dfLanguage));
        }

        return $this;
    }
}
