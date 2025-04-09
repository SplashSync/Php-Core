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

namespace Splash\Core\Models\Client;

use Exception;
use ReflectionClass;
use Splash\Core\Interfaces\Local\LocalClassInterface;
use Splash\Local\Local;

/**
 * Manage All Access to Splash Local Class
 */
trait LocalClassTrait
{
    /**
     * Splash Local Core Class
     *
     * @var LocalClassInterface
     */
    protected LocalClassInterface $localCore;

    /**
     * Access Server Local Class
     *
     * @throws Exception
     */
    public static function local(): LocalClassInterface
    {
        //====================================================================//
        // Initialize Local Core Management Class
        if (isset(self::core()->localCore)) {
            return self::core()->localCore;
        }
        //====================================================================//
        // Verify Local Core Class Exist & is Valid
        if (!self::validate()->isValidLocalClass()) {
            throw new Exception('You requested access to Local Class, but it is Invalid...');
        }
        //====================================================================//
        // Initialize Class
        self::core()->localCore = new Local();
        //====================================================================//
        //  Load Translation File
        self::translator()->load('local');
        //====================================================================//
        // Load Local Includes
        self::core()->localCore->Includes();

        //====================================================================//
        // Return Local Class
        return self::core()->localCore;
    }

    /**
     * Force Server Local Class
     *
     * @param LocalClassInterface $localClass New Local Class to Use
     */
    public static function setLocalClass(LocalClassInterface $localClass): void
    {
        //====================================================================//
        // Force Local Core Management Class
        self::core()->localCore = $localClass;
    }

    /**
     * Detect Real Path of Current Module Local Class
     *
     * Local Path is the main Directory for Storing Splash Connector Classes
     */
    public static function getLocalPath(): ?string
    {
        //====================================================================//
        // Safety Check => Verify Local Class is Valid
        try {
            $local = self::local();
        } catch (Exception $ex) {
            return null;
        }
        //====================================================================//
        // Create A Reflection Class of Local Class
        $reflector = new ReflectionClass(get_class($local));

        //====================================================================//
        // Return Class Local Path
        return dirname((string) $reflector->getFileName());
    }
}
