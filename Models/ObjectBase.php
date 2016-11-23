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
 * @abstract    This class is a base class for all Splash Objects.
 * @author      B. Paquier <contact@splashsync.com>
 */

namespace   Splash\Models;

use Splash\Core\SplashCore      as Splash;
use Splash\Components\FieldsFactory;

//====================================================================//
//********************************************************************//
//====================================================================//
//  SPLASH OBJECTS BASE CLASS
//====================================================================//
//********************************************************************//
//====================================================================//

class ObjectBase
{
    /**
     * @var Static Class Storage
     */
    protected static    $fields;
    
    /**
     *  Object Disable Flag. Override this flag to disable Object.
     */
    protected static    $DISABLED        =  False;
    
    /**
     *  Object Name
     */
    protected static    $NAME            =  __CLASS__;
    
    /**
     *  Object Description 
     */
    protected static    $DESCRIPTION     =  __CLASS__;

    /**
     *  Object Icon (FontAwesome or Glyph ico tag) 
     */
    protected static    $ICO     =  "fa fa-cubes";

    /**
     *  Object Synchronistion Limitations 
     *  
     *  This Flags are Used by Splash Server to Prevent Unexpected Operations on Remote Server
     */
    protected static    $ALLOW_PUSH_CREATED         =  TRUE;        // Allow Creation Of New Local Objects
    protected static    $ALLOW_PUSH_UPDATED         =  TRUE;        // Allow Update Of Existing Local Objects
    protected static    $ALLOW_PUSH_DELETED         =  TRUE;        // Allow Delete Of Existing Local Objects
    
    /**
     *  Object Synchronistion Recommended Configuration 
     */
    protected static    $ENABLE_PUSH_CREATED       =  TRUE;         // Enable Creation Of New Local Objects when Not Existing
    protected static    $ENABLE_PUSH_UPDATED       =  TRUE;         // Enable Update Of Existing Local Objects when Modified Remotly
    protected static    $ENABLE_PUSH_DELETED       =  TRUE;         // Enable Delete Of Existing Local Objects when Deleted Remotly

    protected static    $ENABLE_PULL_CREATED       =  TRUE;         // Enable Import Of New Local Objects 
    protected static    $ENABLE_PULL_UPDATED       =  TRUE;         // Enable Import of Updates of Local Objects when Modified Localy
    protected static    $ENABLE_PULL_DELETED       =  TRUE;         // Enable Delete Of Remotes Objects when Deleted Localy
    
    //====================================================================//
    // General Class Variables	
    //====================================================================//
    
    /**
     * Set Operations Input Buffer
     * 
     * @abstract This variable is used to store Object Array during Set Operations
     *              Each time a field is imported, unset it from this buffer 
     *              to control all fields were imported at the end of Set Operation
     * @var ArrayObject
     */
    protected   $In            = Null;
    
    /**
     * Get Operations Output Buffer
     * 
     * @abstract This variable is used to store Object Array during Get Operations
     * @var ArrayObject
     */
    protected   $Out            = Null;
    
    /**
     * Work Object Class 
     * 
     * @abstract This variable is used to store current working Object during Set & Get Operations
     * @var mixed
     */
    protected   $Object         = Null;
    
    /**
     * Set Operations Updated Flag
     * 
     * @abstract This flag is set when an update is done during Set Operation.
     *           Using this flag is useful to reduce number of data commit on remotes
     * @var bool
     */
    protected   $update         = False;
    
//====================================================================//
//  STATIC CLASS ACCESS
//  Creation & Acces to all subclasses Instances
//====================================================================//
    
    /**
     *      @abstract   Get a singleton FieldsFactory Class
     *                  Acces to Object Fields Creation Functions
     *      @return     SplashFieldsFactory
     */    
    public static function FieldsFactory()
    {
        //====================================================================//
        // Initialize Field Factory Class
        if (isset(self::$fields)) {
            return self::$fields;
        }
        
        //====================================================================//
        // Initialize Class
        self::$fields        = new FieldsFactory();  
        
        //====================================================================//
        //  Load Translation File
        Splash::Translator()->Load("objects");
        
        return self::$fields;
    }     
    
//====================================================================//
//  COMMON CLASS INFORMATIONS
//====================================================================//

