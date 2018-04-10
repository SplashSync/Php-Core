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
 * @abstract    Tooling Class for Validation of Splash Php Module Contents
 * @author      B. Paquier <contact@splashsync.com>
 */

namespace   Splash\Components;

use Splash\Core\SplashCore      as Splash;

//====================================================================//
//  CLASS DEFINITION
//====================================================================//

class Validator
{

//====================================================================//
    // *******************************************************************//
    //  VALIDATE LOCAL CORE CLASS & FUNCTIONS
    // *******************************************************************//
    //====================================================================//

    /**
    *   @abstract   Verify Local Core Class Exists & Is Valid
    *
    *   @return     int         $result     0 if KO, 1 if OK
    */
    public function isValidLocalClass()
    {
        $ClassName = SPLASH_CLASS_PREFIX . "\Local";
        //====================================================================//
        // Verify Results in Cache
        if (isset($this->ValidLocalClass[$ClassName])) {
            return $this->ValidLocalClass[$ClassName];
        }
        
        $this->ValidLocalClass[$ClassName] = false;

        //====================================================================//
        // Verify Splash Local Core Class Exists
        if (class_exists($ClassName) == false) {
            return Splash::log()->err(Splash::trans("ErrLocalClass", $ClassName));
        }
        
        //====================================================================//
        // Verify Splash Local Core Functions Exists
        if ($this->isValidLocalFunction("Parameters", $ClassName) == false) {
            return false;
        }

        if ($this->isValidLocalFunction("Includes", $ClassName) == false) {
            return false;
        }

        if ($this->isValidLocalFunction("Informations", $ClassName) == false) {
            return false;
        }
        
        if ($this->isValidLocalFunction("SelfTest", $ClassName) == false) {
            return false;
        }
        
        $this->ValidLocalClass[$ClassName] = true;
        
        return $this->ValidLocalClass[$ClassName];
    }
    
    /**
    *   @abstract   Verify Local Core Parameters are Valid
    *
    *   @return     int         $result     0 if KO, 1 if OK
    */
    public function isValidLocalParameterArray($In)
    {
        //====================================================================//
        // Verify Array Given
        if (!is_array($In)) {
            return Splash::log()->err(Splash::trans("ErrorCfgNotAnArray", get_class($In)));
        }
        
        //====================================================================//
        // Required Parameters are Available
        //====================================================================//
        if (!array_key_exists("WsIdentifier", $In)) {
            return Splash::log()->err(Splash::trans("ErrorCfgKeyMissing", "WsIdentifier"));
        }

        if (!array_key_exists("WsEncryptionKey", $In)) {
            return Splash::log()->err(Splash::trans("ErrorCfgKeyMissing", "WsEncryptionKey"));
        }
        
        return true;
    }

    /**
    *   @abstract   Verify Local Test Parameters are Valid
    *
    *   @return     int         $result     0 if KO, 1 if OK
    */
    public function isValidLocalTestParameterArray($In)
    {
        //====================================================================//
        // Verify Array Given
        if (!is_array($In)) {
            return Splash::log()->err(Splash::trans("ErrorCfgNotAnArray", get_class($In)));
        }
        return true;
    }
    
