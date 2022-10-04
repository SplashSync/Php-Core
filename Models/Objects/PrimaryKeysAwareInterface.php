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

namespace   Splash\Models\Objects;

/**
 * Splash Objects Primary Keys Aware Interface
 *
 * This is Interface add Primary Keys Features to Splash Objects
 * This is Interface is Optional
 */
interface PrimaryKeysAwareInterface
{
    //====================================================================//
    // Object Primary Keys Management
    //====================================================================//

    /**
     * Identify Object Using Primary Keys
     *
     * Splash will send a list of Fields values to Search for Objects in Database.
     *
     * If One AND Only One Object is Identified
     * this function must return its ID, else NULL
     *
     * This Feature is Optional but Highly recommended for
     * Objects alike Products(SKU), Users (Email), and more...
     *
     * @param array<string, string> $keys Primary Keys List
     *
     * @return null|string
     *
     * @since 2.0.0
     */
    public function getByPrimary(array $keys): ?string;
}
