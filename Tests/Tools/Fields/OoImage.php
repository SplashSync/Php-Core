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
 * Image Field : Define access to an Image File
 *
 * @example
 *
 * //====================================================================//
 * // Image Structure
 * // Sample :
 * // $data["image"]["name"]           =>      Image Name
 * // $data["image"]["file"]           =>      Image Identifier to Require File from Server
 * // $data["image"]["filename"]       =>      Image Filename with Extension
 * // $data["image"]["path"]           =>      Image Full path on local system
 * // $data["image"]["url"]            =>      Complete Public Url, Used to display image
 * // $data["image"]["t_url"]          =>      Complete Thumb Public Url, Used to display image
 * // $data["image"]["width"]          =>      Image Width In Px
 * // $data["image"]["height"]         =>      Image Height In Px
 * // $data["image"]["md5"]            =>      Image File Md5 Checksum
 * // $data["image"]["size"]           =>      Image File Size
 * //====================================================================//
 */
class OoImage implements FieldInterface
{
    //==============================================================================
    //      Structural Data
    //==============================================================================

    /**
     * @var string
     */
    const FORMAT = 'Image';

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
        //====================================================================//
        //      Verify Data is an Array
        if (!is_array($data)) {
            return "Field Data is not an Array.";
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
        $index = mt_rand(0, count($settings["Images"]) - 1);
        $dir = dirname(dirname(dirname(__DIR__)))."/Resources/img/";
        $file = $settings["Images"][$index];
        $fullPath = $dir.$file;
        $name = "Fake Image ".$index;

        //====================================================================//
        // Build Image Array
        $image = array();
        //====================================================================//
        // ADD MAIN INFOS
        //====================================================================//
        // Image Name
        $image["name"] = $name;
        //====================================================================//
        // Image Filename
        $image["filename"] = $file;
        $image["file"] = $file;
        //====================================================================//
        // Image File Identifier (Full Path Here)
        $image["path"] = $dir.$file;
        //====================================================================//
        // Image Publics Url
        $image["url"] = filter_input(INPUT_SERVER, "HTTP_HOST").$file;

        //====================================================================//
        // ADD COMPUTED INFOS
        //====================================================================//
        // Images Informations
        if (file_exists($fullPath)) {
            $imgDims = getimagesize($fullPath);
            $image["width"] = is_array($imgDims) ? $imgDims[0] : 0;
            $image["height"] = is_array($imgDims) ? $imgDims[1] : 0;
        }
        $image["md5"] = md5_file($fullPath);
        $image["size"] = filesize($fullPath);

        return $image;
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
        if (!OoFile::compareMd5($source, $target)) {
            //====================================================================//
            // Check if Image is Marked as Potentially Resized
            if (empty($target['resized'])) {
                return OoFile::compareMd5($source, $target);
            }
            //====================================================================//
            // Compare Image Dims
            return self::compareDims($source, $target);
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
    protected static function compareDims($source, $target)
    {
        //====================================================================//
        // Safety Checks
        if (!isset($source['width']) || !isset($target['width'])
            || !isset($source['height']) || !isset($target['height'])
        ) {
            return false;
        }
        //====================================================================//
        // Compare Image Dimensions
        if ($source['width'] != $target['width']) {
            return false;
        }
        if ($source['height'] != $target['height']) {
            return false;
        }

        return true;
    }

    /**
     * @param array $image
     *
     * @return null|string
     */
    private static function validateContents(array $image): ?string
    {
        if (!isset($image["name"])) {
            return "Image Field => 'name' is missing.";
        }
        if (!isset($image["filename"])) {
            return "Image Field => 'filename' is missing.";
        }
        if (!isset($image["path"]) && !isset($image["file"])) {
            return "Image Field => 'path' is missing.";
        }
        if (!isset($image["width"])) {
            return "Image Field => 'width' is missing.";
        }
        if (!isset($image["height"])) {
            return "Image Field => 'height' is missing.";
        }
        if (!isset($image["md5"])) {
            return "Image Field => 'md5' is missing.";
        }
        if (!isset($image["size"])) {
            return "Image Field => 'size' is missing.";
        }

        return null;
    }
}
