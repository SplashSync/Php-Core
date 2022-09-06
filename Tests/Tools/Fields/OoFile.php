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

namespace Splash\Tests\Tools\Fields;

use ArrayObject;

/**
 * File Field : Define Access to a File
 *
 * @example
 *
 * //====================================================================//
 * // File Structure
 * // Sample :
 * // $data["file"]["name"]           =>      File Name
 * // $data["file"]["file"]           =>      File Identifier to Require File from Server
 * // $data["file"]["filename"]       =>      File Filename with Extension
 * // $data["file"]["path"]           =>      File Full path on local system
 * // $data["file"]["md5"]            =>      File Md5 Checksum
 * // $data["file"]["size"]           =>      File Size
 * //====================================================================//
 */
class OoFile implements FieldInterface
{
    //==============================================================================
    //      Structural Data
    //==============================================================================

    /**
     * @var string
     */
    const FORMAT = 'File';

    //==============================================================================
    //      DATA VALIDATION
    //==============================================================================

    /**
     * {@inheritdoc}
     */
    public static function validate($data): ?string
    {
        //====================================================================//
        //      Verify Data is NOT Empty
        if (empty($data)) {
            return null;
        }
        //==============================================================================
        //      Verify Data is an Array
        if (!is_array($data)) {
            return 'Field Data is not an Array.';
        }
        //====================================================================//
        //      Check Contents
        if (!self::validateContents($data)) {
            return self::validateContents($data);
        }

        return null;
    }

    //==============================================================================
    //      FAKE DATA GENERATOR
    //==============================================================================

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public static function fake(array $settings): array
    {
        //====================================================================//
        // Image Faker Parameters
        $index = mt_rand(0, count($settings['Files']) - 1);
        $dir = dirname(dirname(dirname(__DIR__))).'/Resources/files/';
        $file = $settings['Files'][$index];
        $fullPath = $dir.$file;
        $name = 'Fake File '.$index;

        //====================================================================//
        // Build Image Array
        $fakeFile = array();
        //====================================================================//
        // ADD MAIN INFOS
        //====================================================================//
        // Image Name
        $fakeFile['name'] = $name;
        //====================================================================//
        // Image Filename
        $fakeFile['filename'] = $file;
        $fakeFile['file'] = $file;
        //====================================================================//
        // Image File Identifier (Full Path Here)
        $fakeFile['path'] = $fullPath;

        //====================================================================//
        // ADD COMPUTED INFOS
        //====================================================================//
        $fakeFile['md5'] = md5_file($fullPath);
        $fakeFile['size'] = filesize($fullPath);

        return $fakeFile;
    }

    //==============================================================================
    //      DATA COMPARATOR (OPTIONAL)
    //==============================================================================

    /**
     * {@inheritdoc}
     */
    public static function compare($source, $target, array $settings): bool
    {
        //====================================================================//
        // Smart Validate Arrays
        if (!is_array($source) || !is_array($target)) {
            return false;
        }
        //====================================================================//
        // Compare File CheckSum
        if (!self::compareMd5($source, $target)) {
            return self::compareMd5($source, $target);
        }
        //====================================================================//
        // Compare File Size
        if ($source['size'] != $target['size']) {
            return false;
        }

        return true;
    }

    /**
     * @param array|ArrayObject $source
     * @param array|ArrayObject $target
     *
     * @return boolean
     */
    public static function compareMd5($source, $target): bool
    {
        //====================================================================//
        // Compare File CheckSum
        if (!isset($source['md5']) || !isset($target['md5'])
            || !isset($source['size']) || !isset($target['size'])
        ) {
            return false;
        }
        if ($source['md5'] != $target['md5']) {
            return false;
        }

        return true;
    }

    /**
     * @param array $file
     *
     * @return null|string
     */
    protected static function validateContents(array $file): ?string
    {
        if (!isset($file["name"])) {
            return "File Field => 'name' is missing.";
        }
        if (!isset($file["filename"])) {
            return "File Field => 'filename' is missing.";
        }
        if (!isset($file["path"]) && !isset($file["file"])) {
            return "File Field => 'path' is missing.";
        }
        if (!isset($file["md5"])) {
            return "File Field => 'md5' is missing.";
        }
        if (!isset($file["size"])) {
            return "File Field => 'size' is missing.";
        }

        return null;
    }
}
