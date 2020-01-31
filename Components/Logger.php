<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2020 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace   Splash\Components;

use ArrayObject;
use Exception;
use Splash\Core\SplashCore      as Splash;

/**
 * Requests Log & Debug Management Class
 *
 * This is the Core Logger Conponent for all Splash Modules.
 * It aims to store any kind of logs during normal & server request operations.
 *
 * This is the only & generic way for Splash to retreive Modules Logs
 *
 * @author      SplashSync <contact@splashsync.com>
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class Logger
{
    use \Splash\Models\Logger\HtmlExportsTrait;
    use \Splash\Models\Logger\ConsoleExporterTrait;
    use \Splash\Models\Logger\FileExporterTrait;

    const CMD_COLOR_ERR = 31;
    const CMD_COLOR_MSG = 32;
    const CMD_COLOR_WAR = 33;
    const CMD_COLOR_DEB = 97;
    const CMD_COLOR_NONE = 0;

    /**
     * Success Messages
     *
     * @var array
     */
    public $msg = array();

    /**
     * Warning Messages
     *
     * @var array
     */
    public $war = array();

    /**
     * Error Messages
     *
     * @var array
     */
    public $err = array();

    /**
     * Debug Messages
     *
     * @var array
     */
    public $deb = array();

    /**
     * Store Show Debug Messages
     *
     * @var bool
     */
    private $debug;

    /**
     * Store Show Debug Messages
     *
     * @var string
     */
    private $prefix;

    /**
     * Class Constructor
     *
     * @param bool $debug Allow Log of Debug Messages
     */
    public function __construct($debug = false)
    {
        //====================================================================//
        //  Store Debug Parameter
        $this->debug = $debug;
        //====================================================================//
        //  Define Standard Messages Prefix if Not Overiden
        $this->prefix = 'Splash Client';
    }

    //====================================================================//
    //  LOGGER CONFIGURATION
    //====================================================================//

    /**
     * Set Debug Flag & Clean buffers if needed
     *
     * @param bool $debug Use debug??
     *
     * @return true
     */
    public function setDebug($debug)
    {
        //====================================================================//
        // Change Parameter State
        $this->debug = $debug;
        //====================================================================//
        // Delete Existing Debug Messages
        if ((0 == $debug) && isset($this->debug)) {
            $this->deb = array();
        }

        return true;
    }

    /**
     * Check Debug Mode is Active
     *
     * @return bool
     */
    public function isDebugMode()
    {
        return $this->debug;
    }

    /**
     * Set Prefix String
     *
     * @param string $prefix Prefix for all Splash Messages
     *
     * @return true
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;

        return true;
    }

    //====================================================================//
    //  GENERAL MESSAGES & LOG PARSING FUNCTIONS
    //====================================================================//

    /**
     * Log WebServer Error Messages
     *
     * @param null|string $text   Input String / Key to translate
     * @param null|string $param1 Translation Parameter 1
     * @param null|string $param2 Translation Parameter 2
     * @param null|string $param3 Translation Parameter 3
     * @param null|string $param4 Translation Parameter 4
     *
     * @return false
     */
    public function err($text = null, $param1 = '', $param2 = '', $param3 = '', $param4 = '')
    {
        //====================================================================//
        // Safety Check
        if (is_null($text)) {
            return false;
        }
        //====================================================================//
        // Translate Message
        $message = Splash::trans($text, (string) $param1, (string) $param2, (string) $param3, (string) $param4);
        //====================================================================//
        // Add Message to Buffer
        $this->coreAddLog('err', $message);
        //====================================================================//
        // Add Message To Log File
        self::addLogToFile($message, 'ERROR');

        return   false;
    }

    /**
     * Log WebServer Warning Messages
     *
     * @param null|string $text   Input String / Key to translate
     * @param null|string $param1 Translation Parameter 1
     * @param null|string $param2 Translation Parameter 2
     * @param null|string $param3 Translation Parameter 3
     * @param null|string $param4 Translation Parameter 4
     *
     * @return true
     */
    public function war($text = null, $param1 = '', $param2 = '', $param3 = '', $param4 = '')
    {
        //====================================================================//
        // Safety Check
        if (is_null($text)) {
            return true;
        }
        //====================================================================//
        // Translate Message
        $message = Splash::trans($text, (string) $param1, (string) $param2, (string) $param3, (string) $param4);
        //====================================================================//
        // Add Message to Buffer
        $this->coreAddLog('war', $message);
        //====================================================================//
        // Add Message To Log File
        self::addLogToFile($message, 'WARNING');

        return   true;
    }

    /**
     * Log WebServer Commons Messages
     *
     * @param null|string $text   Input String / Key to translate
     * @param null|string $param1 Translation Parameter 1
     * @param null|string $param2 Translation Parameter 2
     * @param null|string $param3 Translation Parameter 3
     * @param null|string $param4 Translation Parameter 4
     *
     * @return true
     */
    public function msg($text = null, $param1 = '', $param2 = '', $param3 = '', $param4 = '')
    {
        //====================================================================//
        // Safety Check
        if (is_null($text)) {
            return true;
        }
        //====================================================================//
        // Translate Message
        $message = Splash::trans($text, (string) $param1, (string) $param2, (string) $param3, (string) $param4);
        //====================================================================//
        // Add Message to Buffer
        $this->coreAddLog('msg', $message);
        //====================================================================//
        // Add Message To Log File
        self::addLogToFile($message, 'MESSAGE');

        return   true;
    }

    /**
     * Log WebServer Debug Messages
     *
     * @param null|string $text   Input String / Key to translate
     * @param null|string $param1 Translation Parameter 1
     * @param null|string $param2 Translation Parameter 2
     * @param null|string $param3 Translation Parameter 3
     * @param null|string $param4 Translation Parameter 4
     *
     * @return true
     */
    public function deb($text = null, $param1 = '', $param2 = '', $param3 = '', $param4 = '')
    {
        //====================================================================//
        // Safety Check
        if (is_null($text) || !isset($this->debug) || !$this->debug) {
            return   true;
        }
        //====================================================================//
        // Translate Message
        $message = Splash::trans($text, (string) $param1, (string) $param2, (string) $param3, (string) $param4);
        //====================================================================//
        // Add Message to Buffer
        $this->coreAddLog('deb', $message);
        //====================================================================//
        // Add Message To Log File
        self::addLogToFile($message, 'DEBUG');

        return   true;
    }

    //====================================================================//
    //  SPECIAL MESSAGES & LOG PARSING FUNCTIONS
    //====================================================================//

    /**
     * Log Tracable WebServer Error Message (with Class & Function)
     *
     * @param string $text Input String / Key to translate
     *
     * @return false
     */
    public function errTrace($text)
    {
        //====================================================================//
        // Build Error Trace
        $trace = (new Exception())->getTrace()[1];

        //====================================================================//
        // Push Error to Log
        return  self::err("ErrLocalTpl", $trace["class"], $trace["function"], $text);
    }

    /**
     * Log Tracable WebServer Warning Message (with Class & Function)
     *
     * @param string $text Input String / Key to translate
     *
     * @return true
     */
    public function warTrace($text)
    {
        //====================================================================//
        // Build Warning Trace
        $trace = (new Exception())->getTrace()[1];

        //====================================================================//
        // Push Warning to Log
        return  self::war("WarLocalTpl", $trace["class"], $trace["function"], $text);
    }

    /**
     * Log Given object Class Name as Warning
     *
     * @param mixed $object Input Object to get Classname
     *
     * @return true
     */
    public function warClass($object)
    {
        //====================================================================//
        // Get Object Class Name
        $text = is_null($object) ?  "NULL" : get_class($object);
        //====================================================================//
        // Build Warning Trace
        $trace = (new Exception())->getTrace()[1];
        //====================================================================//
        // Push Warning to Log
        return  self::war("WarLocalClass", $trace["class"], $trace["function"], $text);
    }

    /**
     * Log Call Stack as Warning
     *
     * @return true
     */
    public function warStack()
    {
        //====================================================================//
        // Build Warning Trace
        $exc = new Exception();
        $trace = $exc->getTrace()[1];
        //====================================================================//
        // Push Main Warning to Log
        self::war("WarLocalTrace", $trace["class"], $trace["function"], "");
        //====================================================================//
        // Push Full Stack Trace to Log
        $stack = explode("#", $exc->getTraceAsString());
        foreach ($stack as $stackLine) {
            if (!empty($stackLine)) {
                self::war($stackLine);
            }
        }

        return  true;
    }

    /**
     * Read & Returns var_dump() of a variable in a debug message
     *
     * @param string $txt Any text to display before dump
     * @param mixed  $var Any Object to dump
     *
     * @return bool
     */
    public function ddd($txt, $var)
    {
        return $this->deb($txt.$this->getVarDump($var));
    }

    /**
     * Read & Returns print_r() of a variable in a warning message
     *
     * @param string $txt Any text to display before dump
     * @param mixed  $var Any Object to dump
     *
     * @return bool
     */
    public function www($txt, $var)
    {
        return $this->war($txt.'<PRE>'.print_r($var, true).'</PRE>');
    }

    /**
     * Log a Debug Message With Trace from Stack
     *
     * @return bool
     */
    public function trace()
    {
        //====================================================================//
        // Safety Check
        if (!isset($this->debug) || !$this->debug) {
            return true;
        }
        //====================================================================//
        // Build Error Trace
        $trace = (new Exception())->getTrace()[1];
        //====================================================================//
        //  Load Translation File
        Splash::translator()->load('main');
        //====================================================================//
        // Push Trace to Log
        return  self::deb("DebTraceMsg", $trace["class"], $trace["function"]);
    }

    //====================================================================//
    //  USER ACTIONS ON LOGGER
    //====================================================================//

    /**
     * Clean WebServer Class Logs Messages
     *
     * @return bool
     */
    public function cleanLog()
    {
        if (isset($this->err)) {
            $this->err = array();
        }
        if (isset($this->war)) {
            $this->war = array();
        }
        if (isset($this->msg)) {
            $this->msg = array();
        }
        if (isset($this->deb)) {
            $this->deb = array();
        }
        $this->deb('Log Messages Buffer Cleaned');

        return   true;
    }

    /**
     * Return All WebServer current Log WebServer in an arrayobject variable
     *
     * @param bool $clean True if messages needs to be cleaned after reading
     *
     * @return ArrayObject
     */
    public function getRawLog($clean = false)
    {
        $raw = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        if ($this->err) {
            $raw->err = $this->err;
        }
        if ($this->war) {
            $raw->war = $this->war;
        }
        if ($this->msg) {
            $raw->msg = $this->msg;
        }
        if ($this->deb) {
            $raw->deb = $this->deb;
        }
        if ($clean) {
            $this->cleanLog();
        }

        return $raw;
    }

    /**
     * Merge All Messages from a second class with current class
     *
     * @param array|ArrayObject $logs Second logging array
     *
     * @return bool
     */
    public function merge($logs)
    {
        if (!empty($logs->msg)) {
            $this->mergeCore('msg', $logs->msg);
            $this->addLogBlockToFile($logs->msg, 'MESSAGE');
        }

        if (!empty($logs->err)) {
            $this->mergeCore('err', $logs->err);
            $this->addLogBlockToFile($logs->err, 'ERROR');
        }

        if (!empty($logs->war)) {
            $this->mergeCore('war', $logs->war);
            $this->addLogBlockToFile($logs->war, 'WARNING');
        }

        if (!empty($logs->deb)) {
            $this->mergeCore('deb', $logs->deb);
            $this->addLogBlockToFile($logs->deb, 'DEBUG');
        }

        return true;
    }

    /**
     * Read & Store Outputs Buffer Contents in a warning message
     *
     * @return void
     */
    public function flushOuputBuffer()
    {
        //====================================================================//
        // Read the contents of the output buffer
        $contents = ob_get_contents();
        //====================================================================//
        // Clean (erase) the output buffer and turn off output buffering
        ob_end_clean();
        if ($contents) {
            //====================================================================//
            //  Load Translation File
            Splash::translator()->load('main');
            //====================================================================//
            //  Push Warning to Log
            $this->war('UnexOutputs', $contents);
            $this->war('UnexOutputsMsg');
        }
    }

    /**
     * Filter Logs Messages for Smart Notifications
     *
     * @param bool $msg Remove all Success Messages
     * @param bool $war Remove all Warning Messages
     * @param bool $deb Remove all Debug Messages
     *
     * @return bool
     */
    public function smartFilter($msg = true, $war = false, $deb = true)
    {
        if ($msg && isset($this->msg)) {
            $this->msg = array();
        }
        if ($war && isset($this->war)) {
            $this->war = array();
        }
        if ($deb && isset($this->deb)) {
            $this->deb = array();
        }

        return   true;
    }

    //====================================================================//
    //  PRIVATE FUNCTIONS
    //====================================================================//

    /**
     * Add Message to WebServer Log
     *
     * @param string      $type    Message Type
     * @param null|string $message Message String
     *
     * @return void
     */
    private function coreAddLog($type, $message)
    {
        //====================================================================//
        // Safety Check
        if (is_null($message)) {
            return;
        }
        //====================================================================//
        // Initialise buffer if unset
        if (!isset($this->{$type})) {
            $this->{$type} = array();
        }
        //====================================================================//
        // Build Prefix
        $prefix = empty($this->prefix) ? '' : '['.$this->prefix.'] ';
        //====================================================================//
        // Add text message to buffer
        array_push($this->{$type}, $prefix.$message);
    }

    /**
     * Merge Messages from a second class with current class
     *
     * @param string            $logType  Type of Logs to Merge
     * @param array|ArrayObject $logArray Second logging array
     *
     * @return void
     */
    private function mergeCore($logType, $logArray)
    {
        //====================================================================//
        // Fast Line
        if (empty($logArray)) {
            return;
        }
        //====================================================================//
        // Detect ArrayObjects
        if ($logArray instanceof ArrayObject) {
            $logArray = $logArray->getArrayCopy();
        }
        //====================================================================//
        // If Current Log is Empty
        if (!isset($this->{$logType})) {
            $this->{$logType} = $logArray;
        //====================================================================//
        // Really merge Logs
        } else {
            foreach ($logArray as $message) {
                array_push($this->{$logType}, $message);
            }
        }
    }

    /**
     * Read & Returns var_dump() standard php function html result
     *
     * @param mixed $var Any Object to dump
     *
     * @return string HTML display string of this object
     */
    private function getVarDump($var)
    {
        //====================================================================//
        // Safety Check
        if (empty($var)) {
            return 'Empty Object';
        }

        //====================================================================//
        // Var Dump reading
        ob_start();                     // Turn on output buffering
        var_dump($var);                 // Dumps information about a variable
        $html = ob_get_contents();         // Read the contents of the output buffer
        ob_end_clean();                 // Clean (erase) the output buffer and turn off output buffering

        //====================================================================//
        // Return Contents
        return '<PRE>'.$html.'</PRE>';
    }
}
