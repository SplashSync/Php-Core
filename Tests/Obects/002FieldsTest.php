<?php

use Splash\Tests\Tools\ObjectsCase;
use Splash\Client\Splash;
//use ArrayObject;

/**
 * @abstract    Objects Test Suite - Fields List Verifications
 *
 * @author SplashSync <contact@splashsync.com>
 */
class O02FieldsTest extends ObjectsCase {
    
    /**
     * @dataProvider ObjectTypesProvider
     */
    public function testFromModule($ObjectType)
    {
        //====================================================================//
        //   Execute Action Directly on Module  
        $Data = Splash::Object($ObjectType)->Fields();
        //====================================================================//
        //   Module May Return an Array (ArrayObject created by WebService) 
        if (is_array($Data)) {
            $Data   =   new ArrayObject($Data);
        } 
        //====================================================================//
        //   Verify Response
        $this->VerifyResponse($Data);
        
    }
    
    /**
     * @dataProvider ObjectTypesProvider
     */
    public function testFromObjectsService($ObjectType)
    {
        //====================================================================//
        //   Execute Action From Splash Server to Module  
        $Data = $this->GenericAction(SPL_S_OBJECTS, SPL_F_FIELDS, __METHOD__, [ "type" => $ObjectType]);
        
        //====================================================================//
        //   Verify Response
        $this->VerifyResponse($Data);
        
    }

    public function testFromObjectsServiceErrors()
    {
        //====================================================================//
        //      Request definition without Sending ObjectType  
        $this->GenericErrorAction(SPL_S_OBJECTS, SPL_F_FIELDS, __METHOD__);
        
    }
    
    public function VerifyResponse($Data)
    {
//        var_dump($Data);
        
        //====================================================================//
        //   Verify Response
        $this->assertNotEmpty( $Data                        , "Object Fields List is Empty");
        $this->assertInstanceOf( "ArrayObject" , $Data      , "Object Fields List is Not an ArrayObject");
        
        //====================================================================//
        // All Fields Definitions are is right format
        //====================================================================//
        foreach ($Data as $Field) {
            $this->VerifyField($Field);
        }

    }
    
    
    public function VerifyField($Field)
    {
        //====================================================================//
        // Verify Field Type Name Exists
        $this->assertArrayInternalType( $Field, "type", "string"    , "Field Type");
        $this->assertNotEmpty(  self::isValidType( $Field["type"] ) , "Field Type '" . $Field["type"] . "' is not a Valid Splash Field Type.");
        
        //====================================================================//
        // Remove List Name if List Fields Type            
        if ( self::isListField($Field["type"]) ) {
            $FieldListType  = self::isListField($Field["type"]);
            $FieldType      = $FieldListType["fieldname"];
        } else {
            $FieldType      = $Field["type"];
        }
                
        //====================================================================//
        // If Field is Id Field => Verify The given Object Type Exists 
        if ( self::isValidType($FieldType) && self::isIdField($FieldType) ) {
            $ObjectID   =   self::isIdField($FieldType);
            
            $this->assertTrue( 
                    in_array($ObjectID["ObjectType"], Splash::Objects() ) , 
                    "Object ID Field of Type '" . $ObjectID["ObjectType"] . "' is not a Valid. This Object Type was not found."
                );
        }
        
        //====================================================================//
        // All Required Informations are Available and is right format
        $this->assertArrayInternalType($Field , "id",       "string",       "Field Identifier");
        $this->assertArrayInternalType($Field , "name",     "string",       "Field Name");
        $this->assertArraySplashBool($Field ,   "required",                 "Field Required Flag");
        $this->assertArraySplashBool($Field ,   "write",                    "Field Write Flag");
        $this->assertArraySplashBool($Field ,   "read",                     "Field Read Flag");
        $this->assertArraySplashBool($Field ,   "inlist",                   "Field In List Flag");
        
        //====================================================================//
        // All Optional Informations are in right format
                

        if (array_key_exists("desc",$Field)) {
            $this->assertArrayInternalType($Field , "desc",     "string",   "Field Description");
        }
            
        if (array_key_exists("itemtype",$Field)) {
            $this->assertArrayInternalType($Field , "itemtype",     "string",   "Field Schema URL");
            $this->assertArrayInternalType($Field , "itemprop",     "string",   "Field Schema Property");
//                $this->isExtUrl         ($Field["itemtype"], "itemtype");
                $this->isArrayString    ($Field, "itemprop");
//                $this->isValidMicroData($Field["itemtype"],$Field["itemprop"]);
                
            }
            if (array_key_exists("tag",$Field)) {
                $this->isArrayString    ($Field, "tag");
            }
            if (array_key_exists("format",$Field)) {
                $this->isArrayString    ($Field, "format");
            }
            if (array_key_exists("notest",$Field)) {
                $this->isArrayBool      ($Field, "notest");
            }
            if (array_key_exists("asso",$Field) && !empty($Field["asso"])) {
                $this->isArray          ($Field["asso"], "asso");
            }
        
        
        
        return;
    }    
}
