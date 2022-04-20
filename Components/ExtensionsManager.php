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

namespace Splash\Components;

use Splash\Core\SplashCore as Splash;
use Splash\Models\Extensions;

/**
 * Manage Objects User Extensions
 */
class ExtensionsManager
{
    use Extensions\ConfiguratorsTrait;
    use Extensions\SplashObjectsTrait;
    use Extensions\ObjectExtensionsTrait;
    use Extensions\ObjectFiltersTrait;

    /**
     * List of all Extensions Files
     *
     * @var array<string, string>
     */
    private static $extensions = array();

    /**
     * Self Tests of Splash Extensions Configuration
     *
     * @return bool
     */
    public static function selfTest(): bool
    {
        //====================================================================//
        // Check Extensions Files
        if (count(self::getAll())) {
            Splash::log()->war(sprintf(
                "%d Splash Extension Files Detected",
                count(self::getAll())
            ));
        }
        //====================================================================//
        // Check Custom Local Objects
        if (count(self::getObjects())) {
            Splash::log()->war(sprintf(
                "%d Custom Objects Detected: %s",
                count(self::getObjects()),
                implode(", ", array_keys(self::getObjects()))
            ));
        }
        //====================================================================//
        // Check Local Objects Extensions
        if (count(self::getObjectExtensions())) {
            Splash::log()->war(sprintf(
                "%d Objects Extensions Detected: %s",
                count(self::getObjectExtensions()),
                implode(", ", array_keys(self::getObjectExtensions()))
            ));
        }
        //====================================================================//
        // Check Local Objects Filters
        if (count(self::getObjectFilters())) {
            Splash::log()->war(sprintf(
                "%d Objects Filters Detected: %s",
                count(self::getObjectFilters()),
                implode(", ", array_keys(self::getObjectFilters()))
            ));
        }
        //====================================================================//
        // Check Local Configurators
        if (count(self::getConfigurators())) {
            Splash::log()->war(sprintf(
                "%d Local Configurators Detected: %s",
                count(self::getConfigurators()),
                implode(", ", array_keys(self::getConfigurators()))
            ));
        }

        return true;
    }

    /**
     * Get List of All Extensions Files
     *
     * @return array<string, string>
     */
    public static function getAll(): array
    {
        self::loadExtensionsByPath();

        return self::$extensions;
    }

    /**
     * Load All Local Extensions Files
     */
    protected static function loadExtensionsByPath(): void
    {
        static $loaded;
        //====================================================================//
        // Already Done
        if (isset($loaded)) {
            return;
        }
        $loaded = true;
        //====================================================================//
        // Load File Paths
        $files = self::getExtensionsFilenames();
        //====================================================================//
        // Walk on Paths to Autoloader
        foreach ($files as $filename => $fullPath) {
            if (self::registerAutoloadFile($fullPath)) {
                unset($files[$filename]);
            }
        }
        //====================================================================//
        // Walk on Paths to Register Extensions
        foreach ($files as $filename => $fullPath) {
            //====================================================================//
            // Register File
            self::$extensions[$filename] = $fullPath;
            //====================================================================//
            // Load PHP File
            require_once $fullPath;
            //====================================================================//
            // Load Custom Splash Object
            self::registerSplashObjectFile($filename, $fullPath);
            //====================================================================//
            // Load Splash Object Extension
            self::registerObjectExtensionFile($filename, $fullPath);
            //====================================================================//
            // Load Splash Object Filter
            self::registerObjectFilterFile($filename, $fullPath);
            //====================================================================//
            // Load Splash Configurators
            self::registerConfiguratorFile($filename, $fullPath);
        }
    }

    //====================================================================//
    //  PRIVATE - LOW LEVEL METHODS
    //====================================================================//

    /**
     * Check if File as an Object Filter
     *
     * @param string $className
     * @param string $fullPath
     *
     * @return null|class-string
     */
    protected static function isClassFile(string $className, string $fullPath): ?string
    {
        //====================================================================//
        // Check if Class Exists
        if (!class_exists($className)) {
            return null;
        }
        //====================================================================//
        // Check if Class is this File
        $reflexion = new \ReflectionClass($className);

        return ($fullPath == $reflexion->getFileName()) ? $className : null;
    }

    /**
     * Get List of Local Extensions Files
     *
     * @return array<string, string>
     */
    private static function getExtensionsFilenames(): array
    {
        $filenames = array();
        //====================================================================//
        // Get List of Extensions Paths
        $extDirs = Splash::configuration()->ExtensionsPath;
        if (empty($extDirs) || (!is_scalar($extDirs) && !is_array($extDirs))) {
            return $filenames;
        }
        //====================================================================//
        // Walk on Paths
        $extDirs = is_scalar($extDirs) ? array((string) $extDirs) : $extDirs;
        foreach ($extDirs as $extDir) {
            $filenames = array_merge($filenames, FilesLoader::load($extDir, 'php'));
        }

        return $filenames;
    }

    /**
     * Check if File as a Class Autoloader
     *
     * @param string $fullPath
     *
     * @return bool
     */
    private static function registerAutoloadFile(string $fullPath): bool
    {
        //====================================================================//
        // Check if Autoloader
        if (false === strpos($fullPath, "autoload.php")) {
            return false;
        }
        require_once $fullPath;

        return true;
    }
}
