<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2021 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Models;

use ArrayObject;
use Splash\Core\SplashCore as Splash;

/**
 * Abstract Configurator
 * Base Functionnal Class for Configuration Managers
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
    public function getParameters()
    {
        //====================================================================//
        // Load Parameters from Configurator
        $customParameters = $this->getConfigurationValue("parameters");
        //====================================================================//
        // Custom Parameters wher Found
        if (is_array($customParameters) && !empty($customParameters)) {
            //====================================================================//
            // Remove Unsecure Parameters
            self::sercureParameters($customParameters);
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
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function isDisabled($objectType, $isDisabled = false)
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
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function overrideDescription($objectType, $description)
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
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function overrideFields($objectType, $fields)
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
            if (!isset($overrides[$field->id])) {
                continue;
            }
            //====================================================================//
            // Update Field Definition
            $fields[$index] = self::updateField($field, $overrides[$field->id]);
        }

        return $fields;
    }

    //====================================================================//
    // PRIVATE FUNCTIONS
    //====================================================================//

    /**
     * Read Configuration Value
     *
     * @param string      $key1 Main Configuration Key
     * @param null|string $key2 Second Configuration Key
     *
     * @return null|array|bool|string
     */
    private function getConfigurationValue($key1, $key2 = null)
    {
        //====================================================================//
        // Load Configuration from Configurator
        $config = $this->getConfiguration();
        //====================================================================//
        // Check Configuration is Valid
        if (!is_array($config) || empty($config)) {
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
        return isset($config[$key1][$key2]) ? $config[$key1][$key2] : null;
    }

    /**
     * Remove Potentially Unsecure Parameters from Configuration
     *
     * @param array $parameters Custom Parameters Array
     *
     * @return void
     */
    private static function sercureParameters(&$parameters)
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
     * Override a Field Definition
     *
     * @param ArrayObject $field  Original Field Definition
     * @param Array       $values Custom Values to Write
     *
     * @return ArrayObject
     */
    private static function updateField($field, $values)
    {
        Splash::log()->trace();
        //====================================================================//
        // Check New Configuration is an Array
        if (!is_array($values)) {
            return $field;
        }

        //====================================================================//
        // Field Type
        self::updateFieldStrVal($field, $values, "type");
        // Field Name
        self::updateFieldStrVal($field, $values, "name");
        // Field Description
        self::updateFieldStrVal($field, $values, "desc");
        // Field Group
        self::updateFieldStrVal($field, $values, "group");
        // Field MetaData
        self::updateFieldMeta($field, $values);

        //====================================================================//
        // Field Favorite Sync Mode
        self::updateFieldStrVal($field, $values, "syncmode");
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
        // Field is Listed Flag
        self::updateFieldBoolVal($field, $values, "inlist");
        //====================================================================//
        // Field is Logged Flag
        self::updateFieldBoolVal($field, $values, "log");

        return $field;
    }

    /**
     * Override a Field String Definition
     *
     * @param ArrayObject $field  Original Field Definition
     * @param Array       $values Custom Values to Write
     * @param string      $key    String Values Key
     *
     * @return void
     */
    private static function updateFieldStrVal(&$field, $values, $key)
    {
        if (isset($values[$key]) && is_string($values[$key])) {
            $field->{$key} = $values[$key];
        }
    }

    /**
     * Override a Field Bool Definition
     *
     * @param ArrayObject $field  Original Field Definition
     * @param Array       $values Custom Values to Write
     * @param string      $key    String Values Key
     *
     * @return void
     */
    private static function updateFieldBoolVal(&$field, $values, $key)
    {
        if (isset($values[$key]) && is_scalar($values[$key])) {
            $field->{$key} = (bool) $values[$key];
        }
    }

    /**
     * Override a Field Meta Definition
     *
     * @param ArrayObject $field  Original Field Definition
     * @param Array       $values Custom Values to Write
     *
     * @return void
     */
    private static function updateFieldMeta(&$field, $values)
    {
        // Update Field Meta ItemType
        self::updateFieldStrVal($field, $values, "itemtype");
        // Update Field Meta ItemProp
        self::updateFieldStrVal($field, $values, "itemprop");
        // Update Field Meta Tag
        if (isset($values["itemprop"]) || isset($values["itemtype"])) {
            if (is_string($field->itemprop) && is_string($field->itemtype)) {
                $field->tag = md5($field->itemprop.IDSPLIT.$field->itemtype);
            }
        }
    }
}