    /**
    *   @abstract   Verify Webserver Informations are Valid
    *
    *   @return     int         $result     0 if KO, 1 if OK
    */
    public function isValidServerInfos()
    {
        $In     = Splash::ws()->getServerInfos();
        
        //====================================================================//
        // Verify Array Given
        if (!is_a($In, "ArrayObject")) {
            return Splash::log()->err(Splash::trans("ErrInfosNotArrayObject", get_class($In)));
        }
        
        if (defined('SPLASH_DEBUG') && SPLASH_DEBUG) {
            Splash::log()->war("Host : " .  $In['ServerHost']);
            Splash::log()->war("Path : " .  $In['ServerPath']);
        }
        
        //====================================================================//
        // Required Parameters are Available
        //====================================================================//
        if (!isset($In['ServerHost']) || empty($In['ServerHost'])) {
            Splash::log()->err(Splash::trans("ErrEmptyServerHost"));
            return Splash::log()->err(Splash::trans("ErrEmptyServerHostDesc"));
        }

        if (!isset($In['ServerPath']) || empty($In['ServerPath'])) {
            Splash::log()->err(Splash::trans("ErrEmptyServerPath"));
            return Splash::log()->err(Splash::trans("ErrEmptyServerPathDesc"));
        }
        
        //====================================================================//
        // Detect Local Installations
        //====================================================================//
        
        if (strpos($In['ServerHost'], "localhost") !== false) {
            Splash::log()->war(Splash::trans("WarIsLocalhostServer"));
        } elseif (strpos($In['ServerIP'], "127.0.0.1") !== false) {
            Splash::log()->war(Splash::trans("WarIsLocalhostServer"));
        }
        
        if (Splash::input("REQUEST_SCHEME") === "https") {
            Splash::log()->war(Splash::trans("WarIsHttpsServer"));
        }
        

        
        return true;
    }
    
    //====================================================================//
    // *******************************************************************//
    //  VALIDATE OBJECTS CLASSES & FUNCTIONS
    // *******************************************************************//
    //====================================================================//
    
    /**
     *   @abstract   Verify this parameter is a valid object type name
     *   @param      string      $ObjectType     Object Class/Type Name
     *   @return     bool
     */
    public function isValidObject($ObjectType)
    {
        //====================================================================//
        // Verify Result in Cache
        if (isset($this->ValidLocalObject[$ObjectType])) {
            return $this->ValidLocalObject[$ObjectType];
        }
        
        $this->ValidLocalObject[$ObjectType] = false;
        
        //====================================================================//
        // Check if Object Manager is NOT Overriden
        if (!$this->isValidLocalOverride("Objects")) {
            //====================================================================//
            // Verify Object File Exist & is Valid
            if (!$this->isValidObjectFile($ObjectType)) {
                return false;
            }
        }
        
        //====================================================================//
        // Verify Object Class Exist & is Valid
        if (!$this->isValidObjectClass($ObjectType)) {
            return false;
        }
        
        $this->ValidLocalObject[$ObjectType] = true;
        return true;
    }

    /**
     *      @abstract     Verify a Local Object File is Valid.
     *      @param        string    $ObjectType     Object Type Name
     *      @return       int                       0 if KO, 1 if OK
     */
    private function isValidObjectFile($ObjectType)
    {
        //====================================================================//
        // Verify Local Path Exist
        if ($this->isValidLocalPath() == false) {
            return false;
        }
        
        //====================================================================//
        // Verify Object File Exist
        $filename = Splash::getLocalPath() . "/Objects/" . $ObjectType . ".php";
        if (file_exists($filename) == false) {
            $msg = "Local Object File Not Found.</br>";
            $msg.= "Current Filename : " . $filename . "";
            return Splash::log()->err($msg);
        }
        
        return true;
    }
    