    /**
     *      @abstract   Return type of this Object Class
     */
    public static function getType()
    {
        return pathinfo(__FILE__,PATHINFO_FILENAME);
    }
    
    /**
     *      @abstract   Return name of this Object Class
     */
    public function getName()
    {
        return self::Trans(static::$NAME);
    }

    /**
     *      @abstract   Return Description of this Object Class
     */
    public function getDesc()
    {
        return self::Trans(static::$DESCRIPTION);
    }
    
    /**
     *      @abstract   Return Object Status
     */
    public static function getIsDisabled()
    {
        return static::$DISABLED;
    }
    
    /**
     *      @abstract   Return Object Icon
     */
    public static function getIcon()
    {
        return static::$ICO;
    }


//====================================================================//
//  COMMON FIELDS GETTERS & SETTERS
//====================================================================//
    
    /**
     *  @abstract     Common Reading of a Single Field
     * 
     *  @param        string    $FieldName              Field Identifier / Name
     *  @param        string    $Object                 Name of private object to read (Default : "object")
     *  
     *  @return       SplashObject
     */
    protected function getSingleField($FieldName, $Object = "Object") {
        $this->Out[$FieldName] = trim($this->{$Object}->$FieldName);
        return $this;
    }
    
    /**
     *  @abstract     Common Reading of a Single Bool Field
     * 
     *  @param        string    $FieldName              Field Identifier / Name
     *  @param        string    $Object                 Name of private object to read (Default : "object")
     * 
     *  @return       SplashObject
     */
    protected function getSingleBoolField($FieldName, $Object = "Object") {
        $this->Out[$FieldName] = (bool) $this->{$Object}->$FieldName;
        return $this;
    }
    
    /**
     *  @abstract     Common Reading of a Single Field
     *                  => If Field Needs to be Updated, do Object Update & Set $this->update to true
     * 
     *  @param        string    $FieldName              Field Identifier / Name
     *  @param        mixed     $Data                   Field Data
     *  @param        string    $Object                 Name of private object to read (Default : "object")
     * 
     *  @return       SplashObject
     */
    protected function setSingleField($FieldName,$Data, $Object = "Object") {
        
        //====================================================================//
        //  Compare Field Data
        if ( $this->{$Object}->$FieldName != $Data ) {
            //====================================================================//
            //  Update Field Data
            $this->{$Object}->$FieldName = $Data;
            $this->update = True;
        }  
        return $this;
    }
    
     /**
     *  @abstract     Common Reading of a Single Field
     * 
     *  @param        string    $Key                    Input List Key
     *  @param        string    $FieldName              Field Identifier / Name
     *  @param        mixed     $Data                   Field Data
     * 
     *  @return       SplashObject
     */
    protected function setSingleFloatField($FieldName,$Data) {
        //====================================================================//
        //  Compare Field Data
        if ( !$this->Float_Compare($this->Object->$FieldName , $Data) ) {
            //====================================================================//
            //  Update Field Data
            $this->Object->$FieldName = $Data;
            $this->update = True;
        }  
        return $this;
    }
    
//====================================================================//
//  TRANSLATIONS MANAGEMENT
//====================================================================//

    /**
     *      @abstract       Load translations from a specified INI file into Static array.
     *                      If data for file already loaded, do nothing.
     *                      All data in translation array are stored in UTF-8 format.
     *                      trans_loaded is completed with $file key.
     * 
     *      @param	string	$FileName   File name to load (.ini file). Must be "file" or "file@local" for local language files:
     *                                      If $FileName is "file@local" instead of "file" then we look for local lang file
     *                                      in localpath/langs/code_CODE/file.lang
     * 
     *      @return	bool
     * 
     */
    public function Load($FileName) {
        return Splash::Translator()->Load($FileName);
    }
    
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
        return Splash::Translator()->Translate($key,$param1,$param2,$param3,$param4,$param5,$maxsize);
    }  

