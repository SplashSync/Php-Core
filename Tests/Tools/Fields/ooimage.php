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
class ooimage
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
     * @param   array   $Image      Splash Image definition Array
     * 
     * @return bool     True if OK, Error String if KO
     */
    static public function validate($Image)
    {
        //==============================================================================
        //      Verify Data is an Array 
        if ( !is_array($Image) && !is_a( $Image , "ArrayObject") ) {
            return "Field Data is not an Array.";
        }

        //====================================================================//
        // Check Contents Available
        if ( !array_key_exists("name",$Image) ) {
            return "Image Field => 'name' is missing.";
        }
        if ( !array_key_exists("filename",$Image) ) {
            return "Image Field => 'filename' is missing.";
        }
        if ( !array_key_exists("path",$Image) && !array_key_exists("file",$Image) ) {
            return "Image Field => 'path' is missing.";
        }
        if ( !array_key_exists("width",$Image) ) {
            return "Image Field => 'width' is missing.";
        }
        if ( !array_key_exists("height",$Image) ) {
            return "Image Field => 'height' is missing.";
        }
        if ( !array_key_exists("md5",$Image) ) {
            return "Image Field => 'md5' is missing.";
        }
        if ( !array_key_exists("size",$Image) ) {
            return "Image Field => 'size' is missing.";
        }
        
        return True;
    }
    
    //==============================================================================
    //      FAKE DATA GENERATOR  
    //==============================================================================   

    /**
     * Generate Fake Raw Field Data for Debugger Simulations
     *
     * @param      array   $Settings   User Defined Faker Settings
     * 
     * @return mixed   
     */
    static public function fake($Settings)
    {
        //====================================================================//
        // Image Faker Parameters
        $i          = (int) mt_rand(0,count($Settings["Images"]) - 1);
        $Dir        = dirname(dirname(dirname(__DIR__))) . "/Resources/img/";  
        $File       = $Settings["Images"][$i];
        $FullPath   = $Dir . $File;
        $Name       = "Fake Image " . $i;
        
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
        $Image["filename"]      = $File;
        //====================================================================//
        // Image File Identifier (Full Path Here)
        $Image["path"]          = $Dir . $File;
        //====================================================================//
        // Image Publics Url
        $Image["url"]           = filter_input(INPUT_SERVER, "HTTP_HOST") . $File;
        
        //====================================================================//
        // ADD COMPUTED INFOS
        //====================================================================//
        // Images Informations
        if (file_exists($FullPath) ) {
            $ImageDims  = getimagesize($FullPath);
            $Image["width"]         = $ImageDims[0];
            $Image["height"]        = $ImageDims[1];
        }
        $Image["md5"]           = md5_file($FullPath);
        $Image["size"]          = filesize($FullPath);
        
        return $Image;
    }      
    
    //==============================================================================
    //      DATA COMPARATOR (OPTIONNAL)  
    //==============================================================================   
    
    /**
     * Compare Two Data Block to See if similar (Update Required)
     * 
     * !important : Target Data is always validated before compare
     * 
     * @param   mixed   $Source     Original Data Block
     * @param   mixed   $Target     New Data Block
     *
     * @return  bool                TRUE if both Data Block Are Similar
     */
    public static function compare($Source,$Target) {
        //====================================================================//
        // Smart Validate Arrays
        if ( !is_array($Source) && !is_a( $Source , "ArrayObject") ) {
            return False;
        }
        if ( !is_array($Target) && !is_a( $Target , "ArrayObject") ) {
            return False;
        }
        
        if (    !array_key_exists("md5" , $Source) || !array_key_exists("md5" , $Target) 
            || !array_key_exists("size" , $Source) || !array_key_exists("size" , $Target) 
            ) {
            return False;
        }
        
        //====================================================================//
        // Compare File CheckSum
        if ( $Source["md5"] != $Target["md5"] ) {
            return False;
        }
        //====================================================================//
        // Compare File Size
        if ( $Source["size"] != $Target["size"] ) {
            return False;
        }
        return True;
    }    
    
}
