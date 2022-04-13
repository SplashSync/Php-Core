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

namespace   Splash\Models\Helpers;

use Splash\Core\SplashCore      as Splash;

/**
 * Helper for Images Fields Management
 */
class ImagesHelper extends FilesHelper
{
    //====================================================================//
    //  IMAGE FIELDS MANAGEMENT
    //====================================================================//

    /**
     * Build a new image field array
     *
     * @param string $name      Image Name
     * @param string $fileName  Image Filename with Extension
     * @param string $filePath  Image Full path on local system
     * @param string $publicUrl Complete Public Url of this image if available
     *
     * @return array|false Splash Image Array or False
     */
    public static function encode($name, $fileName, $filePath, $publicUrl = null)
    {
        //====================================================================//
        // Safety Checks - Validate Inputs
        $fullPath = self::verifyInputs($name, $fileName, $filePath);
        if (false == $fullPath) {
            return false;
        }
        //====================================================================//
        // Safety Checks - Validate is An Image
        $dimensions = getimagesize($fullPath);
        if (empty($dimensions)) {
            return Splash::log()->err("ErrImgNotAnImage", __FUNCTION__, $fullPath);
        }
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
        $image["filename"] = $fileName;
        //====================================================================//
        // Image Full Path
        $image["path"] = $fullPath;
        //====================================================================//
        // Image Publics Url
        $image["url"] = $publicUrl;
        //====================================================================//
        // ADD COMPUTED INFOS
        //====================================================================//
        // Images Informations
        $image["width"] = $dimensions[0];
        $image["height"] = $dimensions[1];
        $image["md5"] = md5_file($fullPath);
        $image["size"] = filesize($fullPath);
        //====================================================================//
        // Safety Check
        if (empty($image["md5"])) {
            return Splash::log()->err("Unable to read Remote File Md5");
        }

        return $image;
    }

    /**
     * Build a new image field array
     *
     * @param string $name        Image Name
     * @param string $absoluteUrl Image Absolute Url
     * @param string $publicUrl   Complete Public Url of this image if available
     *
     * @return array|false Splash Image Array or False
     */
    public static function encodeFromUrl($name, $absoluteUrl, $publicUrl = null)
    {
        //====================================================================//
        // Safety Checks - Validate Inputs
        if (!is_string($name) || empty($name)) {
            return Splash::log()->err("ErrImgNoName", __FUNCTION__);
        }
        if (!is_string($absoluteUrl) || empty($absoluteUrl)) {
            return Splash::log()->err("ErrImgNoPath", __FUNCTION__);
        }
        //====================================================================//
        // Safety Checks - Validate Image
        $dimensions = getimagesize($absoluteUrl);
        if (empty($dimensions)) {
            return Splash::log()->err("ErrImgNotAnImage", __FUNCTION__, $absoluteUrl);
        }
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
        $image["filename"] = basename((string) parse_url($absoluteUrl, PHP_URL_PATH));
        //====================================================================//
        // Image Full Path
        $image["path"] = $absoluteUrl;
        //====================================================================//
        // Image Publics Url
        $image["url"] = $publicUrl;
        //====================================================================//
        // ADD COMPUTED INFOS
        //====================================================================//
        // Images Informations
        $image["width"] = $dimensions[0];
        $image["height"] = $dimensions[1];
        $image["md5"] = md5_file($absoluteUrl);
        $image["size"] = self::getRemoteFileSize($absoluteUrl);
        //====================================================================//
        // Safety Check
        if (empty($image["md5"])) {
            return Splash::log()->err("Unable to read Remote File Md5");
        }

        return $image;
    }
}
