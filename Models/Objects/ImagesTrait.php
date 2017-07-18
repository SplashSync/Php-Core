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

namespace   Splash\Models\Objects;

use Splash\Core\SplashCore      as Splash;

/**
 * @abstract    This class implements access to Images Fields Helper.
 */
trait ImagesTrait
{
    /**
     * @var Static Class Storage
     */
    private static    $ImagesHelper;
    
    /**
     *      @abstract   Get a singleton List Helper Class
     * 
     *      @return     ImagesHelper
     */    
    public static function Images()
    {
        // Helper Class Exists
        if (isset(self::$ImagesHelper)) {
            return self::$ImagesHelper;
        }
        // Initialize Class
        self::$ImagesHelper        = new ImagesHelper();  
        // Return Helper Class
        return self::$ImagesHelper;
    }  
}

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
    public static function Encode($Name, $FileName, $Path, $PublicUrl = Null)
    {
        //====================================================================//
        // Safety Checks - Validate Inputs
        if ( !is_string($Name) || empty($Name) ) {
            return Splash::Log()->Err("ErrImgNoName",__FUNCTION__);
        }
        if ( !is_string($FileName) || empty($FileName) ) {
            return Splash::Log()->Err("ErrImgNoFileName",__FUNCTION__);
        }
        if ( !is_string($Path) || empty($Path) ) {
            return Splash::Log()->Err("ErrImgNoPath",__FUNCTION__);
        }

        $FullPath = $Path . $FileName;
        //====================================================================//
        // Safety Checks - Validate Image
        if ( !file_exists($FullPath)  ) {
            return Splash::Log()->Err("ErrImgNoPath",__FUNCTION__,$FullPath);
        }
        $ImageDims  = getimagesize($FullPath);
        if ( empty($ImageDims) ) {
            return Splash::Log()->Err("ErrImgNotAnImage",__FUNCTION__,$FullPath);
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
}

?>