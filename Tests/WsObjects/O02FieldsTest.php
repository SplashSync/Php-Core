<?php
namespace Splash\Tests\WsObjects;

use Splash\Tests\Tools\ObjectsCase;
use Splash\Client\Splash;
use ArrayObject;

/**
 * @abstract    Objects Test Suite - Fields List Verifications
 *
 * @author SplashSync <contact@splashsync.com>
 */
class O02FieldsTest extends ObjectsCase
{
    
    /**
     * @dataProvider objectTypesProvider
     */
    public function testFromModule($Sequence, $ObjectType)
    {
        $this->loadLocalTestSequence($Sequence);
        
        //====================================================================//
        //   Execute Action Directly on Module
        $Data = Splash::object($ObjectType)->fields();
        //====================================================================//
        //   Module May Return an Array (ArrayObject created by WebService)
        if (is_array($Data)) {
            $Data   =   new ArrayObject($Data);
        }
        //====================================================================//
        //   Verify Response
        $this->verifyResponse($Data);
    }
    
    /**
     * @dataProvider objectTypesProvider
     */
    public function testFromObjectsService($Sequence, $ObjectType)
    {
        $this->loadLocalTestSequence($Sequence);
        
        //====================================================================//
        //   Execute Action From Splash Server to Module
        $Data = $this->GenericAction(SPL_S_OBJECTS, SPL_F_FIELDS, __METHOD__, [ "id" => null, "type" => $ObjectType]);
        
        //====================================================================//
        //   Verify Response
        $this->verifyResponse($Data);
    }

    public function testFromObjectsServiceErrors()
    {
        //====================================================================//
        //      Request definition without Sending ObjectType
        $this->GenericErrorAction(SPL_S_OBJECTS, SPL_F_FIELDS, __METHOD__);
    }
    
    public function verifyResponse($Data)
    {
        //====================================================================//
        //   Verify Response
        $this->assertNotEmpty($Data, "Object Fields List is Empty");
        $this->assertInstanceOf("ArrayObject", $Data, "Object Fields List is Not an ArrayObject");
        
        //====================================================================//
        // All Fields Definitions are is right format
        //====================================================================//
        foreach ($Data as $Field) {
            $this->verifyFieldRequired($Field);
            $this->verifyFieldMetaData($Field);
            $this->verifyFieldOptional($Field);
            $this->verifyFieldAssociations($Field, $Data);
        }
    }
    
    
    /**
     * @abstract    Verify Main Field Informations are in right format
     *
     * @param array $Field
     */
    public function verifyFieldRequired($Field)
    {
        //====================================================================//
        // Verify Field Type Name Exists
        $this->assertArrayInternalType($Field, "type", "string", "Field Type");
        $this->assertNotEmpty(
            self::isValidType($Field["type"]),
            "Field Type '" . $Field["type"] . "' is not a Valid Splash Field Type."
        );
        
        //====================================================================//
        // Remove List Name if List Fields Type
        if (self::isListField($Field["type"])) {
            $FieldListType  = self::isListField($Field["type"]);
            $FieldType      = $FieldListType["fieldname"];
        } else {
            $FieldType      = $Field["type"];
        }
                
        //====================================================================//
        // If Field is Id Field => Verify The given Object Type Exists
        if (self::isValidType($FieldType) && self::isIdField($FieldType)) {
            $ObjectID   =   self::isIdField($FieldType);
            
            $this->assertTrue(
                in_array($ObjectID["ObjectType"], Splash::objects()),
                "Object ID Field of Type '" . $ObjectID["ObjectType"] . "' is not a Valid. "
                    . "This Object Type was not found."
            );
        }
        
        //====================================================================//
        // All Required Informations are Available and is right format
        $this->assertArrayInternalType($Field, "id", "string", "Field Identifier");
        $this->assertArrayInternalType($Field, "name", "string", "Field Name");
        $this->assertArraySplashBool($Field, "required", "Field Required Flag");
        $this->assertArraySplashBool($Field, "write", "Field Write Flag");
        $this->assertArraySplashBool($Field, "read", "Field Read Flag");
        $this->assertArraySplashBool($Field, "inlist", "Field In List Flag");
    }
    
    public function verifyFieldMetaData($Field)
    {
        //====================================================================//
        // Field MicroData Infos
        if (array_key_exists("itemtype", $Field) && !empty($Field["itemtype"])) {
            $this->assertArrayInternalType($Field, "itemtype", "string", "Field MicroData URL");
            $this->assertArrayInternalType($Field, "itemprop", "string", "Field MicroData Property");
//                $this->isExtUrl         ($Field["itemtype"], "itemtype");
        }
        
        //====================================================================//
        // Field Tag
        if (array_key_exists("tag", $Field) && !empty($Field["tag"])) {
            $this->assertArrayInternalType($Field, "tag", "string", "Field Linking Tag");
        }
        if (array_key_exists("tag", $Field) && array_key_exists("itemtype", $Field) && !empty($Field["itemtype"])) {
            $this->assertEquals(
                $Field["tag"],
                md5($Field["itemprop"] . IDSPLIT . $Field["itemtype"]),
                "Field Tag do not match with defined MicroData. Expected Format: md5('itemprop'@'itemptype') "
            );
        }
    }
    
    public function verifyFieldOptional($Field)
    {
        //====================================================================//
        // Field Description
        if (array_key_exists("desc", $Field)) {
            $this->assertArrayInternalType($Field, "desc", "string", "Field Description");
        }
            
        //====================================================================//
        // Field Format
        if (array_key_exists("format", $Field)) {
            $this->assertArrayInternalType($Field, "format", "string", "Field Format Description");
        }
        
        //====================================================================//
        // Field No Test Flag
        if (array_key_exists("notest", $Field)) {
            $this->assertArraySplashBool($Field, "notest", "Field NoTest Flag");
        }
    }
    
    public function verifyFieldAssociations($Field, $Fields)
    {
        if (!array_key_exists("asso", $Field) || empty($Field["asso"])) {
            return;
        }
        //====================================================================//
        // Field Associated Fields List
        foreach ($Field["asso"] as $FieldType) {
            //====================================================================//
            // Check FieldType Name
            $this->assertInternalType("string", $FieldType, "Associated FieldType must be String Format");

            //====================================================================//
            // Check FieldType Exists
            $AssoField = null;
            foreach ($Fields as $Item) {
                if ($Item["id"] === $FieldType) {
                    $AssoField = $Item;
                }
            }
            $this->assertNotEmpty($AssoField, "Associated Field " . $FieldType . " isn't an existing Field Id String.");
        }
    }
}
