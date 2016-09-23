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
 * @abstract    Simple & Core Functions for Splash & Slaves Classes
 * @author      B. Paquier <contact@splashsync.com>
 */


//====================================================================//
//   INCLUDES 
//====================================================================//

//====================================================================//
// Include Splash Constants Definitions
require_once(SPLASH_DIR."/inc/defines.inc.php");

//====================================================================//
// Include Objects Base Class
require_once("SplashObject.php");
//====================================================================//
// Include Widgets Base Class
require_once("SplashWidget.php");

//====================================================================//
//********************************************************************//
//====================================================================//
//  SPLASH BASE CLASS
//====================================================================//
//********************************************************************//
//====================================================================//

class SplashCore
{
    /**
     * @var Static Class Storage
     */
    protected static $instance;

    /**
     *      @abstract   Class Constructor
     * 
     *      @param      bool    $debug      Force Debug Flag
     * 
     *      @return     bool    False if KO, True if OK
     */
    public function __construct($debug = SPLASH_DEBUG)
    {
        self::$instance = $this;
        
        //====================================================================//
        // Init Logger with Debug Mode
        if ($debug) {
           //====================================================================//
            // Include Class
            require_once("SplashLog.php");
            //====================================================================//
            // Initialize Log & Debug
            self::$instance->log        = new SplashLog($debug);         
        }       
        
        return True;
    }
   
//====================================================================//
//  STATIC CLASS ACCESS
//  Creation & Acces to all subclasses Instances
//====================================================================//
    
    /**
     *      @abstract   Get a singleton Core Class
     *                  Acces to all most commons Module Functions
     *      @return     Splash
     */
    public static function Core()
    {
        if (!isset(self::$instance)) {
            
            //====================================================================//
            //  Load OsWs Core Class
            self::$instance = new Splash();
            
//            //====================================================================//
//            //  Load Translation File
//            self::Tools()->Load("main");
        }
        return self::$instance;
    }
    
    /**
     *      @abstract   Get Configuration Array
     *      @return     Array
     */
    public static function Configuration()
    {
        //====================================================================//
        // Configuration Array Already Exists
        //====================================================================//
        if (isset(self::Core()->conf)) {
            return self::Core()->conf;
        }

        //====================================================================//
        // Load Module Core Configuration
        //====================================================================//

        //====================================================================//
        // Initialize Empty Configuration Array
        self::Core()->conf  =  new ArrayObject(array(),  ArrayObject::ARRAY_AS_PROPS);
        $Conf = &self::Core()->conf;

        //====================================================================//
        // Load Module Core Configuration from Definition File
        //====================================================================//
        // Local Libraries Path
        $Conf->localpath    =   dirname(dirname(__FILE__)) . SPLASH_LOCALPATH;
        //====================================================================//
        // Translations Parameters
        $Conf->DefaultLanguage      =   SPLASH_DF_LANG;
        //====================================================================//
        // WebService Core Parameters
        $Conf->WsTimout     =   SPLASH_TIMEOUT;
        $Conf->WsCrypt      =   SPLASH_CRYPT_METHOD;
        $Conf->WsEncode     =   SPLASH_ENCODE;
        $Conf->WsHost       =   "soap.splashsync.com/";
        //====================================================================//
        // Activity Logging Parameters
        $Conf->Logging      =   SPLASH_LOGGING;
        $Conf->TraceIn      =   SPLASH_TRACE_IN;
        $Conf->TraceOut     =   SPLASH_TRACE_OUT;
        $Conf->TraceTasks   =   SPLASH_TRACE_TASKS;
        
        //====================================================================//
        // Server Requests Configuration
        $Conf->server       =   array();

        //====================================================================//
        // Load Module Local Configuration (In Safe Mode)
        //====================================================================//
        if ( is_null(self::Local()) ) {
            return self::Core()->conf;
        } 
        $LocalConf  =   self::Local()->Parameters();
        //====================================================================//
        // Validate Local Parameters
        if ( self::Validate()->isValidLocalParameterArray($LocalConf) ) {
            
            //====================================================================//
            // Import Local Parameters
            foreach ($LocalConf as $key => $value) {
                $Conf->$key =   $value;
            }
            
        }
        return self::Core()->conf;
    }