//====================================================================//
//  COMMON CLASS VALIDATION
//====================================================================//

    /**
     *      @abstract   Run Validation procedure on this object Class & Return return
     * 
     *      @return     bool
     */
    public function Validate() {
        return Splash::Validate()->isValidObject(__CLASS__);
    }
    
//====================================================================//
//  COMMON CLASS SERVER ACTIONS
//====================================================================//

    /**
     *  @abstract   Get Description Array for requested Object Type
     * 
     *  @return     bool
     */    
    public function Description()
    {
        //====================================================================//
        // Stack Trace
        Splash::Log()->Trace(__CLASS__,__FUNCTION__);  
        
        //====================================================================//
        // Build & Return Object Description Array
        return array(
            //====================================================================//
            // General Object definition
            "type"          =>  $this->getType(),                   // Object Type Name
            "name"          =>  $this->getName(),                   // Object Display Neme
            "description"   =>  $this->getDesc(),                   // Object Descritioon
            "icon"          =>  $this->getIcon(),                   // Object Icon
            "disabled"      =>  $this->getIsDisabled(),              // Is This Object Enabled or Not?
            //====================================================================//
            // Object Limitations
            "allow_push_created"      =>  (bool) static::$ALLOW_PUSH_CREATED,
            "allow_push_updated"      =>  (bool) static::$ALLOW_PUSH_UPDATED,
            "allow_push_deleted"      =>  (bool) static::$ALLOW_PUSH_DELETED,
            //====================================================================//
            // Object Default Configuration
            "enable_push_created"     =>  (bool) static::$ENABLE_PUSH_CREATED,
            "enable_push_updated"     =>  (bool) static::$ENABLE_PUSH_UPDATED,
            "enable_push_deleted"     =>  (bool) static::$ENABLE_PUSH_DELETED,
            "enable_pull_created"     =>  (bool) static::$ENABLE_PULL_CREATED,
            "enable_pull_updated"     =>  (bool) static::$ENABLE_PULL_UPDATED,
            "enable_pull_deleted"     =>  (bool) static::$ENABLE_PULL_DELETED
        );
    }     
    
//====================================================================//
//  OBJECT LOCK MANAGEMENT
//====================================================================//
    
    /**
     *      @abstract   Set Lock for a specific object
     * 
     *                  This function is used to prevent further actions 
     *                  on currently edited objects. Node name & Type are
     *                  single, but Ids have to be stored as list
     * 
     *      @param      int         $Identifier     Local Object Identifier or Empty if New Object
     * 
     *      @return     bool
     */
    function Lock($Identifier = "new")
    {
        //====================================================================//
        // Search for Forced Commit Flag in Configuration
        if (array_key_exists("forcecommit",Splash::Configuration()->server) && (Splash::Configuration()->server["forcecommit"]) ) {
            return True;
        }
        
        //====================================================================//
        // Verify Object Identifier is not Empty
        if ( !$Identifier ) {
            $Identifier = "new";
        }
        
        //====================================================================//
        //  Init Lock Structure 
        if ( !isset($this->locks) )    {
            $this->locks = new ArrayObject(array(),  ArrayObject::ARRAY_AS_PROPS);
        }
        
        //====================================================================//
        //  Insert Object to Structure 
        $this->locks->offsetSet($Identifier,True);
        
        //====================================================================//
        //  Log 
        Splash::Log()->Deb("MsgLockObject", __CLASS__ ,$Identifier);
        
        return True;        
    }   

    /**
     *      @abstract   Get Lock Status for a specific object
     * 
     *      @param      int         $Identifier     Local Object Identifier or Empty if New Object
     * 
     *      @return     bool      
     */
    function isLocked($Identifier = "new")
    {
        Splash::Log()->Deb("MsgisLockedStart", __CLASS__ ,$Identifier);
        
        //====================================================================//
        // Verify Object Identifier is not Empty
        if ( !$Identifier ) {
            $Identifier = "new";
        }
        
        //====================================================================//
        //  Verify Lock Structure Exits
        if ( !isset($this->locks) ) { 
            return False; 
        }
        
        //====================================================================//
        //  Verify Object Exits
        if ( !$this->locks->offsetExists($Identifier) )        
        { 
            return False; 
        }
        
        //====================================================================//
        //  Log 
        Splash::Log()->Deb("MsgisLocked", __CLASS__ ,$Identifier);
        return True;        
    }   
    
    /**
     *      @abstract   Delete Current active Lock 
     * 
     *      @param      int         $Identifier     Local Object Identifier or Empty if New Object
     * 
     *      @return     bool      
     */
    function Unlock($Identifier = "new")
    {
        //====================================================================//
        //  Verify Object Already Locked
        if ( !$this->isLocked($Identifier) )    { 
            return True; 
        }
        
        //====================================================================//
        //  Remove Object Lock
        $this->locks->offsetUnset($Identifier);
        
        //====================================================================//
        //  Log 
        Splash::Log()->Deb("MsgUnlockSuccess", __CLASS__ ,$Identifier);
        
        return True;        
    }  
     