    /**
     *      @abstract     Verify Availability of a Local Object Class.
     *      @param        string    $ObjectType     Object Type Name
     *      @return       int                       0 if KO, 1 if OK
     */
    private function isValidObjectClass($ObjectType)
    {
        //====================================================================//
        // Check if Object Manager is Overriden
        if ($this->isValidLocalOverride("Object")) {
            //====================================================================//
            // Retrieve Object Manager ClassName
            $ClassName = get_class(Splash::local()->object($ObjectType));
        } else {
            $ClassName = SPLASH_CLASS_PREFIX . "\Objects\\" . $ObjectType;
        }
        //====================================================================//
        // Verify Splash Local Core Class Exists
        if (class_exists($ClassName) == false) {
            return Splash::log()->err(Splash::trans("ErrLocalClass", $ObjectType));
        }
        
        //====================================================================//
        // Verify Local Object Core Class Functions Exists
        //====================================================================//
        
        //====================================================================//
        // Read Object Disable Flag
        if ($this->isValidLocalFunction("getIsDisabled", $ClassName) == false) {
            $this->ValidLocalObject[$ObjectType] = false;
            return false;
        }
        if ($ClassName::getIsDisabled()) {
            $this->ValidLocalObject[$ObjectType] = false;
            return false;
        }
        
        //====================================================================//
        // Verify Local Object Class Functions Exists
        //====================================================================//
        
        //====================================================================//
        // Read Object Available Fields List
        if ($this->isValidLocalFunction(SPL_F_FIELDS, $ClassName) == false) {
            $this->ValidLocalObject[$ObjectType] = false;
            return false;
        }
        //====================================================================//
        // Read Object List
        if ($this->isValidLocalFunction(SPL_F_LIST, $ClassName) == false) {
            $this->ValidLocalObject[$ObjectType] = false;
            return false;
        }
        //====================================================================//
        // Read Object Data
        if ($this->isValidLocalFunction(SPL_F_GET, $ClassName) == false) {
            $this->ValidLocalObject[$ObjectType] = false;
            return false;
        }
        //====================================================================//
        // Write Object Data
        if ($this->isValidLocalFunction(SPL_F_SET, $ClassName) == false) {
            $this->ValidLocalObject[$ObjectType] = false;
            return false;
        }
        //====================================================================//
        // Delete Object Data
        if ($this->isValidLocalFunction(SPL_F_DEL, $ClassName) == false) {
            $this->ValidLocalObject[$ObjectType] = false;
            return false;
        }

        return true;
    }
        
    /**
     *   @abstract   Verify this Object Type is valid in Local Syetem
     *   @param      string      $ObjectType         Object Type Name String
     *   @return     bool
     */
    public static function isValidObjectType($ObjectType)
    {
        //====================================================================//
        // Verify Type Name is in List
        return in_array($ObjectType, Splash::objects());
    }

    //====================================================================//
    // *******************************************************************//
    //  VALIDATE OBJECTS I/O
    // *******************************************************************//
    //====================================================================//

    /**
     *  @abstract   Verify Object Identifier
     *
     *  @param      string      $Id     Object Identifier
     *
     *  @return     bool
     */
    public function isValidObjectId($Id)
    {
        //====================================================================//
        // Checks Id is not Null
        if (is_null($Id)) {
            return Splash::log()->err("ErrEmptyObjectId");
        }

        //====================================================================//
        // Checks Id is String or Int
        if (!is_string($Id) && !is_numeric($Id)) {
            return Splash::log()->err("ErrWrongObjectId");
        }
        
        //====================================================================//
        // Checks List Not Empty
        if (is_numeric($Id) && ($Id < 0)) {
            return Splash::log()->err("ErrNegObjectId");
        }
        
        return Splash::log()->deb("MsgObjectIdOk");
    }
    
    /**
     *  @abstract   Verify Object Field List
     *
     *  @param      array   $List       Object Field List
     *
     *  @return     bool
     */
    public function isValidObjectFieldsList($List)
    {
        //====================================================================//
        // Checks List Type
        if (!is_array($List) && !is_a($List, "ArrayObject")) {
            return Splash::log()->err("ErrWrongFieldList");
        }
        
        //====================================================================//
        // Checks List Not Empty
        if (empty($List)) {
            return Splash::log()->err("ErrEmptyFieldList");
        }
        
        return Splash::log()->deb("MsgFieldListOk");
    }
    
    //====================================================================//
    // *******************************************************************//
    //  VALIDATE WIDGETS CLASSES & FUNCTIONS
    // *******************************************************************//
    //====================================================================//
    