    /**
     *      @abstract   Get a singleton Log Class
     *                  Acces to Module Logging Functions
     *      @return     SplashLog
     */
    public static function Log()
    {
        if (!isset(self::Core()->log)) {
            //====================================================================//
            // Include Class
            require_once("SplashLog.php");
            //====================================================================//
            // Initialize Log & Debug
            self::Core()->log        = new SplashLog();   
            //====================================================================//
            //  Define Standard Messages Prefix if Not Overiden
            if ( isset(Splash::Configuration()->localname) ) {
                self::Core()->log->SetPrefix(Splash::Configuration()->localname);
            }           
            
        }
        return self::Core()->log;
    } 
    
    /**
     *      @abstract   Get a singleton NuSOAP Server Class
     *                  Acces to all server side Module Functions
     *      @return     soap_server
     */
    public static function Server()
    {
        if (!isset(self::Core()->Server)) {
            //====================================================================//
            // NUSOAP SERVER INITIALISATION
            //====================================================================//
            //====================================================================//
            // Include ClassNuSOAP WebService Classes
            // NuSOAP WebService Classes
            require_once(SPLASH_DIR."/inc/nusoap/nusoap.php");
            //====================================================================//
            // Initialize NuSOAP Server Class
            self::Core()->Server           = new soap_server();
        }
        return self::Core()->Server;
    } 
    
    /**
     *      @abstract   Get a singleton WebService Class
     *                  Acces to NuSOAP WebService Communication Functions
     *      @return     SplashWs
     */
    public static function Ws()
    {
        if (!isset(self::Core()->ws)) {
            //====================================================================//
            // Include Class
            require_once("SplashWs.php");
            //====================================================================//
            // WEBSERVICE INITIALISATION
            //====================================================================//
            // Initialize SOAP WebServices Class
            self::Core()->ws           = new SplashWs();
            //====================================================================//
            // Initialize WebService Configuration Array
            self::Core()->ws->Setup();
            //====================================================================//
            //  Load Translation File
            self::Translator()->Load("ws");
        }
        return self::Core()->ws;
    } 
    
    /**
     *      @abstract   Get a singleton Router Class
     *                  Acces to Server Tasking Management Functions
     *      @return     SplashTasks
     */
    public static function Router()
    {
        if (isset(self::Core()->router)) {
            return self::Core()->router;
        }
        
        //====================================================================//
        // Include Class
        require_once("SplashRouter.php");
        //====================================================================//
        // Initialize Tasks List
        self::Core()->router        = new SplashRouter();
        
        return self::Core()->router;
    } 
    
    /**
     *      @abstract   Get a singleton File Class
     *                  Acces to File Management Functions
     *      @return     SplashFile
     */
    public static function File()
    {
        if (!isset(self::Core()->file)) {
            
            //====================================================================//
            // Include Class
            require_once("SplashFile.php");
            
            //====================================================================//
            // Initialize Tasks List
            self::Core()->file        = new SplashFile();
            
            //====================================================================//
            //  Load Translation File
            self::Translator()->Load("file");
            
        }
        return self::Core()->file;
    } 
    
    /**
     *      @abstract   Get a singleton Validate Class
     * 
     *                  Acces to Module Validation Functions
     * 
     *      @return     SplashValidate
     */
    public static function Validate()
    {
        if ( isset(self::Core()->valid) ) {
            return self::Core()->valid;
        }
        
        //====================================================================//
        // Include Class
        require_once("SplashValidate.php");
            
        //====================================================================//
        // Initialize Tasks List
        self::Core()->valid        = new SplashValidate();
            
        //====================================================================//
        //  Load Translation File
        self::Translator()->Load("validate");
            
        return self::Core()->valid;
    }     
    
    /**
     *      @abstract   Get a singleton Xml Parser Class
     * 
     *                  Acces to Module Xml Parser Functions
     * 
     *      @return     SplashXml
     */
    public static function Xml()
    {
        if ( isset(self::Core()->xml) ) {
            return self::Core()->xml;
        }
        
        //====================================================================//
        // Include Class
        require_once("SplashXml.php");
            
        //====================================================================//
        // Initialize Tasks List
        self::Core()->xml        = new SplashXml();
            
        return self::Core()->xml;
    }     

