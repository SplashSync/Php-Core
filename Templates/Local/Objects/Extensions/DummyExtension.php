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

namespace Splash\Local\Objects\Extensions;

use Splash\Components\FieldsFactory;
use Splash\Models\ObjectExtensionInterface;

/**
 * TEMPLATE - Dummy Objects Extension
 *
 * Use Splash Objects Extension to add fields on any Synchronized Object
 *  - Place it on one of your app extension folder
 *  - Preserve class namespace
 *
 * In this exemple, we add a new fields called "my_custom_field" for "Dummy" Objects
 */
class DummyExtension implements ObjectExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function getExtendedTypes(): array
    {
        return array('Dummy');
    }

    /**
     * {@inheritDoc}
     */
    public function buildExtendedFields(string $objectType, FieldsFactory $factory): void
    {
        $factory->create(SPL_T_VARCHAR)
            ->identifier("my_custom_field")
            ->name("My Custom Field")
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function getExtendedFields(object $object, string $fieldId, &$fieldData = null): ?bool
    {
        //====================================================================//
        // READ Fields
        switch ($fieldId) {
            case 'my_custom_field':
                /** @phpstan-ignore-next-line */
                $fieldData = $object->my_custom_field;

                return true;
            default:
                return null;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function setExtendedFields(object $object, string $fieldId, $fieldData): ?bool
    {
        //====================================================================//
        // WRITE Fields
        switch ($fieldId) {
            case 'my_custom_field':
                //====================================================================//
                // Check if Data is Changed
                /** @phpstan-ignore-next-line */
                if ($fieldData != $object->my_custom_field) {
                    //====================================================================//
                    // Update Object Data
                    /** @phpstan-ignore-next-line */
                    $object->my_custom_field = $fieldData;
                    //====================================================================//
                    // Tells Splash data was Updated
                    return true;
                }
                //====================================================================//
                // Data is Unchanged
                return false;
            default:
                //====================================================================//
                // This field is NOT Managed by this Extension
                return null;
        }
    }
}
