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

namespace Splash\Models\Objects;

use Splash\Components\ExtensionsManager;
use Splash\Components\FieldsManager;

/**
 * Build, Read & Write All Splash Extensions Fields
 */
trait ExtensionFieldsTrait
{
    /**
     * Build Core Fields using FieldFactory
     *
     * @return void
     */
    protected function buildSplashExtensionsFields(): void
    {
        //====================================================================//
        // Build All Extensions Fields
        ExtensionsManager::buildObjectExtensionsFields(self::getType(), $this->fieldsFactory());
        //====================================================================//
        // Register All Extensions Configurators
        ExtensionsManager::registerConfigurators(self::getType(), $this->fieldsFactory());
    }

    /**
     * Read requested Field
     *
     * @param string $key       Input List Key
     * @param string $fieldName Field Identifier / Name
     *
     * @return void
     */
    protected function getSplashExtensionsFields(string $key, string $fieldName): void
    {
        $fieldData = null;
        //====================================================================//
        // Read Field from All Extensions
        $result = ExtensionsManager::getObjectExtensionsFields(
            self::getType(),
            $this->object,
            $fieldName,
            $fieldData
        );
        //====================================================================//
        // Field Not Managed by Extensions
        if (null === $result) {
            return;
        }
        //====================================================================//
        // Field Managed by Extensions
        if ($listName = FieldsManager::listName($fieldName)) {
            $this->out[$listName] = array_replace_recursive(
                $this->out[$listName] ?? array(),
                $result ? ($fieldData ?? array()) : array()
            );
        } else {
            $this->out[$fieldName] = $result ? $fieldData : null;
        }

        unset($this->in[$key]);
    }

    /**
     * Write Given Fields
     *
     * @param string $fieldName Field Identifier / Name
     * @param mixed  $fieldData Field Data
     *
     * @return void
     */
    protected function setSplashExtensionsFields(string $fieldName, $fieldData): void
    {
        //====================================================================//
        // Write Field from All Extensions
        $result = ExtensionsManager::setObjectExtensionsFields(
            self::getType(),
            $this->object,
            $fieldName,
            $fieldData
        );
        //====================================================================//
        // Field Not Managed by Extensions
        if (null === $result) {
            return;
        }
        //====================================================================//
        // Field Modified by Extensions
        if ($result) {
            $this->needUpdate();
        }
        if (isset($this->in[$fieldName])) {
            unset($this->in[$fieldName]);
        }
    }
}
