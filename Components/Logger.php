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

namespace   Splash\Components;

use Exception;
use Splash\Core\SplashCore      as Splash;
use Splash\Models\Logger as Traits;
use Throwable;

/**
 * Requests Log & Debug Management Class
 *
 * This is the Core Logger Component for all Splash Modules.
 * It aims to store any kind of logs during normal & server request operations.
 *
 * This is the only & generic way for Splash to retrieve Modules Logs
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class Logger
{
    use Traits\HtmlExportsTrait;
    use Traits\ConsoleExporterTrait;
    use Traits\FileExporterTrait;

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
    public array $msg = array();

    /**
     * Warning Messages
     *
     * @var array
     */
    public array $war = array();

    /**
     * Error Messages
     *
     * @var array
     */
    public array $err = array();

    /**
     * Debug Messages
     *
     * @var array
     */
    public array $deb = array();

    /**
     * Store Show Debug Messages
     *
     * @var bool
     */
    private bool $debug;

    /**
     * Standard Messages Prefix if Not Overridden
     *
     * @var string
     */
    private string $prefix = 'Splash Client';

    /**
     * Class Constructor
     *
     * @param bool $debug Allow Log of Debug Messages
     */
    public function __construct(bool $debug = false)
    {
        //====================================================================//
        //  Store Debug Parameter
        $this->debug = $debug;
    }

    //====================================================================//
    //  LOGGER CONFIGURATION
    //====================================================================//

    /**
     * Set Debug Flag & Clean buffers if needed
     *
     * @param bool $debug Use debug??
     *
     * @return self
     */
    public function setDebug(bool $debug): self
    {
        //====================================================================//
        // Change Parameter State
        $this->debug = $debug;
        //====================================================================//
        // Delete Existing Debug Messages
        if (!$debug && !empty($this->debug)) {
            $this->deb = array();
        }

        return $this;
    }

    /**
     * Check Debug Mode is Active
     *
     * @return bool
     */
    public function isDebugMode(): bool
    {
        return $this->debug;
    }

    /**
     * Set Prefix String
     *
     * @param string $prefix Prefix for all Splash Messages
     *
     * @return self
     */
    public function setPrefix(string $prefix): self
    {
        $this->prefix = $prefix;

        return $this;
    }

    //====================================================================//
    //  GENERAL MESSAGES & LOG PARSING FUNCTIONS
    //====================================================================//

    /**
     * Log WebServer Error Messages
     *
     * @param string      $text Input String / Key to translate
     * @param null|string $arg1 Translation Parameter 1
     * @param null|string $arg2 Translation Parameter 2
     * @param null|string $arg3 Translation Parameter 3
     * @param null|string $arg4 Translation Parameter 4
     *
     * @return false
     */
    public function err(
        string $text,
        ?string $arg1 = null,
        ?string $arg2 = null,
        ?string $arg3 = null,
        ?string $arg4 = null
    ): bool {
        //====================================================================//
        // Safety Check
        if (empty($text)) {
            return false;
        }
        //====================================================================//
        // Translate Message
        $message = Splash::trans($text, (string) $arg1, (string) $arg2, (string) $arg3, (string) $arg4);
        //====================================================================//
        // Add Message to Buffer
        $this->coreAddLog('err', $message);
        //====================================================================//
        // Add Message To Log File
        self::addLogToFile($message, 'ERROR');

        return false;
    }

    /**
     * Log WebServer Warning Messages
     *
     * @param string      $text Input String / Key to translate
     * @param null|string $arg1 Translation Parameter 1
     * @param null|string $arg2 Translation Parameter 2
     * @param null|string $arg3 Translation Parameter 3
     * @param null|string $arg4 Translation Parameter 4
     *
     * @return true
     */
    public function war(
        string $text,
        ?string $arg1 = null,
        ?string $arg2 = null,
        ?string $arg3 = null,
        ?string $arg4 = null
    ): bool {
        //====================================================================//
        // Safety Check
        if (empty($text)) {
            return true;
        }
        //====================================================================//
        // Translate Message
        $message = Splash::trans($text, (string) $arg1, (string) $arg2, (string) $arg3, (string) $arg4);
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
     * @param string      $text Input String / Key to translate
     * @param null|string $arg1 Translation Parameter 1
     * @param null|string $arg2 Translation Parameter 2
     * @param null|string $arg3 Translation Parameter 3
     * @param null|string $arg4 Translation Parameter 4
     *
     * @return true
     */
    public function msg(
        string $text,
        ?string $arg1 = null,
        ?string $arg2 = null,
        ?string $arg3 = null,
        ?string $arg4 = null
    ): bool {
        //====================================================================//
        // Safety Check
        if (empty($text)) {
            return true;
        }
        //====================================================================//
        // Translate Message
        $message = Splash::trans($text, (string) $arg1, (string) $arg2, (string) $arg3, (string) $arg4);
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
     * @param string      $text Input String / Key to translate
     * @param null|string $arg1 Translation Parameter 1
     * @param null|string $arg2 Translation Parameter 2
     * @param null|string $arg3 Translation Parameter 3
     * @param null|string $arg4 Translation Parameter 4
     *
     * @return true
     */
    public function deb(
        string $text,
        ?string $arg1 = null,
        ?string $arg2 = null,
        ?string $arg3 = null,
        ?string $arg4 = null
    ): bool {
        //====================================================================//
        // Safety Check
        if (empty($text) || !$this->isDebugMode()) {
            return   true;
        }
        //====================================================================//
        // Translate Message
        $message = Splash::trans($text, (string) $arg1, (string) $arg2, (string) $arg3, (string) $arg4);
        //====================================================================//
        // Add Message to Buffer
        $this->coreAddLog('deb', $message);
        //====================================================================//
        // Add Message To Log File
        self::addLogToFile($message, 'DEBUG');

        return true;
    }

    //====================================================================//
    //  SPECIAL MESSAGES & LOG PARSING FUNCTIONS
    //====================================================================//

    /**
     * Log WebServer Error Messages
     *
     * @param string      $text Input String / Key to translate
     * @param null|string $arg1 Translation Parameter 1
     * @param null|string $arg2 Translation Parameter 2
     * @param null|string $arg3 Translation Parameter 3
     * @param null|string $arg4 Translation Parameter 4
     *
     * @return null
     */
    public function errNull(
        string $text,
        ?string $arg1 = null,
        ?string $arg2 = null,
        ?string $arg3 = null,
        ?string $arg4 = null
    ) {
        $this->err($text, $arg1, $arg2, $arg3, $arg4);

        return null;
    }

    /**
     * Log Traceable WebServer Error Message (with Class & Function)
     *
     * @param string $text Input String / Key to translate
     *
     * @return false
     */
    public function errTrace(string $text): bool
    {
        //====================================================================//
        // Build Error Trace
        $trace = (new Exception())->getTrace()[1];
        //====================================================================//
        // Push Error to Log
        return  self::err("ErrLocalTpl", $trace["class"] ?? '', $trace["function"], $text);
    }

    /**
     * Log Traceable WebServer Warning Message (with Class & Function)
     *
     * @param string $text Input String / Key to translate
     *
     * @return true
     */
    public function warTrace(string $text): bool
    {
        //====================================================================//
        // Build Warning Trace
        $trace = (new Exception())->getTrace()[1];
        //====================================================================//
        // Push Warning to Log
        return  self::war("WarLocalTpl", $trace["class"] ?? '', $trace["function"], $text);
    }

    /**
     * Log Given object Class Name as Warning
     *
     * @param object $object Input Object to get Classname
     *
     * @return true
     */
    public function warClass(object $object): bool
    {
        //====================================================================//
        // Build Warning Trace
        $trace = (new Exception())->getTrace()[1];
        //====================================================================//
        // Push Warning to Log
        return  self::war("WarLocalClass", $trace["class"] ?? '', $trace["function"], get_class($object));
    }

    /**
     * Log Call Stack as Warning
     *
     * @return true
     */
    public function warStack(): bool
    {
        //====================================================================//
        // Build Warning Trace
        $exc = new Exception();
        $trace = $exc->getTrace()[1];
        //====================================================================//
        // Push Main Warning to Log
        self::war("WarLocalTrace", $trace["class"] ?? '', $trace["function"], "");
        //====================================================================//
        // Push Full Stack Trace to Log
        self::war(
            "Full Stack: ".str_replace("#", "<br />#", $exc->getTraceAsString())
        );

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
    public function ddd(string $txt, $var): bool
    {
        return $this->deb($txt.$this->getVarDump($var));
    }

    /**
     * Read & Returns print_r() of a variable in a warning message
     *
     * @param string $txt Any text to display before dump
     * @param mixed  $var Any Object to dump
     *
     * @return true
     */
    public function www(string $txt, $var): bool
    {
        return $this->war($txt.'<PRE>'.print_r($var, true).'</PRE>');
    }

    /**
     * Read & Returns print_r() of a variable in a warning message
     *
     * @param mixed $var Any Object to dump
     *
     * @return true
     */
    public function dump($var): bool
    {
        return $this->www('Dump', $var);
    }

    /**
     * Log a Debug Message With Trace from Stack
     *
     * @return bool
     */
    public function trace(): bool
    {
        //====================================================================//
        // Safety Check
        if (!$this->isDebugMode()) {
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
        return  $this->deb("DebTraceMsg", $trace["class"] ?? '', $trace["function"]);
    }

    /**
     * Build Error Report if an Exception was thrown during Request
     *
     * @param Throwable $throwable
     *
     * @return false
     */
    public function report(Throwable $throwable): bool
    {
        //====================================================================//
        // Push Error to Log
        $this->err("Fatal Error: ".$throwable->getMessage());
        //====================================================================//
        // Push Complement
        $this->err("Location: ".$throwable->getFile()." Line ".$throwable->getLine());
        //====================================================================//
        // Push Full Stack Trace
        $this->err(
            "Full Trace Stack: ".str_replace("#", "<br />#", $throwable->getTraceAsString())
        );

        return false;
    }

    //====================================================================//
    //  USER ACTIONS ON LOGGER
    //====================================================================//

    /**
     * Clean WebServer Class Logs Messages
     *
     * @return true
     */
    public function cleanLog(): bool
    {
        $this->err = array();
        $this->war = array();
        $this->msg = array();
        $this->deb = array();

        $this->deb('Log Messages Buffer Cleaned');

        return true;
    }

    /**
     * Return All WebServer current Log WebServer in an array variable
     *
     * @param bool $clean True if messages needs to be cleaned after reading
     *
     * @return array
     */
    public function getRawLog(bool $clean = false): array
    {
        $raw = array(
            "err" => $this->err,
            "war" => $this->war,
            "msg" => $this->msg,
            "deb" => $this->deb,
        );
        if ($clean) {
            $this->cleanLog();
        }

        return $raw;
    }

    /**
     * Merge All Messages from a second class with current class
     *
     * @param array $logs Second logging array
     *
     * @return bool
     */
    public function merge(array $logs): bool
    {
        if (!empty($logs["msg"])) {
            $this->mergeCore('msg', $logs["msg"]);
            $this->addLogBlockToFile($logs["msg"], 'MESSAGE');
        }

        if (!empty($logs["err"])) {
            $this->mergeCore('err', $logs["err"]);
            $this->addLogBlockToFile($logs["err"], 'ERROR');
        }

        if (!empty($logs["war"])) {
            $this->mergeCore('war', $logs["war"]);
            $this->addLogBlockToFile($logs["war"], 'WARNING');
        }

        if (!empty($logs["deb"])) {
            $this->mergeCore('deb', $logs["deb"]);
            $this->addLogBlockToFile($logs["deb"], 'DEBUG');
        }

        return true;
    }

    /**
     * Read & Store Outputs Buffer Contents in a warning message
     *
     * @return void
     */
    public function flushOutputBuffer()
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
     * @return true
     */
    public function smartFilter(bool $msg = true, bool $war = false, bool $deb = true): bool
    {
        if ($msg && !empty($this->msg)) {
            $this->msg = array();
        }
        if ($war && !empty($this->war)) {
            $this->war = array();
        }
        if ($deb && !empty($this->deb)) {
            $this->deb = array();
        }

        return true;
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
    private function coreAddLog(string $type, ?string $message): void
    {
        //====================================================================//
        // Safety Check
        if (empty($message)) {
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
        $this->{$type}[] = $prefix.$message;
    }

    /**
     * Merge Messages from a second class with current class
     *
     * @param string $logType  Type of Logs to Merge
     * @param array  $logArray Second logging array
     *
     * @return void
     */
    private function mergeCore(string $logType, array $logArray): void
    {
        //====================================================================//
        // Fast Line
        if (empty($logArray)) {
            return;
        }
        //====================================================================//
        // If Current Log is Empty
        if (!isset($this->{$logType})) {
            $this->{$logType} = $logArray;
        //====================================================================//
        // Really merge Logs
        } else {
            foreach ($logArray as $message) {
                $this->{$logType}[] = $message;
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
    private function getVarDump($var): string
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
        $html = ob_get_contents();      // Read the contents of the output buffer
        ob_end_clean();                 // Clean (erase) the output buffer and turn off output buffering
        //====================================================================//
        // Return Contents
        return '<PRE>'.$html.'</PRE>';
    }
}
