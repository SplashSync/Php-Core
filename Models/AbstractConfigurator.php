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

namespace Splash\Models;

use Splash\Core\SplashCore as Splash;

/**
 * Abstract Configurator
 * Base Functional Class for Configuration Managers
 */
abstract class AbstractConfigurator implements ConfiguratorInterface
{
    /**
     * List of Parameters that are Not Allowed
     * on Custom Files Configurations
     *
     * @var array
     */
    const UNSECURED_PARAMETERS = array(
        "WsIdentifier", "WsEncryptionKey", "WsHost", "WsCrypt"
    );

    /**
     * List of Description Keys that are Not Allowed
     * on Custom Files Configurations
     *
     * @var array
     */
    const UNSECURED_DESCRIPTION = array(
        "type", "fields"
    );

    //====================================================================//
    // CONFIGURE LOCAL SERVER
    //====================================================================//

    /**
     * {@inheritdoc}
     */
    public function getParameters(): array
    {
        //====================================================================//
        // Load Parameters from Configurator
        $customParameters = $this->getConfigurationValue("parameters");
        //====================================================================//
        // Custom Parameters where Found
        if (is_array($customParameters) && !empty($customParameters)) {
            //====================================================================//
            // Remove Unsecure Parameters
            self::secureParameters($customParameters);
            //====================================================================//
            // Return Custom Parameters
            return $customParameters;
        }

        return array();
    }

    //====================================================================//
    // CONFIGURE LOCAL OBJECTS
    //====================================================================//

    /**
     * Get Configurator Name
     *
     * @return string
     */
    public static function getName(): string
    {
        return basename(static::class);
    }

    /**
     * {@inheritdoc}
     */
    public function isDisabled(string $objectType, bool $isDisabled = false): bool
    {
        Splash::log()->trace();
        //====================================================================//
        // Check if Configuration is Empty
        if (!empty($this->getConfiguration())) {
            //====================================================================//
            // Load Configuration from Configurator
            $disabled = $this->getConfigurationValue($objectType, "disabled");
            //====================================================================//
            // Configuration Exists
            if (null !== $disabled) {
                return (bool) $disabled;
            }
        }

        return $isDisabled;
    }

    /**
     * {@inheritdoc}
     */
    public function overrideDescription(string $objectType, array $description): array
    {
        Splash::log()->trace();
        //====================================================================//
        // Check if Configuration is Empty
        if (empty($this->getConfiguration())) {
            return $description;
        }
        //====================================================================//
        // Load Configuration from Configurator
        $overrides = $this->getConfigurationValue($objectType);
        //====================================================================//
        // Check if Configuration is an Array
        if (!is_array($overrides)) {
            return $description;
        }
        //====================================================================//
        // Walk on Description Keys
        foreach ($overrides as $key => $value) {
            //====================================================================//
            // Check if Configuration Key is Allowed
            if (in_array($key, self::UNSECURED_DESCRIPTION, true)) {
                continue;
            }
            //====================================================================//
            // Check if Configuration Key Exists
            if (!isset($description[$key])) {
                continue;
            }
            //====================================================================//
            // Update Configuration Key
            $description[$key] = $value;
        }

        return $description;
    }

    /**
     * {@inheritdoc}
     */
    public function overrideFields(string $objectType, array $fields): array
    {
        Splash::log()->trace();
        //====================================================================//
        // Check if Configuration is Empty
        if (empty($this->getConfiguration())) {
            return $fields;
        }
        //====================================================================//
        // Load Configuration from Configurator
        $overrides = $this->getConfigurationValue($objectType, "fields");
        //====================================================================//
        // Check if Configuration is an Array
        if (!is_array($overrides)) {
            return $fields;
        }
        //====================================================================//
        // Walk on Defined Fields
        foreach ($fields as $index => $field) {
            //====================================================================//
            // Check if Configuration Key Exists
            if (!isset($overrides[$field["id"]])) {
                continue;
            }
            $fieldOverrides = $overrides[$field["id"]];
            //====================================================================//
            // Check if Field Shall be Excluded
            if (!empty($fieldOverrides["excluded"] ?? false)) {
                unset($fields[$index]);

                continue;
            }
            //====================================================================//
            // Update Field Definition
            $fields[$index] = self::updateField($field, $fieldOverrides);
        }

        return $fields;
    }

    //====================================================================//
    // PROTECTED FUNCTIONS
    //====================================================================//

    /**
     * Read Configuration Value
     *
     * @param string      $key1 Main Configuration Key
     * @param null|string $key2 Second Configuration Key
     *
     * @return null|array|bool|string
     */
    protected function getConfigurationValue(string $key1, string $key2 = null)
    {
        //====================================================================//
        // Load Configuration from Configurator
        $config = $this->getConfiguration();
        //====================================================================//
        // Check Configuration is Valid
        if (empty($config)) {
            return null;
        }
        //====================================================================//
        // Check Main Configuration Key Exists
        if (!isset($config[$key1])) {
            return null;
        }
        //====================================================================//
        // Check Second Configuration Key Required
        if (is_null($key2)) {
            return $config[$key1];
        }
        //====================================================================//
        // Check Second Configuration Key Exists
        return $config[$key1][$key2] ?? null;
    }

