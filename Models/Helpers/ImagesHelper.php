<?php
/**
 * This file is part of SplashSync Project.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 *  @author    Splash Sync <www.splashsync.com>
 *  @copyright 2015-2017 Splash Sync
 *  @license   GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007
 *
 **/

namespace   Splash\Models\Helpers;

use Splash\Core\SplashCore      as Splash;

/**
 * @abstract    Helper for Images Fields Management
 */
class ImagesHelper
{
    //====================================================================//
    //  IMAGE FIELDS MANAGEMENT
    //====================================================================//
    
    /**
     *  @abstract   Build a new image field array
     *
     *  @param      string      $Name           Image Name
     *  @param      string      $FileName       Image Filename with Extension
     *  @param      string      $Path           Image Full path on local system
     *  @param      string      $PublicUrl      Complete Public Url of this image if available
     *
     *  @return     array                       Splash Image Array or False
     */
    public static function encode($Name, $FileName, $Path, $PublicUrl = null)
    {
        //====================================================================//
        // Safety Checks - Validate Inputs
        if (!is_string($Name) || empty($Name)) {
            return Splash::log()->err("ErrImgNoName", __FUNCTION__);
        }
        if (!is_string($FileName) || empty($FileName)) {
            return Splash::log()->err("ErrImgNoFileName", __FUNCTION__);
        }
        if (!is_string($Path) || empty($Path)) {
            return Splash::log()->err("ErrImgNoPath", __FUNCTION__);
        }

        $FullPath = $Path . $FileName;
        //====================================================================//
        // Safety Checks - Validate Image
        if (!file_exists($FullPath)) {
            return Splash::log()->err("ErrImgNoPath", __FUNCTION__, $FullPath);
        }
        $ImageDims  = getimagesize($FullPath);
        if (empty($ImageDims)) {
            return Splash::log()->err("ErrImgNotAnImage", __FUNCTION__, $FullPath);
        }
        
        //====================================================================//
        // Build Image Array
        $Image = array();
        //====================================================================//
        // ADD MAIN INFOS
        //====================================================================//
        // Image Name
        $Image["name"]          = $Name;
        //====================================================================//
        // Image Filename
        $Image["filename"]      = $FileName;
        //====================================================================//
        // Image Full Path
        $Image["path"]          = $FullPath;
        //====================================================================//
        // Image Publics Url
        $Image["url"]           = $PublicUrl;
        //====================================================================//
        // ADD COMPUTED INFOS
        //====================================================================//
        // Images Informations
        $Image["width"]         = $ImageDims[0];
        $Image["height"]        = $ImageDims[1];
        $Image["md5"]           = md5_file($FullPath);
        $Image["size"]          = filesize($FullPath);
        
        return $Image;
    }
    
    /**
     *  @abstract   Build a new image field array
     *
     *  @param      string      $Name           Image Name
     *  @param      string      $Url            Image Absolute Url
     *  @param      string      $PublicUrl      Complete Public Url of this image if available
     *
     *  @return     array                       Splash Image Array or False
     */
    public static function encodeFromUrl($Name, $Url, $PublicUrl = null)
    {
        //====================================================================//
        // Safety Checks - Validate Inputs
        if (!is_string($Name) || empty($Name)) {
            return Splash::log()->err("ErrImgNoName", __FUNCTION__);
        }
        if (!is_string($Url) || empty($Url)) {
            return Splash::log()->err("ErrImgNoPath", __FUNCTION__);
        }

        //====================================================================//
        // Safety Checks - Validate Image
        $ImageDims  = getimagesize($Url);
        if (empty($ImageDims)) {
            return Splash::log()->err("ErrImgNotAnImage", __FUNCTION__, $Url);
        }
        
        //====================================================================//
        // Build Image Array
        $Image = array();
        //====================================================================//
        // ADD MAIN INFOS
        //====================================================================//
        // Image Name
        $Image["name"]          = $Name;
        //====================================================================//
        // Image Filename
        $Image["filename"]      = basename(parse_url($Url, PHP_URL_PATH));
        //====================================================================//
        // Image Full Path
        $Image["path"]          = $Url;
        //====================================================================//
        // Image Publics Url
        $Image["url"]           = $PublicUrl;
        //====================================================================//
        // ADD COMPUTED INFOS
        //====================================================================//
        // Images Informations
        $Image["width"]         = $ImageDims[0];
        $Image["height"]        = $ImageDims[1];
        $Image["md5"]           = md5_file($Url);
        $Image["size"]          = self::getRemoteFileSize($Url);

        return $Image;
    }
    
    private static function getRemoteFileSize($Url)
    {
        $ch = curl_init($Url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, TRUE);
        curl_setopt($ch, CURLOPT_NOBODY, TRUE);

        curl_exec($ch);
        $size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
        curl_close($ch);
        
        return (int) $size;
    }
}
