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

namespace Splash\Configurator;

use Splash\Models\ConfiguratorInterface;

/**
 * Null Configurator to for Empty Configuration
 */
class NullConfigurator implements ConfiguratorInterface
{
    //====================================================================//
    // ACCESS TO LOCAL CONFIGURATION
    //====================================================================//

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return array();
    }

    //====================================================================//
    // CONFIGURE LOCAL SERVER
    //====================================================================//

    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
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
        return $isDisabled;
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function overrideDescription($objectType, $description)
    {
        return $description;
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function overrideFields($objectType, $fields)
    {
        return $fields;
    }
}
