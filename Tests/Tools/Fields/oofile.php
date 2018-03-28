<?php
namespace Splash\Tests\Tools\Fields;

/**
 * @abstract    File Field : Define Access to a File
 *
 * @example
 *
//====================================================================//
// File Structure
// Sample :
// $data["file"]["name"]           =>      File Name
// $data["file"]["file"]           =>      File Identifier to Require File from Server
// $data["file"]["filename"]       =>      File Filename with Extension
// $data["file"]["path"]           =>      File Full path on local system
// $data["file"]["md5"]            =>      File Md5 Checksum
// $data["file"]["size"]           =>      File Size
//====================================================================//
 *
 */
class oofile
{
    //==============================================================================
    //      Structural Data
    //==============================================================================

    const FORMAT        =   'File';
    
    //==============================================================================
    //      DATA VALIDATION
    //==============================================================================

    /**
     * Verify given Raw Data is Valid
     *
     * @param   array   $File      Splash Image definition Array
     *
     * @return bool     True if OK, Error String if KO
     */
    public static function validate($File)
    {
        //====================================================================//
        //      Verify Data is NOT Empty
        if (empty($File)) {
            return true;
        }
        //==============================================================================
        //      Verify Data is an Array
        if (!is_array($File) && !is_a($File, "ArrayObject")) {
            return "Field Data is not an Array.";
        }

        //====================================================================//
        // Check Contents Available
        if (!array_key_exists("name", $File)) {
            return "File Field => 'name' is missing.";
        }
        if (!array_key_exists("filename", $File)) {
            return "File Field => 'filename' is missing.";
        }
        if (!array_key_exists("path", $File) && !array_key_exists("file", $File)) {
            return "File Field => 'path' is missing.";
        }
        if (!array_key_exists("md5", $File)) {
            return "File Field => 'md5' is missing.";
        }
        if (!array_key_exists("size", $File)) {
            return "File Field => 'size' is missing.";
        }
        
        return true;
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
    public static function fake($Settings)
    {
        //====================================================================//
        // Image Faker Parameters
        $i          = (int) mt_rand(0, count($Settings["Files"]) - 1);
        $Dir        = dirname(dirname(__DIR__)) . "/Resources/files/";
        $File       = $Settings["Files"][$i];
        $FullPath   = $Dir . $File;
        $Name       = "Fake File " . $i;
        
        //====================================================================//
        // Build Image Array
        $FakeFile = array();
        //====================================================================//
        // ADD MAIN INFOS
        //====================================================================//
        // Image Name
        $FakeFile["name"]          = $Name;
        //====================================================================//
        // Image Filename
        $FakeFile["filename"]      = $File;
        $FakeFile["file"]          = $File;
        //====================================================================//
        // Image File Identifier (Full Path Here)
        $FakeFile["path"]          = $FullPath;
        
        //====================================================================//
        // ADD COMPUTED INFOS
        //====================================================================//
        $FakeFile["md5"]           = md5_file($FullPath);
        $FakeFile["size"]          = filesize($FullPath);
        
        return $FakeFile;
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
    public static function compare($Source, $Target)
    {
        //====================================================================//
        // Smart Validate Arrays
        if (!is_array($Source) && !is_a($Source, "ArrayObject")) {
            return false;
        }
        if (!is_array($Target) && !is_a($Target, "ArrayObject")) {
            return false;
        }
        
        if (!array_key_exists("md5", $Source) || !array_key_exists("md5", $Target)
            || !array_key_exists("size", $Source) || !array_key_exists("size", $Target)
            ) {
            return false;
        }
        
        //====================================================================//
        // Compare File CheckSum
        if ($Source["md5"] != $Target["md5"]) {
            return false;
        }
        //====================================================================//
        // Compare File Size
        if ($Source["size"] != $Target["size"]) {
            return false;
        }
        return true;
    }
}