//====================================================================//
//  OBJECT ID FIELDS MANAGEMENT
//====================================================================//
   
    /**
     *      @abstract   Create an Object Identifier String
     * 
     *      @param      string      $ObjectType     Object Type Name. 
     *      @param      string      $Identifier     Object Identifier
     * 
     *      @return     string      
     */
    protected static function ObjectId_Encode($ObjectType,$Identifier)
    {
        //====================================================================//
        // Safety Checks
        if (empty($ObjectType))                 {   return False;     }
        if (empty($Identifier))                 {   return False;     }
        
        //====================================================================//
        // Create & Return Field Id Data String
        return   $Identifier . IDSPLIT . $ObjectType; 
    }    
    
    /**
     *      @abstract   Retrieve Identifier from an Object Identifier String
     * 
     *      @param      string      $ObjectId           Object Identifier String. 
     * 
     *      @return     string
     */
    protected static function ObjectId_DecodeId($ObjectId)
    {
        //====================================================================//
        // Safety Checks
        if (empty($ObjectId))                 {   return False;     }
        
        //====================================================================//
        // Explode Object String
        $Tmp = explode ( IDSPLIT , $ObjectId);
        
        //====================================================================//
        // Check result is Valid
        if (count($Tmp) != 2)                 {   return False;     }
        
        //====================================================================//
        // Return Object Identifier
        return   $Tmp[0]; 
    }     

    /**
     *      @abstract   Retrieve Object Type from an Object Identifier String
     * 
     *      @param      string      $ObjectId           Object Identifier String. 
     * 
     *      @return     string
     */
    protected static function ObjectId_DecodeType($ObjectId)
    {
        //====================================================================//
        // Safety Checks
        if (empty($ObjectId))                 {   return False;     }
        
        //====================================================================//
        // Explode Object String
        $Tmp = explode ( IDSPLIT , $ObjectId);
        
        //====================================================================//
        // Check result is Valid
        if (count($Tmp) != 2)                 {   return False;     }
        
        //====================================================================//
        // Return Object Identifier
        return   $Tmp[1]; 
    }     

