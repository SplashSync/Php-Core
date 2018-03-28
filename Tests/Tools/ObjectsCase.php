<?php

namespace Splash\Tests\Tools;

use Splash\Client\Splash;
use Splash\Server\SplashServer;
use Splash\Tests\Tools\Fields\ooobjectid as ObjectId;

/**
 * @abstract    Splash Test Tools - Objects Test Case Base Class
 *
 * @author SplashSync <contact@splashsync.com>
 */
class ObjectsCase extends BaseCase
{

    /*
     * @abstract    List of Created & Tested Object used to delete if test failled.
     */
    private $CreatedObjects  =   array();
    
    /**
     * Fields Classes Name Prefix
     * @var string
     */
    const       CLASS_PREFIX        =   'Splash\Tests\Tools\Fields\oo';
    
    /**
     * Formater Fake Field Generator Options
     * @var array
     */
    private $settings = array(
        
            //==============================================================================
            //  List generation
            'ListItems'                 =>  2,               // Number of Items to Add in Lists

            //==============================================================================
            //  Double & Prices Fields
            "DoublesPrecision"          =>  6,              // Default Doubles Compare Precision (Number of Digits)
        
            //==============================================================================
            //  Currency Fields
            "Currency"                  =>  "EUR",          // Default Currency
            
            //==============================================================================
            //  Price Fields
            "VAT"                       =>  20,              // Default Vat Rate
            "PriceBase"                 =>  "HT",            // Default Price base
            "PricesPrecision"           =>  6,               // Default Prices Compare Precision (Number of Digits)
            
            //==============================================================================
            //  Url Generator Parameters
            "Url_Prefix"                => "",               // Add a prefix to generated Url (i.e: http://)
            "Url_Sufix"                 => ".splashsync.com",// Add a sufix to generated Url
            
            //==============================================================================
            //  Multilanguage Fields
            "Langs"                     =>  array(          // Available Languages for Multilang Fields
                "en_US",
                "fr_FR",
                "fr_BE",
                "fr_CA",
                ),
            
            //==============================================================================
            //  Country Fields
            "Country"                    =>  array(          // Defaults State Iso Codes
                "US",
                "FR",
                "BE",
                "CA",
                ),
            
            //==============================================================================
            //  State Fields
            "States"                    =>  array(          // Defaults State Iso Codes
                "CA",
                "FL"
                ),
           
            //==============================================================================
            //  Files Fields
            "Files"                    =>  array(          // Defaults Raw Files
                "fake-file1.pdf",
                "fake-file2.pdf",
                "fake-file3.pdf",
                "fake-file4.pdf",
                ),
            
            //==============================================================================
            //  Images Fields
            "Images"                    =>  array(          // Defaults Image Files
                "fake-image1.jpg",
                "fake-image2.jpg",
                "fake-image3.jpg",
                "fake-image4.jpg",
                ),
            
//            //==============================================================================
//            //  Objects Id Fields
//            //  Default is An Empty List To be completed by User Before Generation
//            "Objects"                   =>  array(),
    );

    protected function loadLocalTestParameters()
    {
        //====================================================================//
        // Safety Check
        if (is_null(Splash::Local()) || !method_exists(Splash::Local(), "TestParameters")) {
            return;
        }
        //====================================================================//
        // Read Local Parameters
        $LocalTestSettings  =   Splash::Local()->TestParameters();
        
        //====================================================================//
        // Validate Local Parameters
        if (!Splash::Validate()->isValidLocalTestParameterArray($LocalTestSettings)) {
            return;
        }
        //====================================================================//
        // Import Local Parameters
        foreach ($LocalTestSettings as $key => $value) {
            $this->settings[$key]   =   $value;
        }
    }
    
    protected function loadLocalTestSequence($Sequence)
    {
        //====================================================================//
        // Check if Local Tests Sequences are defined
        if (is_null(Splash::Local()) || !method_exists(Splash::Local(), "TestSequences")) {
            return;
        }
        //====================================================================//
        // Setup Test Sequence
        Splash::Local()->TestSequences($Sequence);
    }
    
    protected function setUp()
    {
        parent::setUp();
        
        //====================================================================//
        // BOOT or REBOOT MODULE
        Splash::Reboot();
        
        //====================================================================//
        // FAKE SPLASH SERVER HOST URL
        Splash::Configuration()->WsHost = "No.Commit.allowed.not";
        
        //====================================================================//
        // Load Module Local Configuration (In Safe Mode)
        //====================================================================//
        $this->loadLocalTestParameters();
    }
    