    /**
     *      @abstract   Get a singleton Translator Class
     *                  Acces to Translation Functions
     *      @return     SplashTranslator
     */
    public static function Translator()
    {
        if (!isset(self::Core()->translator)) {
            
            //====================================================================//
            // Include Class
            require_once("SplashTranslator.php");
            
            //====================================================================//
            // Initialize Tasks List
            self::Core()->translator        = new SplashTranslator();
            
        }
        
        return self::Core()->translator;
    } 
    
    /**
     *      @abstract   Acces Server Local Class
     * 
     *      @return     SplashLocal
     */
    public static function Local()
    {
        //====================================================================//
        // Initialize Local Core Management Class
        if ( isset(self::Core()->localcore) ) {
            return self::Core()->localcore;        
        }

        //====================================================================//
        // Verify Local Core Class Exist
        if ( self::Validate()->isValidLocalClass() == True){
            
            //====================================================================//
            // Initialize Class
            self::Core()->localcore        = new SplashLocal();  

            //====================================================================//
            //  Load Translation File
            self::Translator()->Load("local");
            
            //====================================================================//
            // Load Local Includes
            self::Core()->localcore->Includes();  

            return self::Core()->localcore;        
        }        

        return Null;
    }
        
    /**
     *      @abstract   Get Specific Object Class
     *                  This function is a router for all local object classes & functions
     * 
     *      @params     $type       Specify Object Class Name
     * 
     *      @return     OsWs_LinkerCore
     */
    public static function Object($ObjectType)
    {
        //====================================================================//
        // First Access to Local Objects
        if (!isset(self::Core()->objects)) {
            //====================================================================//
            // Initialize Local Objects Class Array
            self::Core()->objects = Array();
        }
        
        //====================================================================//
        // Check in Cache
        if (array_key_exists( $ObjectType, self::Core()->objects ) ) {
            return self::Core()->objects[$ObjectType];
        }

        //====================================================================//
        // Verify if Object Class is Valid
        if ( !Splash::Validate()->isValidObject($ObjectType) ) {
            return Null;
        }
        
        //====================================================================//
        // Check if Object Manager is Overriden
        if ( Splash::Validate()->isValidLocalOverride("Object")) {
            //====================================================================//
            // Initialize Local Object Manager
            self::Core()->objects[$ObjectType] = Splash::Local()->Object($ObjectType);
        } else {
            //====================================================================//
            // Initialize Standard Class
            $ClassName = SPLASH_CLASS_PREFIX . $ObjectType;
            self::Core()->objects[$ObjectType]        = new $ClassName();
        }
        
        
        //====================================================================//
        //  Load Translation File
        self::Translator()->Load("objects");
            
        return self::Core()->objects[$ObjectType];
    } 
    
    /**
     *      @abstract   Get Specific Widget Class
     *                  This function is a router for all local widgets classes & functions
     * 
     *      @params     $WidgetType         Specify Widget Class Name
     * 
     *      @return     SplashWidget
     */
    public static function Widget($WidgetType)
    {
        //====================================================================//
        // First Access to Local Objects
        if (!isset(self::Core()->widgets)) {
            //====================================================================//
            // Initialize Local Objects Class Array
            self::Core()->widgets = Array();
        }
        
        //====================================================================//
        // Check in Cache
        if (array_key_exists( $WidgetType, self::Core()->widgets ) ) {
            return self::Core()->widgets[$WidgetType];
        }

        //====================================================================//
        // Verify if Object Class is Valid
        if ( !Splash::Validate()->isValidWidget($WidgetType) ) {
            return Null;
        }
        
        //====================================================================//
        // Initialize Class
        $ClassName = SPLASH_CLASS_PREFIX . $WidgetType;
        self::Core()->widgets[$WidgetType]        = new $ClassName();
        
        //====================================================================//
        //  Load Translation File
        self::Translator()->Load("widgets");
            
        return self::Core()->widgets[$WidgetType];
    }
    