//====================================================================//
// FIELDS LIST IDENTIFIERS MANAGEMENT
//====================================================================//
   
    /**
     *      @abstract   Create a List Field Identifier String
     * 
     *      @param      string      $ListName       Field List Name. 
     *      @param      string      $Identifier     Field Identifier
     * 
     *      @return     string      
     */
    protected static function ListField_Encode($ListName,$Identifier)
    {
        //====================================================================//
        // Safety Checks
        if (empty($ListName))                   {   return False;     }
        if (empty($Identifier))                 {   return False;     }
        //====================================================================//
        // Create & Return List Field Id Data String
        return   $Identifier . LISTSPLIT . $ListName; 
    }    
    
    /**
     *      @abstract   Retrieve Field Identifier from an List Field String
     * 
     *      @param      string      $ListFieldName      List Field Identifier String
     * 
     *      @return     string
     */
    protected static function ListField_DecodeFieldName($ListFieldName)
    {
        //====================================================================//
        // Safety Checks
        if (empty($ListFieldName))                 {   return False;     }
        //====================================================================//
        // Explode Object String
        $Tmp = explode ( LISTSPLIT , $ListFieldName);
        //====================================================================//
        // Check result is Valid
        if (count($Tmp) != 2)                 {   return False;     }
        //====================================================================//
        // Return Object Identifier
        return   $Tmp[0]; 
    }     

    /**
     *      @abstract   Retrieve List Name from an List Field String
     * 
     *      @param      string      $ListFieldName      List Field Identifier String
     * 
     *      @return     string
     */
    protected static function ListField_DecodeListName($ListFieldName)
    {
        //====================================================================//
        // Safety Checks
        if (empty($ListFieldName))                 {   return False;     }
        //====================================================================//
        // Explode Object String
        $Tmp = explode ( LISTSPLIT , $ListFieldName);
        //====================================================================//
        // Check result is Valid
        if (count($Tmp) != 2)                 {   return False;     }
        //====================================================================//
        // Return Object Identifier
        return   $Tmp[1]; 
    }         
    
//====================================================================//
// FIELDS LIST DATA MANAGEMENT
//====================================================================//
    
    /**
     *      @abstract   Validate & Init List before Adding Data
     * 
     *      @param      string      $ListName           List Identifier String
     *      @param      string      $FieldName          List Field Identifier String
     * 
     *      @return     string
     */
    protected function List_InitOutput($ListName,$FieldName) {
        //====================================================================//
        // Check List Name
        if (self::ListField_DecodeListName($FieldName) !== $ListName) {
            return True;
        }
        //====================================================================//
        // Create List Array If Needed
        if (!array_key_exists($ListName,$this->Out)) {
            $this->Out[$ListName] = array();
        }
        //====================================================================//
        // Decode Field Name
        return self::ListField_DecodeFieldName($FieldName);
    }

    /**
     *      @abstract   Add Item Data in Given  Output List
     * 
     *      @param      string      $ListName           List Identifier String
     *      @param      string      $FieldName          List Field Identifier String
     *      @param      string      $Key                List Item Index Key
     *      @param      mixed       $Data               Item Data
     * 
     *      @return     string
     */
    protected function List_Insert($ListName,$FieldName,$Key,$Data) 
    {
            //====================================================================//
            // Create Line Array If Needed
            if (!array_key_exists($Key,$this->Out[$ListName])) {
                $this->Out[$ListName][$Key] = array();
            }    
            //====================================================================//
            // Store Data in Array
            $FieldIndex = explode("@",$FieldName);
            $this->Out[$ListName][$Key][$FieldIndex[0]] = $Data;
    }
    
            