    /**
     * @abstract        Verify Last Commit is Valid and Conform to Expected
     *
     * @param string    $Action         Expected Action
     * @param string    $ObjectType     Expected Object Type
     * @param string    $ObjectId       Expected Object Id
     */
    public function assertIsLastCommited($Action, $ObjectType, $ObjectId)
    {
        //====================================================================//
        //   Verify Object Change Was Commited
        $this->assertNotEmpty(Splash::$Commited, "No Object Change Commited by your Module. Please check your triggers.");
        
        //====================================================================//
        //   Get Last Commited
        $LastCommit = array_pop(Splash::$Commited);
        
        //====================================================================//
        //   Check Object Type is OK
        $this->assertEquals(
                $LastCommit->type,
                $ObjectType,
                "Change Commit => Object Type is wrong. (Expected " . $ObjectType . " / Given " . $LastCommit->type
        );
        
        //====================================================================//
        //   Check Object Action is OK
        $this->assertEquals(
                $LastCommit->action,
                $Action,
                "Change Commit => Change Type is wrong. (Expected " . $Action . " / Given " . $LastCommit->action
        );
        
        //====================================================================//
        //   Check Object Id value Format
        $this->assertTrue(
                is_scalar($LastCommit->id) || is_array($LastCommit->id) || is_a($LastCommit->id, "ArrayObject"),
                "Change Commit => Object Id Value is in wrong Format. (Expected String or Array of Strings. / Given " . print_r($LastCommit->id, true)
        );
        
        //====================================================================//
        //   If Commited an Array of Ids
        if (is_array($LastCommit->id) || is_a($LastCommit->id, "ArrayObject")) {
            //====================================================================//
            //   Check each Object Ids
            foreach ($LastCommit->id as $Id) {
                $this->assertTrue(
                        is_scalar($Id),
                        "Change Commit => Object Id Array Value is in wrong Format. (Expected String or Integer. / Given " . print_r($Id, true)
                );
            }
            //====================================================================//
            //   Extract First Object Id
            $FirstId = array_shift($LastCommit->id);
            //====================================================================//
            //   Verify First Object Id is OK
            $this->assertEquals(
                    $FirstId,
                    $ObjectId,
                    "Change Commit => Object Id is wrong. (Expected " . $ObjectId . " / Given " . $FirstId
            );
        } else {
            //====================================================================//
            //   Check Object Id is OK
            $this->assertEquals(
                    $LastCommit->id,
                    $ObjectId,
                    "Change Commit => Object Id is wrong. (Expected " . $ObjectId . " / Given " . $LastCommit->id
            );
        }
        
        //====================================================================//
        //   Check Infos are Not Empty
        $this->assertNotEmpty($LastCommit->user, "Change Commit => User Name is Empty");
        $this->assertNotEmpty($LastCommit->comment, "Change Commit => Action Comment is Empty");
    }

    /**
     * @abstract        Verify First Commit is Valid and Conform to Expected
     *
     * @param string    $Action         Expected Action
     * @param string    $ObjectType     Expected Object Type
     * @param string    $ObjectId       Expected Object Id
     */
    public function assertIsFirstCommited($Action, $ObjectType, $ObjectId)
    {
        //====================================================================//
        //   Verify Object Change Was Commited
        $this->assertNotEmpty(Splash::$Commited, "No Object Change Commited by your Module. Please check your triggers.");
        
        //====================================================================//
        //   Get Last Commited
        $LastCommit = array_shift(Splash::$Commited);
        
        //====================================================================//
        //   Check Object Type is OK
        $this->assertEquals(
                $LastCommit->type,
                $ObjectType,
                "Change Commit => Object Type is wrong. (Expected " . $ObjectType . " / Given " . $LastCommit->type
        );
        
        //====================================================================//
        //   Check Object Action is OK
        $this->assertEquals(
                $LastCommit->action,
                $Action,
                "Change Commit => Change Type is wrong. (Expected " . $Action . " / Given " . $LastCommit->action
        );
        
        //====================================================================//
        //   Check Object Id value Format
        $this->assertTrue(
                is_scalar($LastCommit->id) || is_array($LastCommit->id) || is_a($LastCommit->id, "ArrayObject"),
                "Change Commit => Object Id Value is in wrong Format. (Expected String or Array of Strings. / Given " . print_r($LastCommit->id, true)
        );
        
        //====================================================================//
        //   If Commited an Array of Ids
        if (is_array($LastCommit->id) || is_a($LastCommit->id, "ArrayObject")) {
            //====================================================================//
            //   Check each Object Ids
            foreach ($LastCommit->id as $Id) {
                $this->assertTrue(
                        is_scalar($Id),
                        "Change Commit => Object Id Array Value is in wrong Format. (Expected String or Integer. / Given " . print_r($Id, true)
                );
            }
            //====================================================================//
            //   Extract First Object Id
            $FirstId = array_shift($LastCommit->id);
            //====================================================================//
            //   Verify First Object Id is OK
            $this->assertEquals(
                    $FirstId,
                    $ObjectId,
                    "Change Commit => Object Id is wrong. (Expected " . $ObjectId . " / Given " . $FirstId
            );
        } else {
            //====================================================================//
            //   Check Object Id is OK
            $this->assertEquals(
                    $LastCommit->id,
                    $ObjectId,
                    "Change Commit => Object Id is wrong. (Expected " . $ObjectId . " / Given " . $LastCommit->id
            );
        }
        
        //====================================================================//
        //   Check Infos are Not Empty
        $this->assertNotEmpty($LastCommit->user, "Change Commit => User Name is Empty");
        $this->assertNotEmpty($LastCommit->comment, "Change Commit => Action Comment is Empty");
    }
    
