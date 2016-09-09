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
 * @abstract    Requests Log & Debug  Management Class
 * @author      B. Paquier <contact@splashsync.com>
 */

//====================================================================//
//  CLASS DEFINITION
//====================================================================//

/**
 *	\class      SplashLog
 *	\brief      Simple Logging Functions for OsWs Classes
 */
class SplashLog 
{
    /**
     *      @abstract   Store Show Debug Messages
     *      @var        Bool
     */    
    private $debug;
    
    /**
     *      @abstract   Store Show Debug Messages
     *      @var        String
     */    
    private $prefix;
    
    /**
     *      @abstract   Success Messages
     *      @var        Array
     */    
    public  $msg;
    
    /**
     *      @abstract   Warning Messages
     *      @var        Array
     */    
    public  $war;
    
    /**
     *      @abstract   Error Messages
     *      @var        Array
     */    
    public  $err;
    
    /**
     *      @abstract   Debug Messages
     *      @var        Array
     */    
    public  $deb;
    
    /**
     *      @abstract      Class Constructor
     * 
     *      @param          bool     $debug      Allow Debug
     * 
     *      @return         boot
     */
    function __construct($debug = SPLASH_DEBUG)
    {
        
        //====================================================================//
        //  Store Debug Parameter
        $this->debug        =   $debug;
        
        //====================================================================//
        //  Define Standard Messages Prefix if Not Overiden
        $this->prefix = "Splash Client";
        
        return True;
    }
	
//====================================================================//
//  MESSAGES & LOG MANAGEMENT
//====================================================================//

   /**
     *      @abstract   Clean WebServer Class Logs Messages  
     *      @return     none
     */
   public function CleanLog() 
   {
       $this->War("CleanLog");
       if (isset($this->err)) {
            $this->err = array();
        }
        if (isset($this->war)) {
            $this->war = array();
        }
        if ( isset($this->msg)) {
            $this->msg = array();
        }
        if (isset($this->deb)) {
            $this->deb = array();
        }
        return   True;
   }   

   /**
     *      @abstract      Log WebServer Error Messages
     * 
     *      @param      string      $text       Input String / Key to translate
     *      @param      string      $param1     chaine de param1
     *      @param      string      $param2     chaine de param2
     *      @param      string      $param3     chaine de param3
     *      @param      string      $param4     chaine de param4
     *      @param      string      $param5     chaine de param5
     * 
     *      @return     False
     */
   public function Err($text, $param1='', $param2='', $param3='', $param4='', $param5='') 
   {
        //====================================================================//
        // Initialise buffer if unset 
        if (!isset($this->err)) {
            $this->err = array();
        }
        //====================================================================//
        // Add text message to buffer
        $Text = Splash::Trans($text,$param1,$param2,$param3,$param4,$param5);
        $this->err[] = $Text;
        //====================================================================//
        // Add Message To Log File
        self::Log($Text,"ERROR"); 
        return   False;
   }   
   
   /**
     *      @abstract      Log WebServer Warning Messages
     * 
     *      @param      string      $text       Input String / Key to translate
     *      @param      string      $param1     chaine de param1
     *      @param      string      $param2     chaine de param2
     *      @param      string      $param3     chaine de param3
     *      @param      string      $param4     chaine de param4
     *      @param      string      $param5     chaine de param5
     * 
     *      @return     True
    */
   public function War($text, $param1='', $param2='', $param3='', $param4='', $param5='') 
   {
        //====================================================================//
        // Initialise buffer if unset 
        if (!isset($this->war)) {
            $this->war = array();
        }
        //====================================================================//
        // Add text message to buffer
        $Text = Splash::Trans($text,$param1,$param2,$param3,$param4,$param5);
        $this->war[] = $Text;
        //====================================================================//
        // Add Message To Log File
        self::Log($Text,"WARNING"); 
        return   True;
   }     

   /**
     *      @abstract      Log WebServer Commons Messages
     * 
     *      @param      string      $text       Input String / Key to translate
     *      @param      string      $param1     chaine de param1
     *      @param      string      $param2     chaine de param2
     *      @param      string      $param3     chaine de param3
     *      @param      string      $param4     chaine de param4
     *      @param      string      $param5     chaine de param5
     * 
     *      @return     True
     */
   public function Msg($text, $param1='', $param2='', $param3='', $param4='', $param5='') 
   {
        //====================================================================//
        // Initialise buffer if unset 
        if (!isset($this->msg)) {
            $this->msg = array();
        }
        //====================================================================//
        // Add text message to buffer
        $Text = Splash::Trans($text,$param1,$param2,$param3,$param4,$param5);
        $this->msg[] = $Text;
        //====================================================================//
        // Add Message To Log File
        self::Log($Text,"MESSAGE"); 
        return   True;
   }   