    /**
     *      @abstract   Fully Restart Splash Module
     * 
     *      @return     void
     */
    public static function Reboot()
    {
        //====================================================================//
        // Clear Module Configuration Array
        if (isset(self::Core()->conf)) {
            unset(\Splash::Core()->conf);             
        }
        //====================================================================//
        // Clear Webservice Configuration
        if (isset(self::Core()->ws)) {
            unset(\Splash::Core()->ws);             
        }
        //====================================================================//
        // Clear Module Local Core Class
        if (isset(self::Core()->localcore)) {
            unset(\Splash::Core()->localcore);             
        }
        //====================================================================//
        // Clear Module Local Objects Classes
        if (isset(self::Core()->objects)) {
            unset(\Splash::Core()->objects);             
        }
        //====================================================================//
        // Clear Module Log
        self::Log()->CleanLog();
    }
    
//====================================================================//
//  COMMON CLASS INFORMATIONS
//====================================================================//
    
    /**
     *      @abstract   Return name of this library
     *      @return  string    Name of logger
     */
    public static function getName()
    {
        return SPLASH_NAME;
    }

    /**
     *      @abstract   Return Description of this library
     *      @return  string    Name of logger
     */
    public static function getDesc()
    {
        return SPLASH_DESC;
    }
    
    /**
     *      @abstract   Version of the module ('x.y.z' or 'dolibarr' or 'experimental' or 'development')
     *      @return string
     */
    public static function getVersion()
    {
        return SPLASH_VERSION;
    }  
  
//====================================================================//
//  TRANSLATIONS MANAGEMENT
//====================================================================//
   
    /**
     *      @abstract   Return text translated of text received as parameter (and encode it into HTML)
     *
     *      @param  string	$key        Key to translate
     *      @param  string	$param1     chaine de param1
     *      @param  string	$param2     chaine de param2
     *      @param  string	$param3     chaine de param3
     *      @param  string	$param4     chaine de param4
     *      @param  string	$param5     chaine de param5
     *      @param  int		$maxsize    Max length of text
     *      @return string      		Translated string (encoded into HTML entities and UTF8)
     */
    public static function Trans($key, $param1='', $param2='', $param3='', $param4='', $param5='', $maxsize=0)
    {
        return self::Translator()->Translate($key,$param1,$param2,$param3,$param4,$param5,$maxsize);
    }  

//--------------------------------------------------------------------//
//--------------------------------------------------------------------//
//----  ADMIN WEBSERVICE FUNCTIONS                                ----//
//--------------------------------------------------------------------//
//--------------------------------------------------------------------//
    
    /**
     *      @abstract      Ask for Server System Informations
     *                     Informations may be overwritten by Local Module Class
     * 
     *      @return     ArrayObject         	Array including all server informations
     *
     **********************************************************************************
     *******    General Parameters
     **********************************************************************************
     * 
    *					$r->Name            =   $this->name;
    *					$r->Id              =   $this->id;
    *					
    *******			Server Infos
    *					$r->php             =   phpversion();
    *					$r->Self            =   $_SERVER["PHP_SELF"];
    *					$r->Server          =   $_SERVER["SERVER_NAME"];
    *					$r->ServerAddress   =   $_SERVER["SERVER_ADDR"];
    *					$r->Port            =   $_SERVER["SERVER_PORT"];
    *					$r->UserAgent       =   $_SERVER["HTTP_USER_AGENT"];
    *					
    */
    public static function Informations()
    {
        //====================================================================//
        // Init Response Object
        $Response = new ArrayObject(array(),  ArrayObject::ARRAY_AS_PROPS);
        
        //====================================================================//
        // Server General Description
        $Response->shortdesc        =   SPLASH_NAME . " " .  SPLASH_VERSION;
        $Response->longdesc         =   SPLASH_DESC;
        
        //====================================================================//
        // Company Informations
        $Response->company          =   NUll;
        $Response->address          =   NUll;
        $Response->zip              =   NUll;
        $Response->town             =   NUll;
        $Response->country          =   NUll;
        $Response->www              =   NUll;
        $Response->email            =   NUll;
        $Response->phone            =   NUll;
        
        //====================================================================//
        // Server Logo & Ico
        $Response->icoraw           =   Splash::File()->ReadFileContents(SPLASH_DIR . "/img/Splash-ico.png");
        $Response->logourl          =   NUll;
        $Response->logoraw          =   Splash::File()->ReadFileContents(SPLASH_DIR . "/img/Splash-ico.jpg");
        
        //====================================================================//
        // Server Informations
        $Response->servertype       =   SPLASH_NAME;
        $Response->serverurl        =   filter_input(INPUT_SERVER, "SERVER_NAME");

        //====================================================================//
        // Module Informations
        $Response->moduleauthor     =   SPLASH_AUTHOR;
        $Response->moduleversion    =   SPLASH_VERSION;        

        //====================================================================//
        // Verify Local Module Class Is Valid
        if ( !Splash::Validate()->isValidLocalClass() ) {
            return $Response;
        }
        
        //====================================================================//
        // Merge Informations with Local Module Informations
        $LocalArray = Splash::Local()->Informations($Response);
        if ( !is_array($LocalArray) && !is_a($LocalArray, "arrayobject") ) {
            $Response   =   $LocalArray;
        }
        
        return $Response;
    }
    