    /**
     * @abstract        Set Current Tested Object to Filter Objects List upon Fake ObjectId Creation
     *
     * @param string    $ObjectType     Expected Object Type
     * @param string    $ObjectId       Expected Object Id
     */
    protected function setCurrentObject($ObjectType, $ObjectId)
    {
        $this->settings["CurrentType"]  =   $ObjectType;
        $this->settings["CurrentId"]    =   $ObjectId;
    }
    //==============================================================================
    //      VALIDATION FUNCTIONS
    //==============================================================================

    /**
     *   @abstract   Verify this parameter is a valid sync data type
     *   @param      string      $In         Data Type Name String
     *   @return     int         $result     0 if KO, Field Full Class Name if OK
     */
    public static function isValidType($In)
    {
        //====================================================================//
        // Safety Check
        if (empty($In)) {
            return false;
        }
        //====================================================================//
        // Detects Lists Fields
        //====================================================================//
        $list = self::isListField($In);
        if ($list != false) {
            $In = $list["fieldname"];
        }
        //====================================================================//
        // Detects Id Fields
        //====================================================================//
        $id = self::isIdField($In);
        if ($id != false) {
            $In = "objectid";
        }
        
        //====================================================================//
        // Verify Single Data Type is Valid
        //====================================================================//

        //====================================================================//
        // Build Class Full Name
        $ClassName = self::CLASS_PREFIX .  $In;
        
        //====================================================================//
        // Build New Entity
        if (class_exists($ClassName)) {
            return  $ClassName;
        }

        return false;
    }

    /**
     *   @abstract   Check if this id is a list identifier & return decoded array if ok
     *   @param      string  $In         Data Type Name String
     *   @return     int     $result     0 if KO, 1 if OK
     */
    public static function isListField($In)
    {
        //====================================================================//
        // Safety Check
        if (empty($In)) {
            return false;
        }
        //====================================================================//
        // Detects Lists
        $list = explode(LISTSPLIT, $In);
        if (is_array($list) && (count($list)==2)) {
            //====================================================================//
            // If List Detected, Prepare Field List Information Array
            return array("fieldname" => $list[0],"listname" => $list[1]);
        }
        return false;
    }

    /**
     *      @abstract   Identify if field is Object Identifier Data & Decode Field
     *
     *      @param      string       $In             Id Field String
     *
     *      @return     array       $result         0 if KO or Exploded Field Array
     */
    public static function isIdField($In)
    {
        //====================================================================//
        // Safety Check
        if (empty($In)) {
            return false;
        }
        
        //====================================================================//
        // Detects ObjectId
        $list = explode(IDSPLIT, $In);
        if (is_array($list) && (count($list)==2)) {
            //====================================================================//
            // If List Detected, Prepare Field List Information Array
            $Out["ObjectId"]        = $list[0];
            $Out["ObjectType"]      = $list[1];
            return $Out;
        }
        return false;
    }
    
    /**
     *   @abstract   Verify Data a valid Raw field data
     *   @param      mixed   $Data       Object Field Data
     *   @param      string  $Type       Object Field Type
     *   @return     int     $result     0 if KO, 1 if OK
     */
    public static function isValidData($Data, $Type)
    {
        //====================================================================//
        // Verify Field Type is Valid
        $ClassName = self::isValidType($Type);
        if ($ClassName == false) {
            return false;
        }
        
        //====================================================================//
        // Verify Single Field Data Type is not Null
        if (is_null($Data)) {
            return true;
        }

        //====================================================================//
        // Verify Single Field Data Type is Valid
        return $ClassName::validate($Data);
    }
    
    /**
     *   @abstract   Verify Data a valid field data
     *   @param      mixed   $Data       Object Field Data
     *   @param      string  $Id         Object Field Identifier
     *   @param      string  $Type       Object Field Type
     *   @return     int     $result     0 if KO, 1 if OK
     */
    public function isValidFieldData($Data, $Id, $Type)
    {
        //====================================================================//
        // Safety Check
        $this->assertNotEmpty($Data, "Field Data Block is Empty");
        $this->assertNotEmpty($Id, "Field Id is Empty");
        $this->assertNotEmpty($Type, "Field Type Name is Empty");
        
        //====================================================================//
        // Detects Lists Fields
        $List       = self::isListField($Id);
        if ($List) {
            //====================================================================//
            // Verify List Field Data
            return $this->isValidListFieldData($Data, $Id, $Type);
        }
        
        //====================================================================//
        // Verify Field is in Data Response
        $this->assertArrayHasKey($Id, $Data, "Field '" . $Id . "' is not defined in returned Data Block.");
        
        //====================================================================//
        // Verify Single Field Data Type is not Null
        if (is_null($Data[$Id])) {
            return;
        }
        
        //====================================================================//
        // Verify Raw Field Data
        $this->assertTrue(
                self::isValidData($Data[$Id], $Type),
                $Id . " => Field Raw Data is not a valid " . $Type .  ". (" . print_r($Data[$Id], true) . ")"
            );
    }
        
