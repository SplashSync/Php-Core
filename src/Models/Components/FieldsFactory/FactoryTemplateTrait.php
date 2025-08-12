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
use Splash\Core\Dictionary\Fields\SplFieldConstraints;
use Splash\Core\Dictionary\SplFields;
use Splash\Core\Helpers\FieldTemplatesHelper;

trait FactoryTemplateTrait
{
    /**
     * Create a new Field Definition from Template
     *
     * @param string       $fieldId   Local Data Identifier (Shall be unique on local machine)
     * @param class-string $template  Field Template Class
     * @param null|string  $fieldName Data Name (Will Be Translated by Splash if Possible)
     */
    public function createFromTemplate(string $fieldId, string $template, ?string $fieldName = null): self
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
     * @param string $template Field Template Class or Code
     */
    public function template(string $template): self
    {
        //====================================================================//
        // Safety Check - Template Exists
        if (!$fieldTemplate = FieldTemplatesHelper::resolve($template)) {
            Splash::log()->err(
                sprintf("Unable to apply Field Template %s: Wrong Code or Class", $template)
            );

            return $this;
        }
        //====================================================================//
        // Get Currently Edited Field
        if ($current = $this->current()) {
            //====================================================================//
            // Apply Template Configuration
            $current->update($fieldTemplate->getConfiguration($this->dfLanguage));
            //====================================================================//
            // Store Template Code in Options
            $current->addOption(SplFieldConstraints::TEMPLATE, $fieldTemplate::getCode());
        }

        return $this;
    }
}