    /**
     *      @abstract   Build list of Available Objects
     * 
     *      @return     array       $list           list array including all available Objects Type 
     * 
     */
    public static function Objects()
    {
        $ObjectsList = array();
        
        //====================================================================//
        // Safety Check => Verify Objects Folder Exists
        $path    =   Splash::Configuration()->localpath . "/Objects";
        if ( !is_dir($path)) {
            return $ObjectsList;
        }
        
        //====================================================================//
        // Scan Local Objects Folder  
        $scan = array_diff(scandir($path,1), array('..', '.'));
        if ( $scan == FALSE ) {
            return $ObjectsList;
        }
            
        //====================================================================//
        // Scan Each File in Folder  
        foreach ($scan as $filename) {
            $ClassName = pathinfo($path . "/" . $filename, PATHINFO_FILENAME );

            //====================================================================//
            // Verify ClassName is a Valid Object File
            if (self::Validate()->isValidObject($ClassName) == False) {
                break;
            }
            $ObjectsList[] = $ClassName;
        }
        return $ObjectsList;
    }    
   
    /**
     *      @abstract   Perform Local Module Self Test
     * 
     *      @return     bool    Test Result
     */
    public static function SelfTest()
    {
        //====================================================================//
        //  Perform Local Core Class Test
        if ( !Splash::Validate()->isValidLocalClass()) {
            return False;
        }
        
        //====================================================================//
        //  Read Local Objects List
        $ObjectsList   =   Splash::Objects();
        if (is_array($ObjectsList)) {
            foreach ($ObjectsList as $ObjectType) {
                if ( !Splash::Validate()->isValidObject($ObjectType)) {
                    return False;
                }
            }
        }
        
        //====================================================================//
        //  Perform Local SelfTest
        if ( !Splash::Local()->Selftest() ) {
            return False;
        }
        
        return True;
    }       

//--------------------------------------------------------------------//
//--------------------------------------------------------------------//
//----  WIDGETS WEBSERVICE FUNCTIONS                              ----//
//--------------------------------------------------------------------//
//--------------------------------------------------------------------//
    
    /**
     *      @abstract   Build list of Available Widgets
     * 
     *      @return     array       $list           list array including all available Widgets Type 
     * 
     */
    public static function Widgets()
    {
        $WidgetsList = array();
        
        //====================================================================//
        // Safety Check => Verify Objects Folder Exists
        $path    =   Splash::Configuration()->localpath . "/Widgets";
        if ( !is_dir($path)) {
            return $WidgetsList;
        }
        
        //====================================================================//
        // Scan Local Objects Folder  
        $scan = array_diff(scandir($path,1), array('..', '.'));
        if ( $scan == FALSE ) {
            return $WidgetsList;
        }
            
        //====================================================================//
        // Scan Each File in Folder  
        foreach ($scan as $filename) {
            $ClassName = pathinfo($path . "/" . $filename, PATHINFO_FILENAME );

            //====================================================================//
            // Verify ClassName is a Valid Object File
            if (self::Validate()->isValidWidget($ClassName) == False) {
                break;
            }
            $WidgetsList[] = $ClassName;
        }
        return $WidgetsList;
    }      
    
}

?>