    /**
    *   @abstract   Verify Data a valid list field data
    *   @param      mixed   $Data       Object Field Data
    *   @param      string  $Id         Object Field Identifier
    *   @param      string  $Type       Object Field Type
    *   @return     int     $result     0 if KO, 1 if OK
    */
    public function isValidListFieldData($Data, $Id, $Type)
    {
        $ListId     = self::isListField($Id);
        $ListType   = self::isListField($Type);
        if (!$ListId) {
            return false;
        }
        
        //====================================================================//
        // Verify List is in Data Response
        $this->assertArrayHasKey($ListId["listname"], $Data, "List '" . $ListId["listname"] . "' is not defined in returned Data Block.");
        
        //====================================================================//
        // Verify Field Type is List Type Identifier
        $this->assertEquals(
            $ListType["listname"],
            SPL_T_LIST,
                "List Field Type Must match Format 'type'@list. (Given " . print_r($Type, true) . ")"
            );
        
        //====================================================================//
        // Verify Field Type is Valid Splahs Field type
        $this->assertNotEmpty(
                self::isValidType($ListType["fieldname"]),
                "List Field Type is not a valid Splash Field Type. (Given " . print_r($ListType["fieldname"], true) . ")"
            );
        
        $ListData = $Data[$ListId["listname"]];
        //====================================================================//
        // Verify if Field Data is Null
        if (empty($ListData)) {
            return true;
        }
        
        //====================================================================//
        // Verify if Field Data is an Array
        $this->assertTrue(
                is_array($ListData) || is_a($ListData, "ArrayObject"),
                "List Field '" . $ListId["listname"] . "' is not of Array Type. (Given " . print_r($ListData, true). ")"
            );
        
        //====================================================================//
        // Verify all List Data Are Valid
        foreach ($ListData as $Value) {
            $this->isValidFieldData($Value, $ListId["fieldname"], $Type);
        }
        return true;
    }
    
    //====================================================================//
    //   Data Provider Functions
    //====================================================================//
    
    public function ObjectTypesProvider()
    {
        $Result = array();
        
        self::setUp();

        //====================================================================//
        // Check if Local Tests Sequences are defined
        if (!is_null(Splash::Local()) && method_exists(Splash::Local(), "TestSequences")) {
            $Sequences  =   Splash::Local()->TestSequences("List");
        } else {
            $Sequences  =   array( 1 => "None");
        }
        
        //====================================================================//
        //   For Each Test Sequence
        foreach ($Sequences as $Sequence) {
            $this->loadLocalTestSequence($Sequence);
            
            //====================================================================//
            //   For Each Object Type
            foreach (Splash::Objects() as $ObjectType) {
                //====================================================================//
                //   If Object Type Is Disabled Type  =>> Skip
                if (Splash::Object($ObjectType)->getIsDisabled()) {
                    continue;
                }
                //====================================================================//
                //   Add Object Type to List
                $Result[] = array($Sequence, $ObjectType);
            }
        }
        
        self::tearDown();
        
        return $Result;
    }

    public function ObjectFieldsProvider()
    {
        $Result = array();
        
        self::setUp();
        
        //====================================================================//
        // Check if Local Tests Sequences are defined
        if (!is_null(Splash::Local()) && method_exists(Splash::Local(), "TestSequences")) {
            $Sequences  =   Splash::Local()->TestSequences("List");
        } else {
            $Sequences  =   array( 1 => "None");
        }
        
        //====================================================================//
        //   For Each Test Sequence
        foreach ($Sequences as $Sequence) {
            $this->loadLocalTestSequence($Sequence);
            //====================================================================//
            //   For Each Object Type
            foreach (Splash::Objects() as $ObjectType) {
                //====================================================================//
                //   If Object Type Is Disabled Type  =>> Skip
                if (Splash::Object($ObjectType)->getIsDisabled()) {
                    continue;
                }
                //====================================================================//
                //   For Each Field Type
                foreach (Splash::Object($ObjectType)->Fields() as $Field) {
                    $Result[] = array($Sequence, $ObjectType, $Field);
                }
            }
        }
        
        self::tearDown();
        
        return $Result;
    }
    
    //==============================================================================
    //      FAKE DATA GENERATORS
    //==============================================================================
    
