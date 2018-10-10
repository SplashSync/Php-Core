<?php

namespace Splash\Tests\Tools\Traits;

use Splash\Models\Fields\FieldsManagerTrait;

/**
 * @abstract    Splash Test Tools - Objects Data Validation
 *
 * @author SplashSync <contact@splashsync.com>
 */
trait ObjectsValidatorTrait
{
    use FieldsManagerTrait;
    
    /**
     * Fields Classes Name Prefix
     * @var string
     */
    protected static $CLASS_PREFIX        =   'Splash\Tests\Tools\Fields\Oo';
    
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
        $ClassName = self::$CLASS_PREFIX .  $In;
        
        //====================================================================//
        // Build New Entity
        if (class_exists($ClassName)) {
            return  $ClassName;
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
        $this->assertArrayHasKey(
            $ListId["listname"],
            $Data,
            "List '" . $ListId["listname"] . "' is not defined in returned Data Block."
        );
        
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
}