    /**
     * Override a Field Definition
     *
     * @param array $field  Original Field Definition
     * @param array $values Custom Values to Write
     *
     * @return array
     */
    protected static function updateField(array $field, array $values): array
    {
        Splash::log()->trace();
        //====================================================================//
        // Field Type
        self::updateFieldStrVal($field, $values, "type");
        //====================================================================//
        // Field Name
        self::updateFieldStrVal($field, $values, "name");
        //====================================================================//
        // Field Description
        self::updateFieldStrVal($field, $values, "desc");
        //====================================================================//
        // Field Group
        self::updateFieldStrVal($field, $values, "group");
        //====================================================================//
        // Field MetaData
        self::updateFieldMeta($field, $values);
        //====================================================================//
        // Field Choices
        self::updateFieldChoices($field, $values);
        //====================================================================//
        // Field Favorite Sync Mode
        self::updateFieldStrVal($field, $values, "syncmode");
        //====================================================================//
        // Field Primary Key Flag
        self::updateFieldStrVal($field, $values, "primary");
        //====================================================================//
        // Field is Required Flag
        self::updateFieldBoolVal($field, $values, "required");
        //====================================================================//
        // Field Read Allowed
        self::updateFieldBoolVal($field, $values, "read");
        //====================================================================//
        // Field Write Allowed
        self::updateFieldBoolVal($field, $values, "write");
        //====================================================================//
        // Field Indexing Flag
        self::updateFieldBoolVal($field, $values, "index");
        //====================================================================//
        // Field is Listed Flag
        self::updateFieldBoolVal($field, $values, "inlist");
        //====================================================================//
        // Field is Listed Hidden Flag
        self::updateFieldBoolVal($field, $values, "hlist");
        //====================================================================//
        // Field is Logged Flag
        self::updateFieldBoolVal($field, $values, "log");

        return $field;
    }

    //====================================================================//
    // PRIVATE FUNCTIONS
    //====================================================================//

    /**
     * Remove Potentially Unsecure Parameters from Configuration
     *
     * @param array $parameters Custom Parameters Array
     *
     * @return void
     */
    private static function secureParameters(array &$parameters): void
    {
        //====================================================================//
        // Detect Travis from SERVER CONSTANTS => Allow Unsecure for Testing
        if (!empty(Splash::input('SPLASH_TRAVIS'))) {
            return;
        }
        //====================================================================//
        // Walk on Unsecure Parameter Keys
        foreach (self::UNSECURED_PARAMETERS as $index) {
            //====================================================================//
            // Check Parameter Exists
            if (isset($parameters[$index])) {
                unset($parameters[$index]);
            }
        }
    }

    /**
     * Override a Field String Definition
     *
     * @param array  $field  Original Field Definition
     * @param array  $values Custom Values to Write
     * @param string $key    String Values Key
     *
     * @return void
     */
    private static function updateFieldStrVal(array &$field, array $values, string $key): void
    {
        if (isset($values[$key]) && is_string($values[$key])) {
            $field[$key] = $values[$key];
        }
    }

    /**
     * Override a Field Bool Definition
     *
     * @param array  $field  Original Field Definition
     * @param array  $values Custom Values to Write
     * @param string $key    String Values Key
     *
     * @return void
     */
    private static function updateFieldBoolVal(array &$field, array $values, string $key): void
    {
        if (isset($values[$key]) && is_scalar($values[$key])) {
            $field[$key] = (bool) $values[$key];
        }
    }

    /**
     * Override a Field Meta Definition
     *
     * @param array $field  Original Field Definition
     * @param array $values Custom Values to Write
     *
     * @return void
     */
    private static function updateFieldMeta(array &$field, array $values): void
    {
        // Update Field Meta ItemType
        self::updateFieldStrVal($field, $values, "itemtype");
        // Update Field Meta ItemProp
        self::updateFieldStrVal($field, $values, "itemprop");
        // Update Field Meta Tag
        if (isset($values["itemprop"]) || isset($values["itemtype"])) {
            if (is_string($field["itemprop"]) && is_string($field["itemtype"])) {
                $field["tag"] = md5($field["itemprop"].IDSPLIT.$field["itemtype"]);
            }
        }
    }

    /**
     * Override a Field Meta Definition
     *
     * @param array $field  Original Field Definition
     * @param array $values Custom Values to Write
     *
     * @return void
     */
    private static function updateFieldChoices(array &$field, array $values): void
    {
        if (!isset($values["choices"]) || !is_iterable($values["choices"])) {
            return;
        }
        $field["choices"] = array();
        foreach ($values["choices"] as $description => $value) {
            $field["choices"][] = array(
                "key" => $value,
                "value" => $description
            );
        }
    }
}
