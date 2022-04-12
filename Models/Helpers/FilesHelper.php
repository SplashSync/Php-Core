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
 * Helper for Files Fields Management
 */
class FilesHelper
{
    //====================================================================//
    //  FILE FIELDS MANAGEMENT
    //====================================================================//

    /**
     * Build a new file field array
     *
     * @param string $name      File Name
     * @param string $fileName  Filename with Extension
     * @param string $filePath  File Full path on local system
     * @param string $publicUrl Complete Public Url of this file if available
     *
     * @return array|false Splash File Array or False
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
        // Build File Array
        $file = array();
        //====================================================================//
        // ADD MAIN INFOS
        //====================================================================//
        // Image Name
        $file["name"] = $name;
        //====================================================================//
        // Image Filename
        $file["filename"] = $fileName;
        //====================================================================//
        // Image Full Path
        $file["path"] = $fullPath;
        //====================================================================//
        // Image Publics Url
        $file["url"] = $publicUrl;
        //====================================================================//
        // ADD COMPUTED INFOS
        //====================================================================//
        $file["md5"] = md5_file($fullPath);
        $file["size"] = filesize($fullPath);
        //====================================================================//
        // Safety Check
        if (empty($file["md5"])) {
            return Splash::log()->err("Unable to read Remote File Md5");
        }

        return $file;
    }

    /**
     * Build a new streamed file field array
     *
     * @param string $name      File Name
     * @param string $fileName  Filename with Extension
     * @param string $filePath  File Full path on local system
     * @param int    $fileTtl   Lifetime of File on Sync Server (In Days)
     * @param string $publicUrl Complete Public Url of this file if available
     *
     * @return array|false Splash File Array or False
     */
    public static function stream($name, $fileName, $filePath, $fileTtl = 3, $publicUrl = null)
    {
        $file = self::encode($name, $fileName, $filePath, $publicUrl);
        if (is_array($file)) {
            $file['ttl'] = (int) $fileTtl;
        }

        return $file;
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
            return Splash::log()->err("ErrFileNoName", __FUNCTION__);
        }
        if (!is_string($absoluteUrl) || empty($absoluteUrl)) {
            return Splash::log()->err("ErrFileNoPath", __FUNCTION__);
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
        $image["md5"] = md5_file($absoluteUrl);
        $image["size"] = self::getRemoteFileSize($absoluteUrl);
        //====================================================================//
        // Safety Check
        if (empty($image["md5"])) {
            return Splash::log()->err("Unable to read Remote File Md5");
        }

        return $image;
    }

    /**
     * Uses CURL to GET Remote File Once
     *
     * @param string $fileUrl
     *
     * @return bool
     */
    public static function touchRemoteFile($fileUrl)
    {
        // Get cURL resource
        $curl = curl_init($fileUrl);
        if (!$curl) {
            return false;
        }
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $fileUrl,
            CURLOPT_USERAGENT => 'Splash cURL Agent'
        ));
        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        // Close request to clear up some resources
        curl_close($curl);

        return (false != $resp);
    }

    /**
     * Verif Parameters & Return Fullpath
     *
     * @param string $name     File Name
     * @param string $fileName File Filename with Extension
     * @param string $filePath File Full path on local system
     *
     * @return false|string File FullPath or False
     */
    protected static function verifyInputs($name, $fileName, $filePath)
    {
        //====================================================================//
        // Safety Checks - Validate Inputs
        if (!is_string($name) || empty($name)) {
            return Splash::log()->err("ErrFileNoName", __FUNCTION__);
        }
        if (!is_string($fileName) || empty($fileName)) {
            return Splash::log()->err("ErrFileNoFileName", __FUNCTION__);
        }
        if (!is_string($filePath) || empty($filePath)) {
            return Splash::log()->err("ErrFileNoPath", __FUNCTION__);
        }

        $fullPath = $filePath.$fileName;
        //====================================================================//
        // Safety Checks - Validate Image
        if (!file_exists($fullPath)) {
            return Splash::log()->err("ErrFileNoPath", __FUNCTION__, $fullPath);
        }

        return $fullPath;
    }

    /**
     * Ues CURL to detect Remote File Size
     *
     * @param string $fileUrl
     *
     * @return int
     */
    protected static function getRemoteFileSize($fileUrl)
    {
        $result = curl_init($fileUrl);
        if (!$result) {
            return 0;
        }

        curl_setopt($result, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($result, CURLOPT_HEADER, true);
        curl_setopt($result, CURLOPT_NOBODY, true);

        curl_exec($result);
        $fileSize = curl_getinfo($result, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
        curl_close($result);

        return (int) $fileSize;
    }
}