    /**
     *   @abstract   Verify this parameter is a valid widget type name
     *   @param      string      $WidgetType     Widget Class/Type Name
     *   @return     bool
     */
    public function isValidWidget($WidgetType)
    {
        //====================================================================//
        // Verify Result in Cache
        if (isset($this->ValidLocalWidget[$WidgetType])) {
            return $this->ValidLocalWidget[$WidgetType];
        }
        $this->ValidLocalWidget[$WidgetType] = false;
        
        //====================================================================//
        // Check if Widget Manager is NOT Overriden
        if (!$this->isValidLocalOverride("Widgets")) {
            //====================================================================//
            // Verify Widget File Exist & is Valid
            if (!$this->isValidWidgetFile($WidgetType)) {
                return false;
            }
        }
        
        //====================================================================//
        // Verify Widget Class Exist & is Valid
        if (!$this->isValidWidgetClass($WidgetType)) {
            return false;
        }
        $this->ValidLocalWidget[$WidgetType] = true;
        return true;
    }
    
    /**
     *      @abstract     Verify a Local Widget File is Valid.
     *      @param        string    $WidgetType     Widget Type Name
     *      @return       int                       0 if KO, 1 if OK
     */
    private function isValidWidgetFile($WidgetType)
    {
        //====================================================================//
        // Verify Local Path Exist
        if ($this->isValidLocalPath() == false) {
            return false;
        }
        //====================================================================//
        // Verify Object File Exist
        $filename = Splash::getLocalPath() . "/Widgets/" . $WidgetType . ".php";
        if (file_exists($filename) == false) {
            $msg = "Local Widget File Not Found.</br>";
            $msg.= "Current Filename : " . $filename . "";
            return Splash::log()->err($msg);
        }
        return true;
    }
    
    /**
     *      @abstract     Verify Availability of a Local Widget Class.
     *      @param        string    $WidgetType     Widget Type Name
     *      @return       int                       0 if KO, 1 if OK
     */
    private function isValidWidgetClass($WidgetType)
    {
        //====================================================================//
        // Check if Widget Manager is Overriden
        if ($this->isValidLocalOverride("Widget")) {
            //====================================================================//
            // Retrieve Widget Manager ClassName
            $ClassName = get_class(Splash::local()->widget($WidgetType));
        } else {
            $ClassName = SPLASH_CLASS_PREFIX . "\Widgets\\" .$WidgetType;
        }

        //====================================================================//
        // Verify Splash Local Core Class Exists
        if (class_exists($ClassName) == false) {
            return Splash::log()->err(Splash::trans("ErrLocalClass", $WidgetType));
        }
        
        //====================================================================//
        // Verify Local Widget Core Class Functions Exists
        //====================================================================//
        
        //====================================================================//
        // Read Object Disable Flag
        if ($this->isValidLocalFunction("getIsDisabled", $ClassName) == false) {
            $this->ValidLocalWidget[$WidgetType] = false;
            return false;
        }
        if ($ClassName::getIsDisabled()) {
            $this->ValidLocalWidget[$WidgetType] = false;
            return false;
        }
        
        //====================================================================//
        // Verify Local Widget Class Functions Exists
        //====================================================================//
        
        //====================================================================//
        // Read Object Available Fields List
        if ($this->isValidLocalFunction(SPL_F_WIDGET_DEFINITION, $ClassName) == false) {
            $this->ValidLocalWidget[$WidgetType] = false;
            return false;
        }
        //====================================================================//
        // Read Object List
        if ($this->isValidLocalFunction(SPL_F_WIDGET_GET, $ClassName) == false) {
            $this->ValidLocalWidget[$WidgetType] = false;
            return false;
        }

        return true;
    }
    
    //====================================================================//
    // *******************************************************************//
    //  VALIDATE COMMONS FUNCTIONS
    // *******************************************************************//
    //====================================================================//
    
