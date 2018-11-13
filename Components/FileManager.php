<?php
/*
 * This file is part of SplashSync Project.
 *
 * Copyright (C) Splash Sync <www.splashsync.com>
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @abstract    Low Level Files Management Class
 * @author      B. Paquier <contact@splashsync.com>
 */

namespace   Splash\Components;

use Splash\Core\SplashCore      as Splash;
use ArrayObject;

//====================================================================//
//   INCLUDES
//====================================================================//

//====================================================================//
//  CLASS DEFINITION
//====================================================================//

class FileManager
{

    /**
     *      @abstract   Read a file from Splash Server
     *
     *      @param      string      $file       File Identifier (Given by Splash Server)
     *      @param      string      $md5        Local FileName
     *
     *      @return     array       $file       False if not found, else file contents array
     */
    public function getFile($file = null, $md5 = null)
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace(__CLASS__, __FUNCTION__);
        
        //====================================================================//
        // PHPUNIT Exception => Look First in Local FileSystem
        //====================================================================//
        if (SPLASH_DEBUG) {
            $filePath   = dirname(__DIR__) . "/Resources/files/"  . $file;
            if (is_file($filePath)) {
                return $this->readFile($filePath, $md5);
            }
            $imgPath    = dirname(__DIR__) . "/Resources/img/"  . $file;
            if (is_file($imgPath)) {
                return $this->readFile($imgPath, $md5);
            }
        }
        
        //====================================================================//
        // Initiate Tasks parameters array
        $params             = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        $params->file       = $file;
        $params->md5        = $md5;
        //====================================================================//
        // Add Task to Ws Task List
        Splash::ws()->addTask(SPL_F_GETFILE, $params, Splash::trans("MsgSchRemoteReadFile", $file));
        //====================================================================//
        // Execute Task
        $response   =   Splash::ws()->call(SPL_S_FILE);
        //====================================================================//
        // Analyze NuSOAP results
        if (!isset($response->result) || ($response->result != true)) {
            return false;
        }