    /**
     *   @abstract   Generate Fake Object Fields List
     *
     *   @param      string     $ObjectType     Object Type Name
     *   @param      array      $FieldsList     Object Field Ids List
     *   @param      bool       $Associate      Include Associated Fields
     *
     *   @return     array      $Out            Array of Fields
     */
    public function fakeFieldsList($ObjectType, $FieldsList = false, $Associate = false)
    {
        //====================================================================//
        // Safety Check => $ObjectType is a valid
        $this->assertTrue(in_array($ObjectType, Splash::Objects()), "Invalid Object Type Name. (" . $ObjectType . ")");

        //====================================================================//
        // Create Empty Object Data Array
        $Out    = array();
        $Write  = false;
        
        //====================================================================//
        // Load Object Fields Definition
        $Fields = Splash::Object($ObjectType)->Fields();
        if (empty($Fields)) {
            return $Out;
        }
        
        //====================================================================//
        // Generate Fields Data
        foreach ($Fields as $Field) {
            
            //====================================================================//
            // Check if Fields is Writable
            if (!$Field->write) {
                continue;
            }
            $Write = true;

            //====================================================================//
            // Check if Fields is Needed
            //====================================================================//

            $Needed = false;
            //====================================================================//
            // Required Field
            if ($Field->required) {
                $Needed = true;
            }
            //====================================================================//
            // If NO Fields List is Given => Select All Write Fields
            if (($FieldsList == false) || !is_array($FieldsList)) {
                $Needed = true;
            } else {
                //====================================================================//
                // Field is in Requested List
                if (in_array($Field->id, $FieldsList)) {
                    $Needed = true;
                }
            }
            if (!$Needed) {
                continue;
            }
                
            //====================================================================//
            // Add Fields to List
            $Out[$Field->id] = $Field;
        }
        
        //====================================================================//
        // No Associated Fields
        if (!$Associate) {
            return $Out;
        }
        
        //====================================================================//
        // Add Associated Fields to List
        foreach ($Out as $OutField) {
            
            //====================================================================//
            // No Associated Field
            if (empty($OutField->asso)) {
                continue;
            }
            //====================================================================//
            // For Associated Fields
            foreach ($Fields as $Field) {
                if (in_array($Field->id, $OutField->asso)) {
                    $Out[$Field->id] = $Field;
                }
            }
        }
        
        return $Out;
    }
    
    /**
     *   @abstract   Create Fake/Dummy Object Data
     *
     *   @param      array   $FieldsList     Object Field List
     *
     *   @return     int     $result     0 if KO, 1 if OK
     */
    public function fakeObjectData($FieldsList)
    {
        //====================================================================//
        // Create Dummy Data Array
        $Out = array();
        if (empty($FieldsList)) {
            return $Out;
        }
        
        //====================================================================//
        // Create Dummy Fields Data
        foreach ($FieldsList as $Field) {
            
            //====================================================================//
            // Generate Single Fields Dummy Data (is Not a List Field)
            if (!self::isListField($Field->id)) {
                $Out[$Field->id] = self::fakeFieldData($Field->type, $Field->choices, $Field->options);
                continue;
            }
            
            //====================================================================//
            // Generate Dummy List  Data
            $List       =   self::isListField($Field->id);
            $ListName   =   $List["listname"];
            $FieldName  =   $List["fieldname"];
            $ListData   =   self::fakeListData($Field);
            //====================================================================//
            // Create List
            if (!array_key_exists($ListName, $Out)) {
                $Out[$ListName] = array();
            }
            //====================================================================//
            // Parse Data in List
            foreach ($ListData as $Key => $Data) {
                if (!array_key_exists($Key, $Out[$ListName])) {
                    $Out[$ListName][$Key] = array();
                }
                $Out[$ListName][$Key][$FieldName] = $Data[$FieldName];
            }
        }
        return $Out;
    }
    
    /**
     *   @abstract   Create Fake/Dummy Object List Data
     *
     *   @param      array   $Field          Object Field Definition
     *
     *   @return     int     $result     0 if KO, 1 if OK
     */
    public function fakeListData($Field)
    {
        //====================================================================//
        // Read Number of Items to Put in Lists
        $NbItems =  $this->settings["ListItems"]?$this->settings["ListItems"]:2;
        //====================================================================//
        // Parse List Identifiers
        $List   =   self::isListField($Field->id);
        $Type   =   self::isListField($Field->type);
        
        //====================================================================//
        // Generate Unik Dummy Fields Data
        $ListData = array();
        while (count($ListData) < $NbItems) {
            $Data           =   self::fakeFieldData($Type["fieldname"], $Field->choices, $Field->options);
            $Md5            =   md5(serialize($Data));
            $ListData[$Md5] =   $Data;
        }

        //====================================================================//
        // Create Dummy List Data
        $Out = array();
        
        //====================================================================//
        // Create Dummy Fields Data
        for ($i = 0; $i < $NbItems; $i++) {
            $Out[][$List["fieldname"]] = array_shift($ListData);
        }
        
        return $Out;
    }
    
    /**
     *   @abstract   Create Fake Field data
     *
     *   @param      string  $Type       Object Field Type
     *   @param      array   $Choices    Object Field Possible Values
     *   @param      array   $Options     Object Field Values Options
     *
     *   @return     int     $result     0 if KO, 1 if OK
     */
    public function fakeFieldData($Type, $Choices = null, $Options = array())
    {
        //====================================================================//
        // Safety Check
        if (empty($Type)) {
            return false;
        }
        //====================================================================//
        // Verify Field Type is Valid
        $ClassName = self::isValidType($Type);
        if ($ClassName == false) {
            return false;
        }
        //====================================================================//
        // Detects Id Fields    => Cannot Generate Fake for Id Fields Here...
        if (($id = self::isIdField($Type))) {
            return $ClassName::fake($id["ObjectType"], array_merge_recursive($this->settings, $Options));
        }
        
        //====================================================================//
        // Take Values From Given Choices
        if (!empty($Choices)) {
            $Index = mt_rand(0, count($Choices) - 1);
            if (isset($Choices[$Index]["key"]) && ($Type == SPL_T_VARCHAR)) {
                return (string) $Choices[$Index]["key"];
            } elseif (isset($Choices[$Index]["key"])) {
                return $Choices[$Index]["key"];
            }
        }
        
        //====================================================================//
        // Generate Single Field Data Type is Valid
        return $ClassName::fake(array_merge_recursive($this->settings, $Options));
    }
    
