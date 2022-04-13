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
    public function getConfiguration(): array
    {
        return array();
    }

    //====================================================================//
    // CONFIGURE LOCAL SERVER
    //====================================================================//

    /**
     * {@inheritdoc}
     */
    public function getParameters(): array
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
    public function isDisabled(string $objectType, bool $isDisabled = false): bool
    {
        return $isDisabled;
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function overrideDescription(string $objectType, array $description): array
    {
        return $description;
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function overrideFields(string $objectType, array $fields): array
    {
        return $fields;
    }
}