   /**
     *      @abstract      Log WebServer Debug Messages
     * 
     *      @param      string      $text       Input String / Key to translate
     *      @param      string      $param1     chaine de param1
     *      @param      string      $param2     chaine de param2
     *      @param      string      $param3     chaine de param3
     *      @param      string      $param4     chaine de param4
     *      @param      string      $param5     chaine de param5
     * 
     *      @return     True
     */
   public function Deb($text, $param1='', $param2='', $param3='', $param4='', $param5='') 
   {
        if ( !isset($this->debug) || !$this->debug ) { 
            return   True;
        }
        //====================================================================//
        // Initialise buffer if unset 
        if (!isset($this->deb)) {
            $this->deb = array();
        }
        //====================================================================//
        // Add text message to buffer
        $Text = Splash::Trans($text,$param1,$param2,$param3,$param4,$param5);
        $this->deb[] = $Text;
        //====================================================================//
        // Add Message To Log File
        self::Log($Text,"DEBUG"); 
        return   True;
   }     
   

   /**
    *      @abstract    Return All WebServer current Log WebServer in Html format
    *      @return      string		All existing log messages in an human readable Html format
    */
   public function GetHtml($msgarray,$title = "", $Color = "#000000") 
   {
        $html  = '<font color="' . $Color . '">';
        
        if (count($msgarray) > 0 )  {
            //====================================================================//
            // Prepare Title as Bold
            if ( $title ) {
                $html .= '<u><b>' . $title . '</b></u></br> ';
            }
            //====================================================================//
            // Add Messages
            foreach( $msgarray as $txt) {
                $html .= $txt . "</br>";
            }
        }
        
        return $html . "</font>";
   }
   
   /**
    *      @abstract    Return All WebServer current Log WebServer in Html format
    *      @param       bool            True if messages needs to be cleaned after reading.
    *      @return      string		All existing log messages in an human readable Html format
    */
   public function GetHtmlLog( $clean = False ) 
   {
        $html  = NULL;
        //====================================================================//
        // Read All Messages as Html
        $html .= $this->GetHtml($this->err, "Errors",   "#FF3300");
        $html .= $this->GetHtml($this->war, "Warning",  "#FF9933");
        $html .= $this->GetHtml($this->msg, "Messages", "#006600");
        $html .= $this->GetHtml($this->deb, "Debug",    "#003399");
        //====================================================================//
        // Clear Log Buffer If Requiered
        if ($clean) {
            $this->CleanLog();
        }
        return $html;
   }    
   
   /**
    *      @abstract    Return All WebServer current Log WebServer in an arrayobject variable
    * 
    *      @param       bool                True if messages needs to be cleaned after reading.
    * 
    *      @return      ArrayObject         All existing log messages in an arrayobject structure
    */
   public function GetRawLog( $clean = 0 ) 
   {
        $raw = new ArrayObject();
        if ($this->err) {   $raw->err = $this->err;        }
        if ($this->war) {   $raw->war = $this->war;        }
        if ($this->msg) {   $raw->msg = $this->msg;        }
        if ($this->deb) {   $raw->deb = $this->deb;        }
        if ($clean) {
            $this->CleanLog();
        }
        return $raw;
   }      
   
   /**
    *      @abstract    Merge All Log messages from a second class with current class 
    * 
    *      @param       logs                 a second logging class structure.
    * 
    *      @return      True
    */
   public function Merge( $logs ) 
   {
        if (!empty($logs->msg)) {
            $this->MergeCore( "msg" , $logs->msg); 
            $this->LogBlock($logs->msg,"MESSAGE");
        }
        
        if (!empty($logs->err)) {
            $this->MergeCore( "err" , $logs->err); 
            $this->LogBlock($logs->err,"ERROR");
        }
        
        if (!empty($logs->war)) {
            $this->MergeCore( "war" , $logs->war); 
            $this->LogBlock($logs->war,"WARNING");
        }
        
        if (!empty($logs->deb)) {
            $this->MergeCore( "deb" , $logs->deb); 
            $this->LogBlock($logs->deb,"DEBUG");
        }
        return True;
    }         
   
   /**
    *      @abstract    Merge Messages from a second class with current class 
    * 
    *      @param       logs                 a second logging class structure.
    * 
    *      @return      True
    */
   private function MergeCore( $what , $In) 
   {
        if ( !empty($In) )     
        {
            if (is_object($In)) {
                $In = $In->getArrayCopy();
            }
            
            if (!isset($this->$what)) {
                    $this->$what = $In;
            } else {
                $this->$what = array_merge($this->$what,$In);
            }
        }
        return True;
    }  