//====================================================================//
//  PRICE FIELDS MANAGEMENT
//====================================================================//
    
    /**
    *   @abstract   Build a new price field array 
    *   @param      double      $HT             Price Without VAT (Or Null if Price Send with VAT)
    *   @param      double      $VAT            VAT percentile
    *   @param      double      $TTC            Price With VAT
    *   @param      string      $Code           Price Currency Code
    *   @param      string      $Symbol         Price Currency Symbol
    *   @param      string      $Name           Price Currency Name
    *   @return     array                      Contact Firstname, Lastname & Compagny Name
    */    
    public static function Price_Encode($HT, $VAT, $TTC=Null, $Code="",$Symbol="",$Name="")
    {
        //====================================================================//
        // Safety Checks 
        if ( !is_double($HT) && !is_double($TTC) ) {
            Splash::Log()->Err("ErrPriceInvalid",__FUNCTION__);
            return "Error Invalid Price";
        }
        if ( is_double($HT) && is_double($TTC) ) {
            Splash::Log()->Err("ErrPriceBothValues",__FUNCTION__);
            return "Error Too Much Input Values";
        }
        if ( !is_double($VAT) ) {
            Splash::Log()->Err("ErrPriceNoVATValue",__FUNCTION__);
            return "Error Invalid VAT";
        }
        if ( empty($Code) ) {
            Splash::Log()->Err("ErrPriceNoCurrCode",__FUNCTION__);
            return "Error no Currency Code";
        }
        //====================================================================//
        // Build Price Array
        $Price = array("vat" => $VAT, "code" => $Code,"symbol" => $Symbol,"name" => $Name);
        if ( !is_null($HT) ) {
            $Price["base"]  =    0;
            $Price["ht"]    =    $HT;
            $Price["tax"]   =    $HT * ( $VAT/100);
            $Price["ttc"]   =    $HT * (1 + $VAT/100);
        }
        else {
            $Price["base"]  =    1;
            $Price["ht"]    =    $TTC / (1 + $VAT/100);
            $Price["tax"]   =    $TTC - $Price["ht"];
            $Price["ttc"]   =    $TTC;
        }
        return $Price;
    }  
    
    /**
    *   @abstract   Read price without Vat 
    *   @param      array       $Price1          Price field Array
    *   @param      array       $Price2          Price field Array
    *   @return     boolean                      return true if Price are identical
    */    
    public static function Price_Compare($Price1,$Price2)
    {
        //====================================================================//
        // Check Both Prices are valid
        if ( !self::Price_isValid($Price1) || !self::Price_isValid($Price2))  {
            Splash::Log()->War(__FUNCTION__ . " : Given Prices are invalid" );
            if ( !self::Price_isValid($Price1) )  {
                Splash::Log()->www(__FUNCTION__ . " Price 1" , $Price1 );
            }
            if ( !self::Price_isValid($Price2) )  {
                Splash::Log()->www(__FUNCTION__ . " Price 2" , $Price2 );
            }
            return False;
        }
        //====================================================================//
        // Compare Base Price
        if ( ((bool) $Price1["base"]) != ((bool) $Price2["base"]) ) {
            return False;
        }
        //====================================================================//
        // Compare Price
        if ( $Price1["base"] ) {
            if ( !self::Float_Compare($Price1["ttc"],$Price2["ttc"]) ) {
                return False;
            }
        } else {
            if ( !self::Float_Compare($Price1["ht"],$Price2["ht"]) ) {
                return False;
            }
        }
        //====================================================================//
        // Compare VAT
        if ( !self::Float_Compare($Price1["vat"],$Price2["vat"]) ) {
            return False;
        }
        //====================================================================//
        // Compare Currency If Set on Both Sides
        if ( empty($Price1["code"]) ) {    return True;    }
        if ( empty($Price2["code"]) ) {    return True;    }
        if ( $Price1["code"] !== $Price2["code"] ) {
            return False;
        }
        //====================================================================//
        // Prices Are Identical
        return True;
    }    
    
    /**
     *  @abstract   Verify price field array
     *  
     *  @param      array       $Price          Price field definition Array
     *  
     *  @return     bool                     
     */    
    public static function Price_isValid($Price)
    {
        //====================================================================//
        // Check Contents Available
        if ( !is_array($Price) && !is_a($Price, "ArrayObject"))         {      return False;   }
        if ( !array_key_exists("base",$Price) )                         {      return False;   }
        if ( !array_key_exists("ht",$Price) )                           {      return False;   }
        if ( !array_key_exists("ttc",$Price) )                          {      return False;   }
        if ( !array_key_exists("vat",$Price) )                          {      return False;   }
        if ( !array_key_exists("tax",$Price) )                          {      return False;   }
        if ( !array_key_exists("symbol",$Price))                        {      return False;   }
        if ( !array_key_exists("code",$Price) )                         {      return False;   }
        if ( !array_key_exists("name",$Price) )                         {      return False;   }
        
        
        //====================================================================//
        // Check Contents Type
        if ( !empty($Price["ht"]) && !is_numeric($Price["ht"]) )         {      return False;   }
        if ( !empty($Price["ttc"]) && !is_numeric($Price["ttc"]) )       {      return False;   }
        if ( !empty($Price["vat"]) && !is_numeric($Price["vat"]) )       {      return False;   }

        return TRUE;
    }     
    
    