        //====================================================================//
        // Get Next Task Result
        if (Splash::count($response->tasks)) {
            //====================================================================//
            // Shift Task Array
            if (is_a($response->tasks, "ArrayObject")) {
                $tasks  = $response->tasks->getArrayCopy();
                $task   = array_shift($tasks);
            } else {
                $task   = array_shift($response->tasks);
            }
            //====================================================================//
            // Return Task Data
            return   isset($task->data)?$task->data:false;
        }
        return false;
    }
    
    /**
     *  @abstract   Check whether if a file exists or not
     *
     *  @param      string      $path       Full Path to Local File
     *  @param      string      $md5        Local File Checksum
     *
     *  @return     array       $infos      0 if not found, else file informations array
     *
     *              File Information Array Structure
     *              $infos["owner"]     =>  File Owner Name (Human Readable);
     *              $infos["readable"]  =>  File is Readable;
     *              $infos["writable"]  =>  File is writable;
     *              $infos["mtime"]     =>  Last Modification TimeStamp;
     *              $infos["modified"]  =>  Last Modification Date (Human Readable);
     *              $infos["md5"]       =>  File MD5 Checksum
     *              $infos["size"]      =>  File Size in bytes
     *
     *
     *  @remark     For all file access functions, File Checksumm is used to ensure
     *              the server who send request was allowed by local server to read
     *              the specified file.
     *
     */
    public function isFile($path = null, $md5 = null)
    {
        //====================================================================//
        // Safety Checks
        if (empty($path)) {
            return Splash::log()->err("ErrFileFileMissing", __FUNCTION__);
        }
        if (empty(dirname($path))) {
            return Splash::log()->err("ErrFileDirMissing", __FUNCTION__);
        }
        if (empty($md5)) {
            return Splash::log()->err("ErrFileMd5Missing", __FUNCTION__);
        }
        
        //====================================================================//
        // Check if folder exists
        if (!is_dir(dirname($path))) {
            return Splash::log()->war("ErrFileDirNoExists", __FUNCTION__, dirname($path));
        }
        //====================================================================//
        // Check if file exists
        if (!is_file($path)) {
            return Splash::log()->war("ErrFileNoExists", __FUNCTION__, $path);
        }
        //====================================================================//
        // Check File CheckSum => No Diff with Previous Error for Higher Safety Level
        if (md5_file($path) != $md5) {
            return Splash::log()->war("ErrFileNoExists", __FUNCTION__, $path);
        }
        
        //====================================================================//
        // Read file Informations
        Splash::log()->deb("Splash - Get File Infos : File ".$path." exists.");
        $infos              = array();
        $owner              = posix_getpwuid(fileowner($path));
        $infos["owner"]     = $owner["name"];
        $infos["readable"]  = is_readable($path);
        $infos["writable"]  = is_writable($path);
        $infos["mtime"]     = filemtime($path);
        $infos["modified"]  = date("F d Y H:i:s.", $infos["mtime"]);
        $infos["md5"]       = md5_file($path);
        $infos["size"]      = filesize($path);
        return $infos;
    }

    /**
     *  @abstract   Read a file from local filesystem
     *
     *  @param      string      $path       Full Path to Local File
     *  @param      string      $md5        Local File Checksum
     *
     *  @return     array       $file       0 if not found, else file contents array
     *
     *              File Information Array Structure
     *              $infos["filename"]  =>  File Name
     *              $infos["raw"]       =>  Raw File Contents
     *              $infos["md5"]       =>  File MD5 Checksum
     *              $infos["size"]      =>  File Size in bytes
     *
     *  @remark     For all file access functions, File Checksumm is used to ensure
     *              the server who send request was allowed by local server to read
     *              the specified file.
     *
     */
    public function readFile($path = null, $md5 = null)
    {
        //====================================================================//
        // Safety Checks
        if (empty($path)) {
            return Splash::log()->err("ErrFileFileMissing", __FUNCTION__);
        }
        if (empty(dirname($path))) {
            return Splash::log()->err("ErrFileDirMissing", __FUNCTION__);
        }
        if (empty($md5)) {
            return Splash::log()->err("ErrFileMd5Missing", __FUNCTION__);
        }

        //====================================================================//
        // Check if folder exists
        if (!is_dir(dirname($path))) {
            return Splash::log()->war("ErrFileDirNoExists", __FUNCTION__, dirname($path));
        }
        //====================================================================//
        // Check if file exists
        if (!is_file($path) || !is_readable($path)) {
            return Splash::log()->war("ErrFileReadable", __FUNCTION__, $path);
        }
        //====================================================================//
        // Check File CheckSum => No Diff with Previous Error for Higher Safety Level
        if (md5_file($path) != $md5) {
            return Splash::log()->war("ErrFileNoExists", __FUNCTION__, $path);
        }
        
        //====================================================================//
        // Open File
        $filehandle = fopen($path, "rb");
        if ($filehandle == false) {
            return Splash::log()->err("ErrFileRead", __FUNCTION__, $path);
        }
        
        //====================================================================//
        // Fill file Informations
        $infos = array();
        $infos["filename"]  = basename($path);
        $infos["raw"]       = base64_encode(fread($filehandle, filesize($path)));
        fclose($filehandle);
        $infos["md5"]       = md5_file($path);
        $infos["size"]      = filesize($path);
        Splash::log()->deb("MsgFileRead", __FUNCTION__, basename($path));
        return $infos;
    }

    /**
     *  @abstract   Read a file contents from local filesystem & encode it to base64 format
     *
     *  @param      string      $fileName       Full path local FileName
     *
     *  @return     string  Base64 encoded raw file
     *
     */
    public function readFileContents($fileName = null)
    {
        //====================================================================//
        // Safety Checks
        if (empty($fileName)) {
            return Splash::log()->err("ErrFileFileMissing");
        }
        //====================================================================//
        // Check if file exists
        if (!is_file($fileName)) {
            return Splash::log()->err("ErrFileNoExists", $fileName);
        }
        //====================================================================//
        // Check if file is readable
        if (!is_readable($fileName)) {
            return Splash::log()->err("ErrFileReadable", $fileName);
        }
        Splash::log()->deb("MsgFileRead", $fileName);
        //====================================================================//
        // Read File Contents
        return base64_encode(file_get_contents($fileName));
    }
    
    /**
     *  @abstract   Write a file on local filesystem
     *
     *  @param      string      $dir        Local File Directory
     *  @param      string      $file       Local FileName
     *  @param      string      $md5        File MD5 Checksum
     *  @param      string      $raw        Raw File Contents (base64 Encoded)
     *  @return     int         $result     0 if OK, 1 if OK
     *
     *  @remark     For all function used remotly, all parameters have default predefined values
     *              in order to avoid remote execution errors.
     *
     */
    public function writeFile($dir = null, $file = null, $md5 = null, $raw = null)
    {
        //====================================================================//
        // Safety Checks
        if (!$this->isWriteValidInputs($dir, $file, $md5, $raw)) {
            return false;
        }
        //====================================================================//
        // Assemble full Filename
        $fullpath = $dir.$file;
        //====================================================================//
        // Check if folder exists or create it
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        //====================================================================//
        // Check if folder exists
        if (!is_dir($dir)) {
            Splash::log()->war("ErrFileDirNoExists", __FUNCTION__, $dir);
        }
        //====================================================================//
        // Check if file exists
        if (is_file($fullpath)) {
            Splash::log()->deb("MsgFileExists", __FUNCTION__, $file);
            //====================================================================//
            // Check if file is different
            if ($md5 === md5_file($fullpath)) {
                return true;
            }
            //====================================================================//
            // Check if file is writable
            if (!is_writable($file)) {
                return Splash::log()->err("ErrFileWriteable", __FUNCTION__, $file);
            }
        }
        //====================================================================//
        // Open File
        $filehandle = fopen($fullpath, 'w');
        if ($filehandle == false) {
            return Splash::log()->war("ErrFileOpen", __FUNCTION__, $fullpath);
        }
        
        //====================================================================//
        // Write file
        fwrite($filehandle, base64_decode($raw));
        fclose($filehandle);
        
        //====================================================================//
        // Verify file checksum
        if ($md5 !== md5_file($fullpath)) {
            return Splash::log()->err("ErrFileWrite", __FUNCTION__, $file);
        }
        return true;
    }

    private function isWriteValidInputs($dir = null, $file = null, $md5 = null, $raw = null)
    {
        //====================================================================//
        // Safety Checks
        if (empty($dir)) {
            return Splash::log()->err("ErrFileDirMissing", __FUNCTION__);
        }
        if (empty($file)) {
            return Splash::log()->err("ErrFileFileMissing", __FUNCTION__);
        }
        if (empty($md5)) {
            return Splash::log()->err("ErrFileMd5Missing", __FUNCTION__);
        }
        if (empty($raw)) {
            return Splash::log()->err("ErrFileRawMissing", __FUNCTION__);
        }
        return true;
    }
    
    /**
     *  @abstract   Delete a file exists or not
     *
     *  @param      string      $path       Full Path to Local File
     *  @param      string      $md5        Local File Checksum
     *
     *  @return     bool
     *
     *  @remark     For all file access functions, File Checksumm is used to ensure
     *              the server who send request was allowed by local server to read
     *              the specified file.
     *
     */
    public function deleteFile($path = null, $md5 = null)
    {
        //====================================================================//
        // Safety Checks
        if (empty(dirname($path))) {
            return Splash::log()->err("ErrFileDirMissing", __FUNCTION__);
        }
        if (empty(basename($path))) {
            return Splash::log()->err("ErrFileFileMissing", __FUNCTION__);
        }
        if (empty($md5)) {
            return Splash::log()->err("ErrFileMd5Missing", __FUNCTION__);
        }
        //====================================================================//
        // Check if folder exists
        if (!is_dir(dirname($path))) {
            Splash::log()->war("ErrFileDirNoExists", __FUNCTION__, dirname($path));
        }
        //====================================================================//
        // Check if file exists
        if (is_file($path) && (md5_file($path) === $md5)) {
            Splash::log()->deb("MsgFileExists", __FUNCTION__, basename($path));
            //====================================================================//
            // Delete File
            if (unlink($path)) {
                return Splash::log()->msg("MsgFileDeleted", __FUNCTION__, basename($path));
            }
            return Splash::log()->err("ErrFileDeleted", __FUNCTION__, basename($path));
        }
        
        return true;
    }
}