   /**
    *      @abstract    Set Debug Flag & Clean buffers if needed
    * 
    *      @param       bool    $debug       Use debug??
    * 
    *      @return      True
    */
   public function SetDebug($debug) 
   {
        //====================================================================//
        // Change Parameter State
        $this->debug = $debug;
        //====================================================================//
        // Delete Existing Debug Messages 
        if ( ( $debug == 0 ) && isset($this->debug) )     {
            unset($this->deb);
        }
        return True;
    }         
   
   /**
    *      @abstract    Set Prefix String
    * 
    *      @param       string      $prefix     Prefix for all Splash Messages
    * 
    *      @return      True
    */    
   public function SetPrefix($prefix) 
   {
        $this->prefix = $prefix;
        
        return True;
    }       
    
//====================================================================//
//  VARIOUS TOOLS
//====================================================================//
   
    /**
     * 
     *  @abstract    Read & Returns var_dump() standard php function html result 
     * 
     *  @param      var       $var        Any Object to dump
     * 
     *  @return     string                HTML display string of this object
     * 
     */
    function GetVarDump($var)
    {
        //====================================================================//
        // Safety Check
        if (empty($var)) {
            return "Empty Object";
        }

        //====================================================================//
        // Var Dump reading
        ob_start();                     // Turn on output buffering
        var_dump($var);                 // Dumps information about a variable
        $r = ob_get_contents();         // Read the contents of the output buffer
        ob_end_clean();                 // Clean (erase) the output buffer and turn off output buffering

        //====================================================================//
        // Return Contents
        return "<PRE>" . $r . "</PRE>";
    }

    /**
     *  @abstract    Read & Returns var_dump() of a variable in a debug message 
     * 
     *  @param      string    $txt        Any text to display before dump
     *  @param      var       $var        Any Object to dump
     * 
     *  @return     string                HTML display string of this object
     */
    function ddd($txt,$var)
    {
        $this->Deb($txt . $this->GetVarDump($var));    
        return True;
    }

    /**
     *  @abstract    Read & Returns var_dump() of a variable in a warning message 
     * 
     *  @param      string    $txt        Any text to display before dump
     *  @param      var       $var        Any Object to dump
     *  @return     string                HTML display string of this object
     */
    function www($txt,$var)
    {
        $this->War($txt . "<PRE>" . print_r($var,1) . "</PRE>");    
        return True;
    }    
    
    /**
     *  @abstract    Log a debug message trace stack 
     * 
     *  @param      string    $Class      shall be __CLASS__
     *  @param      string    $Fucntion   shall be __FUNCTION__
     */
    function Trace($Class,$Fucntion)
    {
        //====================================================================//
        //  Load Translation File
        Splash::Translator()->Load("main");

        
        $this->Deb("DebTraceMsg",$Class,$Fucntion); 
    }
    
//====================================================================//
//  LOG FILE MANAGEMENT
//====================================================================//
   
    /**
     *  @abstract    Add a message to LOg File 
     * 
     *  @param      string    $txt        Message text to log
     *  @param      string    $type       Message Type
     *  @return     True
     */
    static function Log($txt,$type = "Unknown")
    {
        //====================================================================//
        // Safety Check        
        if ( Splash::Configuration()->Logging == 0 ) { return True; }
        if ( strlen(SPLASH_DIR) == 0 ) { return True; }
        //====================================================================//
        // Open Log File
        $logfile = SPLASH_DIR . "/splash.log";
        $filefd = @fopen($logfile, 'a+');
        //====================================================================//
        // Write Log File
        if ($filefd)
        {
            $message = date("Y-m-d H:i:s")." ".sprintf("%-15s", $type) . $txt;
            fwrite($filefd, $message."\n");
            fclose($filefd);
            @chmod($logfile, 0604 );
        }    
        return True;
    }   
    
    /**
     *  @abstract    Add a message to Log File 
     * 
     *  @param      array     $array      Array of Message text to log
     *  @param      string    $type       Message Type
     *  @return     True
     */
    static function LogBlock($array,$type = "Unknown")
    {
        //====================================================================//
        // Safety Check        
        if ( Splash::Configuration()->Logging == False ) { 
            return True; 
        }
        
        //====================================================================//
        // Run a Messages List
        if ( ( count($array) > 0 ) && ( !empty($array) ) ) {
            foreach ($array as $message) {
                //====================================================================//
                // Add Message To Log File
                self::Log(utf8_decode(html_entity_decode($message)),$type);
            }
        }
        return True;
    }  
}
?>