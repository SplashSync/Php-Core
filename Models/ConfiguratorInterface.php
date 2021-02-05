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

/**
 * Splash Module Configurator Interface
 * Define Required Implementation for Splash Module Configurator Classes
 *
 * @author      B. Paquier <contact@splashsync.com>
 */
interface ConfiguratorInterface
{
    //====================================================================//
    // ACCESS TO LOCAL CONFIGURATION
    //====================================================================//

    /**
     * Return Local Configuration Array
     *
     * @return array
     */
    public function getConfiguration();

    //====================================================================//
    // CONFIGURE LOCAL SERVER
    //====================================================================//

    /**
     * Get Custom Server Parameters Array
     * This Event is Triggered by Splash Core during Configuration Reading
     *
     * @return array
     */
    public function getParameters();

    //====================================================================//
    // CONFIGURE LOCAL OBJECTS
    //====================================================================//

    /**
     * Override Object is Disabled Flag
     * This Event is Triggered by Abstract Object during isDisabled Flag Reading
     *
     * @param string $objectType Local Object Type Name
     * @param bool   $isDisabled Current Object Flag Value
     *
     * @return bool
     */
    public function isDisabled($objectType, $isDisabled = false);

    /**
     * Override Object is Description Array
     * This Event is Triggered by Abstract Object during Descritpion Reading
     *
     * @param string $objectType  Local Object Type Name
     * @param array  $description Current Object Description Array
     *
     * @return array
     */
    public function overrideDescription($objectType, $description);

    /**
     * Override Object Fields Array using Field Factory
     * This Event is Triggered by Object Class during Field Publish Action
     *
     * @param string $objectType Local Object Type Name
     * @param array  $fields     Current Object Fields List
     *
     * @return array
     */
    public function overrideFields($objectType, $fields);
}