//====================================================================//
//  IMAGE FIELDS MANAGEMENT
//====================================================================//
    
    /**
     *  @abstract   Build a new image field array 
     * 
     *  @param      string      $Name           Image Name
     *  @param      string      $FileName       Image Filename with Extension
     *  @param      string      $Path           Image Full path on local system
     *  @param      string      $PublicUrl      Complete Public Url of this image if available
     * 
     *  @return     array                       Splash Image Array or False
     */    
    public static function Img_Encode($Name, $FileName, $Path, $PublicUrl = Null)
    {
        //====================================================================//
        // Safety Checks - Validate Inputs
        if ( !is_string($Name) || empty($Name) ) {
            return Splash::Log()->Err("ErrImgNoName",__FUNCTION__);
        }
        if ( !is_string($FileName) || empty($FileName) ) {
            return Splash::Log()->Err("ErrImgNoFileName",__FUNCTION__);
        }
        if ( !is_string($Path) || empty($Path) ) {
            return Splash::Log()->Err("ErrImgNoPath",__FUNCTION__);
        }

        $FullPath = $Path . $FileName;
        //====================================================================//
        // Safety Checks - Validate Image
        if ( !file_exists($FullPath)  ) {
            return Splash::Log()->Err("ErrImgNoPath",__FUNCTION__,$FullPath);
        }
        $ImageDims  = getimagesize($FullPath);
        if ( empty($ImageDims) ) {
            return Splash::Log()->Err("ErrImgNotAnImage",__FUNCTION__,$FullPath);
        }
        
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
        $Image["filename"]      = $FileName;
        //====================================================================//
        // Image Full Path
        $Image["path"]          = $Path;
        //====================================================================//
        // Image Publics Url
        $Image["url"]           = $PublicUrl;
        //====================================================================//
        // ADD COMPUTED INFOS
        //====================================================================//
        // Images Informations
        $Image["width"]         = $ImageDims[0];
        $Image["height"]        = $ImageDims[1];
        $Image["md5"]           = md5_file($FullPath);
        $Image["size"]          = filesize($FullPath);        
        
        return $Image;
    }  
    
//====================================================================//
// *******************************************************************//
//  VARIOUS FUNCTIONS
// *******************************************************************//
//====================================================================//

    /**
     *      @abstract   Compare Two Float Value and 
     * 
     *      @return     float   $float1         Float Value To Compare
     *      @return     float   $float2         Float Value To Compare
     * 
     *      @return     bool                    true if equal, else false
     */
    public static function Float_Compare($float1,$float2)
    {   
        $epsilon = 1E-6;
        if ( abs($float1 - $float2) < $epsilon )    { return true; }
        else                                        { return false; }
    }        
    
    /**
     *  @abstract     Reading of a Single Bit inside a Field
     * 
     *  @param        int       $Data                   Input Data
     *  @param        int       $Position               Bit position (Starting form 0)
     * 
     *  @return       bool
     */
    protected function Bitwise_Read($Data,$Position) {
        return (bool) ( $Data >> $Position ) & 1;
    }  
    
    /**
     *  @abstract     Writting of a Single Bit inside a Field
     * 
     *  @param        int       $Data                   Input Data
     *  @param        int       $Position               Bit position (Starting form 0)
     *  @param        bool      $Value                  New Bit Value
     * 
     *  @return       bool
     */
    protected function Bitwise_Write($Data,$Position,$Value) {
        if ($Value) {
            return $Data | ( 1 << $Position);
        } else {
            return $Data & ~ ( 1 << $Position);
        }
    }       
    
}

?>