    //==============================================================================
    //      FIELDS LIST FUNCTIONS
    //==============================================================================
    
    /**
     *   @abstract   Filter a Fields List to keap only given Fields Ids
     *
     *   @param      array      $FieldsList     Object Field List
     *   @param      array      $Filters        Array of Fields Ids
     *
     *   @return     array
     */
    public static function filterFieldList($FieldsList, $Filters = array())
    {
        $Result =   array();
        
        foreach ($FieldsList as $Field) {
            if (in_array($Field->id, $Filters)) {
                $Result[] = $Field;
            }
        }
        
        return $Result;
    }
    
    /**
     *   @abstract   Find a Field Definition in List by Id
     *
     *   @param      array      $FieldsList     Object Field List
     *   @param      array      $FieldId        Field Id
     *
     *   @return     array
     */
    public static function findField($FieldsList, $FieldId)
    {
        $Fields = self::filterFieldList($FieldsList, $FieldId);
        
        if (count($Fields) != 1) {
            return null;
        }
                
        return array_shift($Fields);
    }

    /**
     *   @abstract   Redure a Fields List to an Array of Field Ids
     *
     *   @param      array      $FieldsList     Object Field List
     *   @param      bool       $isRead         Filter non Readable Fields
     *   @param      bool       $isWrite        Filter non Writable Fields
     *
     *   @return     array
     */
    public static function reduceFieldList($FieldsList, $isRead = false, $isWrite = false)
    {
        $Result =   array();
       
        foreach ($FieldsList as $Field) {
            
            //==============================================================================
            //      Filter Non-Readable Fields
            if ($isRead && !$Field->read) {
                continue;
            }
            //==============================================================================
            //      Filter Non-Writable Fields
            if ($isWrite && !$Field->write) {
                continue;
            }
            $Result[] = $Field->id;
        }
            
        return $Result;
    }
    
    //==============================================================================
    //      OBJECTS DATA BLOCKS FUNCTIONS
    //==============================================================================
        
    /**
     *   @abstract   Extract Raw Field Data from an Object Data Block
     *
     *   @param      array      $DataBlock          Object Data Block
     *   @param      string      $Filter            Single Fields Id
     *
     *   @return     array
     */
    public static function extractRawData($DataBlock, $Filter)
    {
        $FilteredData   =   self::filterData($DataBlock, array($Filter));
        
        //====================================================================//
        // Explode List Field Id
        $List       =   Field::isListField($Filter);
        
        //====================================================================//
        // Simple Single Field
        if (!$List) {
            if (isset($FilteredData[$Filter])) {
                return $FilteredData[$Filter];
            }
            
            //====================================================================//
        // List Field
        } else {
            //====================================================================//
            // Check List Exists
            if (!array_key_exists($List["listname"], $FilteredData)) {
                return null;
            }
            
            //====================================================================//
            // Parse Raw List Data
            $Result = array();
            foreach ($FilteredData[$List["listname"]] as $Key => $ListItem) {
                $Result[$Key]   =   $ListItem[$List["fieldname"]];
            }
            return $Result;
        }
        
        //====================================================================//
        // Field Not Received or is Empty
        return null;
    }
    
    /**
     *   @abstract   Filter a Object Data Block to keap only given Fields
     *
     *   @param      array      $DataBlock      Object Data Block
     *   @param      array      $Filters        Array of Fields Ids
     *
     *   @return     array
     */
    public static function filterData($DataBlock, $Filters = array())
    {
        $Result         =   array();
        $ListFilters    =   array();
        
        //====================================================================//
        // Process All Single Fields Ids & Store Sorted List Fields Ids
        foreach ($Filters as $FieldId) {
            
            //====================================================================//
            // Explode List Field Id
            $List       =   self::isListField($FieldId);
            //====================================================================//
            // Single Field Data Type
            if ((!$List) && (array_key_exists($FieldId, $DataBlock))) {
                $Result[$FieldId] = $DataBlock[$FieldId];
            } elseif (!$List) {
                continue;
            }
            //====================================================================//
            // List Field Data Type
            $ListName   =   $List["listname"];
            $FieldName  =   $List["fieldname"];
            //====================================================================//
            // Check List Data are Present in Block
            if (!array_key_exists($ListName, $DataBlock)) {
                continue;
            }
            //====================================================================//
            // Create List
            if (!array_key_exists($ListName, $ListFilters)) {
                $ListFilters[$ListName] = array();
            }
            $ListFilters[$ListName][] = $FieldName;
        }
        
        //====================================================================//
        // Process All List Fields Ids Filters
        foreach ($ListFilters as $ListName => $ListFilters) {
            $Result[$ListName] = self::filterListData($DataBlock[$ListName], $ListFilters);
        }
        
        return $Result;
    }
    
