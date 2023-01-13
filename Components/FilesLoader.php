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

namespace Splash\Components;

/**
 * Manage Loading of PHP Files from Folders
 */
class FilesLoader
{
    /**
     * Get List of Local Files in a Folder
     *
     * @return array<string, string>
     */
    public static function load(string $dir, ?string $ext = null, int $depth = 3): array
    {
        $filenames = array();
        //====================================================================//
        // Safety Check => Folder Exists
        if (!is_dir($dir) || !is_readable($dir)) {
            return $filenames;
        }
        //====================================================================//
        // Scan for Files in Folder
        $files = array_diff(
            scandir($dir, SCANDIR_SORT_DESCENDING) ?: array(),
            array('..', '.', 'index.php', 'index.html')
        );
        foreach ($files as $file) {
            $filenames = array_merge(
                $filenames,
                self::loadFile($dir, $file, $ext, $depth)
            );
        }

        return $filenames;
    }

    /**
     * Check if File is a Valid Path
     *
     * @param string      $dir
     * @param string      $filename
     * @param null|string $ext
     * @param int         $depth
     *
     * @return array<string, string>
     */
    private static function loadFile(string $dir, string $filename, ?string $ext, int $depth): array
    {
        $fullPath = $dir.'/'.$filename;
        $filenames = array();
        //====================================================================//
        // If Filename is a Directory
        if (is_dir($fullPath)) {
            if ($depth) {
                $filenames = array_merge(
                    $filenames,
                    self::load($fullPath, $ext, $depth - 1)
                );
            }

            return $filenames;
        }
        //====================================================================//
        // Verify Filename is a File
        if (!is_file($fullPath) || !is_readable($fullPath)) {
            return $filenames;
        }
        //====================================================================//
        // Verify Filename is a PHP File
        if ($ext && ($ext != pathinfo($fullPath, PATHINFO_EXTENSION))) {
            return $filenames;
        }
        //====================================================================//
        // Extract File Name
        $filenames[pathinfo($fullPath, PATHINFO_FILENAME)] = $fullPath;

        return $filenames;
    }
}
