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
    public function testFromModule($testSequence, $objectType)
    {
        $this->loadLocalTestSequence($testSequence);
        
        //====================================================================//
        //   Execute Action Directly on Module
        $data = Splash::object($objectType)->fields();
        //====================================================================//
        //   Module May Return an Array (ArrayObject created by WebService)
        if (is_array($data)) {
            $data   =   new ArrayObject($data);
        }
        //====================================================================//
        //   Verify Response
        $this->verifyResponse($data);
    }
    
    /**
     * @dataProvider objectTypesProvider
     */
    public function testFromObjectsService($testSequence, $objectType)
    {
        $this->loadLocalTestSequence($testSequence);
        
        //====================================================================//
        //   Execute Action From Splash Server to Module
        $data = $this->genericAction(SPL_S_OBJECTS, SPL_F_FIELDS, __METHOD__, [ "id" => null, "type" => $objectType]);
        
        //====================================================================//
        //   Verify Response
        $this->verifyResponse($data);
    }

    public function testFromObjectsServiceErrors()
    {
        //====================================================================//
        //      Request definition without Sending ObjectType
        $this->genericErrorAction(SPL_S_OBJECTS, SPL_F_FIELDS, __METHOD__);
    }
    
    public function verifyResponse($data)
    {
        //====================================================================//
        //   Verify Response
        $this->assertNotEmpty($data, "Object Fields List is Empty");
        $this->assertInstanceOf("ArrayObject", $data, "Object Fields List is Not an ArrayObject");
        
        //====================================================================//
        // All Fields Definitions are is right format
        //====================================================================//
        foreach ($data as $fieldData) {
            $this->verifyFieldRequired($fieldData);
            $this->verifyFieldMetaData($fieldData);
            $this->verifyFieldOptional($fieldData);
            $this->verifyFieldAssociations($fieldData, $data);
        }
    }
    
    
    /**
     * @abstract    Verify Main Field Informations are in right format
     *
     * @param array $field
     */
    public function verifyFieldRequired($field)
    {
        //====================================================================//
        // Verify Field Type Name Exists
        $this->assertArrayInternalType($field, "type", "string", "Field Type");
        $this->assertNotEmpty(
            self::isValidType($field["type"]),
            "Field Type '" . $field["type"] . "' is not a Valid Splash Field Type."
        );
        
        //====================================================================//
        // Remove List Name if List Fields Type
        if (self::isListField($field["type"])) {
            $fieldListType  = self::isListField($field["type"]);
            $fieldType      = $fieldListType["fieldname"];
        } else {
            $fieldType      = $field["type"];
        }
                
        //====================================================================//
        // If Field is Id Field => Verify The given Object Type Exists
        if (self::isValidType($fieldType) && self::isIdField($fieldType)) {
            $objectId   =   self::isIdField($fieldType);
            
            $this->assertTrue(
                in_array($objectId["ObjectType"], Splash::objects()),
                "Object ID Field of Type '" . $objectId["ObjectType"] . "' is not a Valid. "
                    . "This Object Type was not found."
            );
        }
        
        //====================================================================//
        // All Required Informations are Available and is right format
        $this->assertArrayInternalType($field, "id", "string", "Field Identifier");
        $this->assertArrayInternalType($field, "name", "string", "Field Name");
        $this->assertArraySplashBool($field, "required", "Field Required Flag");
        $this->assertArraySplashBool($field, "write", "Field Write Flag");
        $this->assertArraySplashBool($field, "read", "Field Read Flag");
        $this->assertArraySplashBool($field, "inlist", "Field In List Flag");
    }
    
    public function verifyFieldMetaData($field)
    {
        //====================================================================//
        // Field MicroData Infos
        if (array_key_exists("itemtype", $field) && !empty($field["itemtype"])) {
            $this->assertArrayInternalType($field, "itemtype", "string", "Field MicroData URL");
            $this->assertArrayInternalType($field, "itemprop", "string", "Field MicroData Property");
//                $this->isExtUrl         ($Field["itemtype"], "itemtype");
        }
        
        //====================================================================//
        // Field Tag
        if (array_key_exists("tag", $field) && !empty($field["tag"])) {
            $this->assertArrayInternalType($field, "tag", "string", "Field Linking Tag");
        }
        if (array_key_exists("tag", $field) && array_key_exists("itemtype", $field) && !empty($field["itemtype"])) {
            $this->assertEquals(
                $field["tag"],
                md5($field["itemprop"] . IDSPLIT . $field["itemtype"]),
                "Field Tag do not match with defined MicroData. Expected Format: md5('itemprop'@'itemptype') "
            );
        }
    }
    
    public function verifyFieldOptional($field)
    {
        //====================================================================//
        // Field Description
        if (array_key_exists("desc", $field)) {
            $this->assertArrayInternalType($field, "desc", "string", "Field Description");
        }
            
        //====================================================================//
        // Field Format
        if (array_key_exists("format", $field)) {
            $this->assertArrayInternalType($field, "format", "string", "Field Format Description");
        }
        
        //====================================================================//
        // Field No Test Flag
        if (array_key_exists("notest", $field)) {
            $this->assertArraySplashBool($field, "notest", "Field NoTest Flag");
        }
    }
    
    public function verifyFieldAssociations($field, $fields)
    {
        if (!array_key_exists("asso", $field) || empty($field["asso"])) {
            return;
        }
        //====================================================================//
        // Field Associated Fields List
        foreach ($field["asso"] as $fieldType) {
            //====================================================================//
            // Check FieldType Name
            $this->assertInternalType("string", $fieldType, "Associated FieldType must be String Format");

            //====================================================================//
            // Check FieldType Exists
            $assoField = null;
            foreach ($fields as $item) {
                if ($item["id"] === $fieldType) {
                    $assoField = $item;
                }
            }
            $this->assertNotEmpty($assoField, "Associated Field " . $fieldType . " isn't an existing Field Id String.");
        }
    }
}
