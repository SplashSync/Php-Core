<?php

namespace Splash\Tests\Tools\Traits;

use Splash\Client\Splash;

/**
 * @abstract    Splash Test Tools - Objects Faker trait
 *
 * @author SplashSync <contact@splashsync.com>
 */
trait ObjectsFakerTrait
{
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
        $this->assertTrue(in_array($ObjectType, Splash::objects()), "Invalid Object Type Name. (" . $ObjectType . ")");

        //====================================================================//
        // Create Empty Object Data Array
        $Out    = array();
        
        //====================================================================//
        // Load Object Fields Definition
        $Fields = Splash::object($ObjectType)->fields();
        if (empty($Fields)) {
            return $Out;
        }
        
        //====================================================================//
        // Generate Fields Data
        foreach ($Fields as $Field) {
            //====================================================================//
            // Check if Fields is Needed
            if (!$this->isFieldNeeded($Field, $FieldsList)) {
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
     *   @abstract   Check if Field Need to be in List
     *
     *   @param      ArrayObject    $Field          Field Definition
     *   @param      array          $FieldsList     Object Field Ids List
     *
     *   @return     bool
     */
    private function isFieldNeeded($Field, $FieldsList = false)
    {
        //====================================================================//
        // Check if Fields is Writable
        if (!$Field->write) {
            return false;
        }
        //====================================================================//
        // Check if Fields is Needed
        //====================================================================//

        //====================================================================//
        // Required Field
        if ($Field->required) {
            return true;
        }
        //====================================================================//
        // If NO Fields List is Given => Select All Write Fields
        if (($FieldsList == false) || !is_array($FieldsList)) {
            return true;
        }
        //====================================================================//
        // Field is in Requested List
        if (!in_array($Field->id, $FieldsList)) {
            return false;
        }
        return true;
    }
    
    /**
     *   @abstract   Create Fake/Dummy Object Data
     *
     *   @param      array   $FieldsList     Object Field List
     *   @param      array   $OriginData     Original Object Data
     *
     *   @return     int     $result     0 if KO, 1 if OK
     */
    public function fakeObjectData($FieldsList, $OriginData = null)
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
                $Out[$Field->id] = (self::isFieldToPreserve($Field, $OriginData) ?
                        $OriginData[$Field->id] :
                        self::fakeFieldData($Field->type, $Field->choices, $Field->options)
                    );
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
     *   @abstract   Check if Field Need to e in List
     *
     *   @param      ArrayObject    $Field          Field Definition
     *   @param      array          $OriginData     Original Object Data
     *
     *   @return     bool
     */
    private static function isFieldToPreserve($Field, $OriginData)
    {
        //====================================================================//
        // Check if Origin Data Exists
        if (empty($OriginData) || !isset($OriginData[$Field->id]) || empty($OriginData[$Field->id])) {
            return false;
        }
        //====================================================================//
        // Check if Fields Should be Tested or Not
        if (!$Field->notest) {
            return false;
        }
        return true;
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
}
