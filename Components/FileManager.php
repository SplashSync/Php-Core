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

/**
 * Low Level Files Management Class
 */

namespace   Splash\Components;

use Splash\Core\SplashCore      as Splash;

/**
 * Splash File Manager
 * Take care of Reading & Writing Files from Local to Splash Server
 */
class FileManager
{
    /**
     * Read a file from Splash Server
     *
     * @param string $file File Identifier (Given by Splash Server)
     * @param string $md5  Local FileName
     *
     * @return null|array File contents array
     */
    public function getFile(string $file, string $md5): ?array
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();
        //====================================================================//
        // PHPUNIT Exception => Look First in Local FileSystem
        //====================================================================//
        if (Splash::isDebugMode()) {
            $filePath = $this->getDebugFullPath($file);
            if (null !== $filePath) {
                $file = $this->readFile($filePath, $md5);

                return is_array($file) ? $file : null;
            }
        }
        //====================================================================//
        // Add Task to Ws Task List
        Splash::ws()->addTask(
            SPL_F_GETFILE,
            array("file" => $file, "md5" => $md5),
            Splash::trans("MsgSchRemoteReadFile", (string) $file)
        );
        //====================================================================//
        // Execute Task
        $response = Splash::ws()->call(SPL_S_FILE);

        //====================================================================//
        // Return First Task Result
        return Splash::ws()->getNextResult($response);
    }

    /**
     * Check whether if a file exists or not
     *
     * @param string $path Full Path to Local File
     * @param string $md5  Local File Checksum
     *
     * @return null|array $infos      File information array
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
     * @remark  For all file access functions, File Checksum is used to ensure
     *          the server who send request was allowed by local server to read
     *          the specified file.
     */
    public function isFile(string $path, string $md5): ?array
    {
        //====================================================================//
        // Safety Checks
        if (!$this->isReadValidInputs($path, $md5)) {
            return null;
        }
        //====================================================================//
        // Read file Informations
        Splash::log()->deb("Splash - Get File Infos : File ".$path." exists.");
        $owner = posix_getpwuid((int) fileowner($path));

        return array(
            "owner" => $owner ? $owner["name"] : "Unknown",
            "readable" => is_readable($path),
            "writable" => is_writable($path),
            "mtime" => filemtime($path),
            "modified" => date("F d Y H:i:s.", (int) filemtime($path)),
            "md5" => md5_file($path),
            "size" => filesize($path),
        );
    }

    /**
     * Read a file from local filesystem
     *
     * @param string $path Full Path to Local File
     * @param string $md5  Local File Checksum
     *
     * @return null|array File contents array
     *
     *              File Information Array Structure
     *              $infos["filename"]  =>  File Name
     *              $infos["raw"]       =>  Raw File Contents
     *              $infos["md5"]       =>  File MD5 Checksum
     *              $infos["size"]      =>  File Size in bytes
     *
     * @remark  For all file access functions, File Checksum is used to ensure
     *          the server who send request was allowed by local server to read
     *          the specified file.
     */
    public function readFile(string $path, string $md5): ?array
    {
        //====================================================================//
        // Safety Checks
        if (!$this->isReadValidInputs($path, $md5)) {
            return null;
        }
        //====================================================================//
        // Open File
        $fileHandle = fopen($path, "rb");
        if (false == $fileHandle) {
            return Splash::log()->errNull("ErrFileRead", __FUNCTION__, $path);
        }
        //====================================================================//
        // Return file Informations
        $infos = array(
            "filename" => basename($path),
            "md5" => md5_file($path),
            "size" => filesize($path),
            "raw" => base64_encode((string) fread($fileHandle, (int) filesize($path))),
        );
        fclose($fileHandle);
        Splash::log()->deb("MsgFileRead", __FUNCTION__, basename($path));

        return $infos;
    }

    /**
     * Read a file contents from local filesystem & encode it to base64 format
     *
     * @param string $fileName Full path local FileName
     *
     * @return null|string Base64 encoded raw file
     */
    public function readFileContents(string $fileName): ?string
    {
        //====================================================================//
        // Safety Checks
        if (empty($fileName)) {
            return Splash::log()->errNull("ErrFileFileMissing");
        }
        //====================================================================//
        // Check if file exists
        if (!is_file($fileName)) {
            return Splash::log()->errNull("ErrFileNoExists", $fileName);
        }
        //====================================================================//
        // Check if file is readable
        if (!is_readable($fileName)) {
            return Splash::log()->errNull("ErrFileReadable", $fileName);
        }
        Splash::log()->deb("MsgFileRead", $fileName);

        //====================================================================//
        // Read File Contents
        return base64_encode((string) file_get_contents($fileName));
    }

    /**
     * Write a file on local filesystem
     *
     * @param string $dir  Local File Directory
     * @param string $file Local FileName
     * @param string $md5  File MD5 Checksum
     * @param string $raw  Raw File Contents (base64 Encoded)
     *
     * @return bool
     *
     * @remark      For all function used remotely, all parameters have default predefined values
     *              in order to avoid remote execution errors.
     */
    public function writeFile(string $dir, $file, string $md5, string $raw): bool
    {
        //====================================================================//
        // Safety Checks
        if (!$this->isWriteValidInputs($dir, $file, $md5, $raw)) {
            return false;
        }
        //====================================================================//
        // Assemble full Filename
        $fullPath = $dir.$file;
        //====================================================================//
        // Check if folder exists or create it
        if (!is_dir((string) $dir)) {
            mkdir((string) $dir, 0775, true);
        }
        //====================================================================//
        // Check if folder exists
        if (!is_dir((string) $dir)) {
            Splash::log()->war("ErrFileDirNoExists", __FUNCTION__, $dir);
        }
        //====================================================================//
        // Check if file exists
        if (is_file($fullPath)) {
            Splash::log()->deb("MsgFileExists", __FUNCTION__, $file);
            //====================================================================//
            // Check if file is different
            if ($md5 === md5_file($fullPath)) {
                return true;
            }
            //====================================================================//
            // Check if file is writable
            if (!is_writable($fullPath)) {
                return Splash::log()->err("ErrFileWriteable", __FUNCTION__, $file);
            }
        }
        //====================================================================//
        // Open File
        $fileHandle = fopen($fullPath, 'w');
        if (false == $fileHandle) {
            return Splash::log()->war("ErrFileOpen", __FUNCTION__, $fullPath);
        }
        //====================================================================//
        // Write file
        fwrite($fileHandle, (string)  base64_decode($raw, true));
        fclose($fileHandle);
        clearstatcache();

        //====================================================================//
        // Verify file checksum
        if ($md5 !== md5_file($fullPath)) {
            return Splash::log()->err("ErrFileWrite", __FUNCTION__, $file);
        }

        return true;
    }

    /**
     * Delete a file exists or not
     *
     * @param string $path Full Path to Local File
     * @param string $md5  Local File Checksum
     *
     * @return bool
     *
     * @remark      For all file access functions, File Checksum is used to ensure
     *              the server who send request was allowed by local server to read
     *              the specified file.
     */
    public function deleteFile(string $path, string $md5): bool
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
                return Splash::log()->deb("MsgFileDeleted", __FUNCTION__, basename($path));
            }

            return Splash::log()->err("ErrFileDeleted", __FUNCTION__, basename($path));
        }

        return true;
    }

    /**
     * Verify Read Inputs are Conform & File Present
     *
     * @param string $path Full Path to Local File
     * @param string $md5  Local File Checksum
     *
     * @return bool
     */
    private function isReadValidInputs(string $path, string $md5): bool
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
            return Splash::log()->err("ErrFileDirNoExists", __FUNCTION__, dirname($path));
        }
        //====================================================================//
        // Check if file exists
        if (!is_file($path) || !is_readable($path)) {
            return Splash::log()->err("ErrFileReadable", __FUNCTION__, $path);
        }
        //====================================================================//
        // Check File CheckSum => No Diff with Previous Error for Higher Safety Level
        if (md5_file($path) != $md5) {
            return Splash::log()->err("ErrFileNoExists", __FUNCTION__, $path);
        }

        return true;
    }

    /**
     * Verify Write Inputs are Conform to Expected
     *
     * @param string $dir
     * @param string $file
     * @param string $md5
     * @param string $raw
     *
     * @return bool
     */
    private function isWriteValidInputs(string $dir, string $file, string $md5, string $raw): bool
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
     * PHPUNIT - Check if File is Available on Local System for Debug
     *
     * @param string $file File Identifier (Given by Splash Server)
     *
     * @return null|string False if not found, else file full path
     */
    private function getDebugFullPath(string $file): ?string
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();
        //====================================================================//
        // Safety Check => Look First in Local FileSystem
        //====================================================================//
        if (!Splash::isDebugMode() || empty($file)) {
            return null;
        }
        //====================================================================//
        // Filter File Path to Remove Dir
        $filename = pathinfo($file, PATHINFO_BASENAME);
        //====================================================================//
        // Look for File in Local FileSystem
        $locations = array(
            dirname(__DIR__)."/Resources/files/",
            dirname(__DIR__)."/Resources/img/",
        );
        foreach ($locations as $location) {
            if (is_file($location.$filename)) {
                return $location.$filename;
            }
        }

        return null;
    }
}
