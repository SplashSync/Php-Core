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

namespace Splash\Local\Configurators;

use Splash\Models\AbstractConfigurator;

/**
 * TEMPLATE - Dummy Objects Configurator
 *
 * Use this file to create your own static objects configurators
 *  - Place it on one of your app extension folder
 *  - Preserve class namespace
 *
 * In this exemple, we override fields for "Dummy" Objects
 */
class DummyConfigurator extends AbstractConfigurator
{
    /**
     * {@inheritdoc}
     */
    public function getConfiguration(): array
    {
        return array(
            "Dummy" => array(
                //====================================================================//
                // Object Limitations
                "allow_push_created" => true,
                "allow_push_updated" => true,
                "allow_push_deleted" => true,
                //====================================================================//
                // Object Default Configuration
                "enable_push_created" => true,
                "enable_push_updated" => true,
                "enable_push_deleted" => true,
                "enable_pull_created" => true,
                "enable_pull_updated" => true,
                "enable_pull_deleted" => true,
                //====================================================================//
                // Object Fields Configuration
                "fields" => array(
                    "my_custom_field" => array(
                        //====================================================================//
                        // Change field description
                        "description" => "I changed this by Configurator",
                        //====================================================================//
                        // Mark field as Read Only
                        // "write" => false,
                        //====================================================================//
                        // Mark field as Write Only
                        // "read" => false,
                        //====================================================================//
                        // Mark field as Required for Creation
                        // "required" => true,
                        //====================================================================//
                        // Change field type
                        // "type" => SPL_T_INT,
                        //====================================================================//
                        // Change field metadata
                        // "itemtype" => "http://schema.org/ParcelDelivery", "itemprop" => "alternateName",
                    ),
                )
            )
        );
    }
}