    /**
     *   @abstract   Verify Local Path Exists
     *   @return     int         $result     0 if KO, 1 if OK
     */
    public function isValidLocalPath()
    {
        //====================================================================//
        // Verify no result in Cache
        if (!isset($this->ValidLocalPath)) {
            $path    = Splash::getLocalPath();
            //====================================================================//
            // Verify Local Path Exist
            if (!is_dir($path)) {
                $this->ValidLocalPath = false;
                return Splash::log()->err(Splash::trans("ErrLocalPath", $path));
            }
            
            $this->ValidLocalPath = true;
        }
        return $this->ValidLocalPath;
    }
    
    /**
     *      @abstract     Verify Availability of a local method/function prior to task execution.
     *
     *      @param        string    $Method         Function Name
     *      @param        string    $ClassName      Optionnal Class Name
     *      @param        bool      $Required       Indicate this Function is Required by Module (Or Optional)
     *
     *      @return       bool
     */
    public function isValidLocalFunction($Method, $ClassName = null, $Required = true)
    {
        //====================================================================//
        // Prefill ClassName
        if (is_null($ClassName)) {
            $ClassName = SPLASH_CLASS_PREFIX . "\Local";
        }
        //====================================================================//
        // Verify Result in Cache
        if (isset($this->ValidLocalFunctions[$ClassName][$Method])) {
            return $this->ValidLocalFunctions[$ClassName][$Method];
        }

        //====================================================================//
        // Verify Class Method Exists
        if (method_exists($ClassName, $Method) == false) {
            $this->ValidLocalFunctions[$ClassName][$Method] = false;
            return $Required?Splash::log()->err(Splash::trans("ErrLocalFunction", $ClassName, $Method)):false;
        }
        $this->ValidLocalFunctions[$ClassName][$Method] = true;

        return $this->ValidLocalFunctions[$ClassName][$Method];
    }

    /**
     *      @abstract     Verify Availability of a local method/function prior to local overriding.
     *
     *      @param        string    $Method         Function Name
     *
     *      @return       bool
     */
    public function isValidLocalOverride($Method)
    {
        //====================================================================//
        // Verify Local Core Class Exist & Is Valid
        if ($this->isValidLocalClass()) {
            //====================================================================//
            // Check if Local Core Class Include Overriding Functions
            return $this->isValidLocalFunction($Method, null, false);
        }
        
        return false;
    }
    
    //====================================================================//
    // *******************************************************************//
    //  VALIDATE LOCAL SERVER
    // *******************************************************************//
    //====================================================================//
   
    /**
     *      @abstract     Verify PHP Version is Compatible.
     *
     *      @return       bool
     */
    public function isValidPHPVersion()
    {
        if (version_compare(PHP_VERSION, '5.6.0') < 0) {
            return Splash::log()->err(
                "PHP : Your PHP version is too low to use Splash (" . PHP_VERSION . "). PHP >5.6 is Requiered."
            );
        }
        return Splash::log()->msg(
            "PHP : Your PHP version is compatible with Splash (" . PHP_VERSION . ")"
        );
    }
    
    /**
     *      @abstract     Verify PHP Required are Installed & Active
     *
     *      @return       bool
     */
    public function isValidPHPExtensions()
    {
        $Extensions = array("xml", "soap", "curl");
        foreach ($Extensions as $Extension) {
            if (!extension_loaded($Extension)) {
                return Splash::log()->err(
                    "PHP :" . $Extension . " PHP Extension is required to use Splash PHP Module."
                );
            }
        }
        return Splash::log()->msg(
            "PHP : Required PHP Extension are installed (" . implode(', ', $Extensions) . ")"
        );
    }
    
    /**
     *      @abstract     Verify WebService Library is Valid.
     *
     *      @return       bool
     */
    public function isValidSOAPMethod()
    {
        if (!in_array(Splash::configuration()->WsMethod, ["SOAP", "NuSOAP"])) {
            return Splash::log()->err(
                "Config : Your selected an unknown SOAP Method (" . Splash::configuration()->WsMethod . ")."
            );
        }
        return Splash::log()->msg(
            "Config : SOAP Method is Ok (" . Splash::configuration()->WsMethod . ")."
        );
    }
}
