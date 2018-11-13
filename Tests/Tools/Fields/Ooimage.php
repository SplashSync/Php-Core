<?php

namespace Splash\Tests\Tools\Fields;

/**
 * @abstract    Image Field : Define acces to an Image File
 *
 * @example
 *
//====================================================================//
// Image Structure
// Sample :
// $data["image"]["name"]           =>      Image Name
// $data["image"]["file"]           =>      Image Identifier to Require File from Server
// $data["image"]["filename"]       =>      Image Filename with Extension
// $data["image"]["path"]           =>      Image Full path on local system
// $data["image"]["url"]            =>      Complete Public Url, Used to display image
// $data["image"]["t_url"]          =>      Complete Thumb Public Url, Used to display image
// $data["image"]["width"]          =>      Image Width In Px
// $data["image"]["height"]         =>      Image Height In Px
// $data["image"]["md5"]            =>      Image File Md5 Checksum
// $data["image"]["size"]           =>      Image File Size
//====================================================================//
 *
 */
class Ooimage
{
    //==============================================================================
    //      Structural Data
    //==============================================================================

    const FORMAT        =   'Image';
    
    //==============================================================================
    //      DATA VALIDATION
    //==============================================================================

    /**
     * Verify given Raw Data is Valid
     *
     * @param   array   $data      Splash Image definition Array
     *
     * @return bool     True if OK, Error String if KO
     */
    public static function validate($data)
    {
        //====================================================================//
        //      Verify Data is NOT Empty
        if (empty($data)) {
            return true;
        }
        //====================================================================//
        //      Verify Data is an Array
        if (!is_array($data) && !is_a($data, "ArrayObject")) {
            return "Field Data is not an Array.";
        }
        //====================================================================//
        //      Check Contents
        if (!self::validateContents($data)) {
            return self::validateContents($data);
        }
        return true;
    }
    
    private static function validateContents($image)
    {
        if (!array_key_exists("name", $image)) {
            return "Image Field => 'name' is missing.";
        }
        if (!array_key_exists("filename", $image)) {
            return "Image Field => 'filename' is missing.";
        }
        if (!array_key_exists("path", $image) && !array_key_exists("file", $image)) {
            return "Image Field => 'path' is missing.";
        }
        if (!array_key_exists("width", $image)) {
            return "Image Field => 'width' is missing.";
        }
        if (!array_key_exists("height", $image)) {
            return "Image Field => 'height' is missing.";
        }
        if (!array_key_exists("md5", $image)) {
            return "Image Field => 'md5' is missing.";
        }
        if (!array_key_exists("size", $image)) {
            return "Image Field => 'size' is missing.";
        }
        
        return true;
    }
    
    //==============================================================================
    //      FAKE DATA GENERATOR
    //==============================================================================

    /**
     * Generate Fake Raw Field Data for Debugger Simulations
     *
     * @param      array   $settings   User Defined Faker Settings
     *
     * @return mixed
     */
    public static function fake($settings)
    {
        //====================================================================//
        // Image Faker Parameters
        $index          = (int) mt_rand(0, count($settings["Images"]) - 1);
        $dir        = dirname(dirname(dirname(__DIR__))) . "/Resources/img/";
        $file       = $settings["Images"][$index];
        $fullPath   = $dir . $file;
//        $Name       = "Fake Image " . substr(preg_replace('/[^A-Za-z0-9\-]/', '', utf8_encode(mt_rand())), 0, 3);
        $name       = "Fake Image " . $index;
        
        //====================================================================//
        // Build Image Array
        $image = array();
        //====================================================================//
        // ADD MAIN INFOS
        //====================================================================//
        // Image Name
        $image["name"]          = $name;
        //====================================================================//
        // Image Filename
        $image["filename"]      = $file;
        $image["file"]          = $file;
        //====================================================================//
        // Image File Identifier (Full Path Here)
        $image["path"]          = $dir . $file;
        //====================================================================//
        // Image Publics Url
        $image["url"]           = filter_input(INPUT_SERVER, "HTTP_HOST") . $file;
        
        //====================================================================//
        // ADD COMPUTED INFOS
        //====================================================================//
        // Images Informations
        if (file_exists($fullPath)) {
            $imgDims  = getimagesize($fullPath);
            $image["width"]         = $imgDims[0];
            $image["height"]        = $imgDims[1];
        }
        $image["md5"]           = md5_file($fullPath);
        $image["size"]          = filesize($fullPath);
        
        return $image;
    }
    
    //==============================================================================
    //      DATA COMPARATOR (OPTIONNAL)
    //==============================================================================
    
    /**
     * Compare Two Data Block to See if similar (Update Required)
     *
     * !important : Target Data is always validated before compare
     *
     * @param   mixed   $source     Original Data Block
     * @param   mixed   $target     New Data Block
     *
     * @return  bool                TRUE if both Data Block Are Similar
     */
    public static function compare($source, $target)
    {
        return Oofile::compare($source, $target);
    }
}