    /**
     *   @abstract   Filter a Object List Data Block to keap only given Fields
     *
     *   @param      array      $ListBlock  Object Data Block
     *   @param      array      $Filters    Array of Fields Ids
     *
     *   @return     array
     */
    public static function filterListData($ListBlock, $Filters = array())
    {
        $Result =   array();
        
        foreach ($ListBlock as $ItemBlock) {
            $FilteredItems = array();
            
            //====================================================================//
            // Search for Field in Item Block
            if (!is_array($ItemBlock) && !is_a($ItemBlock, "ArrayObject")) {
                dump($ListBlock);
                dump($ItemBlock);
                
                continue;
            }
            
            //====================================================================//
            // Search for Field in Item Block
            foreach ($Filters as $FieldId) {
                if (array_key_exists($FieldId, $ItemBlock)) {
                    $FilteredItems[$FieldId] = $ItemBlock[$FieldId];
                }
            }
            
            $Result[] = $FilteredItems;
        }
        
        return $Result;
    }
    
    /**
     *  @abstract   Normalize An Object Data Block (ie: before Compare)
     *
     *  @param      mixed       $array      Input Array
     *
     *  @return     array                   Sorted Array
     */
    public static function Normalize(&$In)
    {
       
        //==============================================================================
        //      Convert ArrayObjects To Simple Array
        if (is_a($In, "ArrayObject")) {
            $In = $In->getArrayCopy();
            //==============================================================================
            // Normalize Contents
            self::Normalize($In);
            
        //==============================================================================
        // Normalize Array Contents
        } elseif (is_array($In)) {
            foreach ($In as &$value) {
                self::Normalize($value);
            }
            
            //==============================================================================
        // Normalize Bool as Strings
        } elseif (is_bool($In)) {
            $In = $In?"1":"0";
            
        //==============================================================================
        // Normalize Numbers as Strings
        } elseif (is_numeric($In)) {
            $In = strval($In);
        }
        
        return $In;
    }
    
    /**
    *   @abstract   kSort of An Object Data Block (ie: before Compare)
    *
    *   @param      array       $array      Input Array
    *
    *   @return     array                   Sorted Array
    */
    public static function Sort(&$In)
    {
        if (!is_array($In)) {
            return $In;
        }
            
        //==============================================================================
        // Sort All Sub-Contents
        foreach ($In as &$value) {
            if (is_array($value)) {
                self::Sort($value);
            }
        }
        return ksort($In);
    }
    
    /**
     * @abstract    Check Two Data Blocks Have Similar Data
     *
     * @param   array   $Block1             Raw Data to Compare
     * @param   array   $Block2             Raw Data to Compare
     * @param   object  $TestController     Provide PhpUnit Test Controller Class to Use PhpUnit assertions
     * @param   string  $Comment            Comment on this Test
     *
     * @return bool
     */
    public function compareRawData($Block1, $Block2, $TestController = null, $Comment = null)
    {
        //====================================================================//
        // Filter ArrayObjects
        if (is_a($Block1, "ArrayObject")) {
            $Block1 = $Block1->getArrayCopy();
        }
        if (is_a($Block2, "ArrayObject")) {
            $Block2 = $Block2->getArrayCopy();
        }
        
        //====================================================================//
        // Remove Id Data if Present on Block
        if (is_array($Block1)) {
            unset($Block1['id']);
        }
        if (is_array($Block2)) {
            unset($Block2['id']);
        }
        
        //====================================================================//
        // Normalize Data Blocks
        $this->Normalize($Block1);
        $this->Normalize($Block2);
        //====================================================================//
        // If Test Controller Given
        if ($TestController) {
            $TestController->assertEquals($Block1, $Block2, $Comment);
            return true;
        }
            
        //====================================================================//
        // If NO Test Controller Given => Do Raw Array Compare
        //====================================================================//
        
        //====================================================================//
        // Sort Data Blocks
        $this->Sort($Block1);
        $this->Sort($Block2);

        $Serialized1 = serialize($Block1);
        $Serialized2 = serialize($Block2);
        
        return ($Serialized1 === $Serialized2);
    }
    
    /**
     * @abstract    Check Two Object Data Blocks using Field's Compare functions
     *
     * @param   array   $Fields             Array of OpenObject Fields Definitions
     * @param   array   $Block1             Raw Data to Compare
     * @param   array   $Block2             Raw Data to Compare
     * @param   string  $Comment            Comment on this Test
     *
     * @return bool
     */
    public function compareDataBlocks($Fields, $Block1, $Block2, $Comment = null)
    {

        //====================================================================//
        // For Each Object Fields
        foreach ($Fields as $Field) {

            //====================================================================//
            // Extract Field Data
            $Data1        =  $this->filterData($Block1, array($Field->id));
            $Data2        =  $this->filterData($Block2, array($Field->id));

            //dump($Data1);
            //dump($Data2);
            //====================================================================//
            // Compare List Data
            $FieldType      =  self::isListField($Field->type);
            if ($FieldType) {
                $Result = $this->compareListField($FieldType["fieldname"], $Field->id, $Data1, $Data2, $Comment . "->" . $Field->id);
            }
            //====================================================================//
            // Compare Single Fields
            else {
                $Result = $this->compareField($Field->type, $Data1[$Field->id], $Data2[$Field->id], $Comment . "->" . $Field->id);
            }
                
            //====================================================================//
            // If Compare Failled => Return Fail Code
            if ($Result !== true) {
                return $Result;
            }
        }
        
        return true;
    }
    
