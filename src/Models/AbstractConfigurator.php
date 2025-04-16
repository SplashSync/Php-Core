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

namespace Splash\Core\Models;

use Splash\Core\Client\Splash;
use Splash\Core\Fields\FieldsCollection;
use Splash\Core\Interfaces\ConfiguratorInterface;

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
        // Convert Fields List to Collection
        $fieldsCollection = FieldsCollection::fromArray($fields);
        //====================================================================//
        // Apply Overrides
        $fieldsCollection = $this->overrideFieldsCollection($objectType, $fieldsCollection);

        //====================================================================//
        // Revert Fields List to Array
        return $fieldsCollection->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function overrideFieldsCollection(string $objectType, FieldsCollection $fields): FieldsCollection
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
        // Walk on Defined Overrides
        foreach ($overrides as $fieldId => $fieldOverrides) {
            //====================================================================//
            // Check if this Field Exists
            if (!$field = $fields->get($fieldId)) {
                continue;
            }

            //====================================================================//
            // Check if Field Shall be Excluded
            if (!empty($fieldOverrides["excluded"] ?? false)) {
                $fields->remove($fieldId);

                continue;
            }
            //====================================================================//
            // Update Field Definition
            $field->update($fieldOverrides);
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
        if (Splash::isCiCdMode()) {
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
}