    /**
     * @abstract    Check Two Object Data Blocks using Field's Compare functions
     *
     * @param   string  $FieldType          Field Type Name
     * @param   array   $Block1             Raw Data to Compare
     * @param   array   $Block2             Raw Data to Compare
     * @param   string  $Comment            Comment on this Test
     *
     * @return string   error / success translator string for debugger
     */
    private function compareField($FieldType, $Block1, $Block2, $Comment = null)
    {
        //dump($FieldType);
        //dump($Block1);
        
        //====================================================================//
        // Build Full ClassName
        if (ObjectId::decodeIdField($FieldType)) {
            $ClassName      = self::isValidType("objectid");
        } else {
            $ClassName      = self::isValidType($FieldType);
        }
        
        //====================================================================//
        // Verify Class has its own Validate & Compare Function*
        $this->assertTrue(method_exists($ClassName, "validate"), "Field of type " . $FieldType . " has no Validate Function.");
        $this->assertTrue(method_exists($ClassName, "compare"), "Field of type " . $FieldType . " has no Compare Function.");
        
        //====================================================================//
        // Validate Data Using Field Type Validator
        $this->assertTrue(
                $ClassName::validate($Block1),
                $Comment . " Source Data is not a valid " . $FieldType . " Field Data Block (" . print_r($Block1, 1) . ")"
        );
        $this->assertTrue(
                $ClassName::validate($Block2),
                $Comment . " Target Data is not a valid " . $FieldType . " Field Data Block (" . print_r($Block2, 1) . ")"
        );
            
        //====================================================================//
        // Compare Data Using Field Type Comparator
        if (!$ClassName::compare($Block1, $Block2, $this->settings)) {
            echo PHP_EOL . "Source :" . print_r($Block1, true);
            echo PHP_EOL . "Target :" . print_r($Block2, true);
        }
        $this->assertTrue(
                $ClassName::compare($Block1, $Block2, $this->settings),
                $Comment . " Source and Target Data are not similar " . $FieldType . " Field Data Block"
 
        );

        return true;
    }
    
    /**
     * @abstract    Check Two List Data Blocks using Field's Compare functions
     *
     * @param   string  $FieldType          Field Type Name
     * @param   string  $FieldId            Field Identifier
     * @param   array   $Block1             Raw Data to Compare
     * @param   array   $Block2             Raw Data to Compare
     * @param   string  $Comment            Comment on this Test
     *
     * @return string   error / success translator string for debugger
     */
    private function compareListField($FieldType, $FieldId, $Block1, $Block2, $Comment = null)
    {
        //====================================================================//
        // Explode List Field Id
        $FieldIdArray      =  self::isListField($FieldId);
        $this->assertNotEmpty($FieldIdArray);
        $FieldName  = $FieldIdArray["fieldname"];
        $ListName   = $FieldIdArray["listname"];

        //====================================================================//
        // Extract List Data
        $List1 = $Block1[$ListName];
        $List2 = $Block2[$ListName];
        
        //====================================================================//
        // Verify Data Count is similar
        $this->assertEquals(
                count($List1),
                count($List2),
                "Source and Target List Data have diffrent number of Items"
 
        );

        //====================================================================//
        // Normalize Data Blocks
        $this->Normalize($List1);
        $this->Normalize($List2);
        while (!empty($List1)) {
            //====================================================================//
            // Extract Next Item
            $Item1  =   array_shift($List1);
            $Item2  =   array_shift($List2);

            //====================================================================//
            // Verify List field is Available
            $this->assertArrayHasKey(
 
                $FieldName,
 
                $Item1,
                    "Field " . $FieldType . " not found in Source List Data "
 
            );
            $this->assertArrayHasKey(
                $FieldName,
                $Item2,
                    "Field " . $FieldType . " not found in Target List Data "
            );
            
            //====================================================================//
            // Compare Items
            $Result = $this->compareField($FieldType, $Item1[$FieldName], $Item2[$FieldName], $Comment);
            if ($Result !== true) {
                return $Result;
            }
        }
        
        return true;
    }
    
    //==============================================================================
    //      OBJECTS DELETE AT THE END OF TESTS
    //==============================================================================
    
    protected function AddTestedObject($ObjectType, $ObjectId = null)
    {
        $this->CreatedObjects[] =   array(
            "ObjectType"    =>  $ObjectType,
            "ObjectId"      =>  $ObjectId,
        );
    }
    
    protected function CleanTestedObjects()
    {
        foreach ($this->CreatedObjects as $Object) {
            if (empty($Object["ObjectId"])) {
                continue;
            }
            //====================================================================//
            //   Verify Delete is Allowed
            $Definition = Splash::Object($Object["ObjectType"])->Description();
            if ($Definition["allow_push_deleted"]) {
                continue;
            }
            Splash::Object($Object["ObjectType"])->Delete($Object["ObjectId"]);
        }
    }
}
