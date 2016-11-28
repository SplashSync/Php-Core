<?php

use Splash\Tests\Tools\ObjectsCase;
use Splash\Client\Splash;

/**
 * @abstract    Objects Test Suite - Objects List Reading Verifications
 *
 * @author SplashSync <contact@splashsync.com>
 */
class O03ListTest extends ObjectsCase {
    
    
    /**
     * @dataProvider ObjectTypesProvider
     */
    public function testFromModule($ObjectType)
    {
        //====================================================================//
        //   Execute Action Directly on Module  
        $Data = Splash::Object($ObjectType)->ObjectsList();
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
        $Data = $this->GenericAction(SPL_S_OBJECTS, SPL_F_LIST, __METHOD__, [ "type" => $ObjectType]);
        
        //====================================================================//
        //   Verify Response
        $this->VerifyResponse($Data);
        
    }

    public function testFromObjectsServiceErrors()
    {
        //====================================================================//
        //      Request definition without Sending ObjectType  
        $this->GenericErrorAction(SPL_S_OBJECTS, SPL_F_LIST, __METHOD__);
        
    }
    
    public function VerifyResponse($Data)
    {
        //====================================================================//
        //   Verify Response
        $this->assertNotEmpty( $Data                        , "Objects List is Empty");
        $this->assertInstanceOf( "ArrayObject" , $Data      , "Objects List is Not an ArrayObject");
        
        $this->VerifyMetaInformations($Data);
        $this->VerifyAvailableFields($Data, $ObjectType);
        
        
        
        //====================================================================//
        // CHECK ITEMS
        foreach ($Data as $ObjectType) {
            $this->assertNotEmpty( $ObjectType              , "Objects Type is Empty");
            $this->assertInternalType("string", $ObjectType , "Objects Type is Not an String. (Given" . print_r($ObjectType , True) . ")");
        }        
    }
    

    public function VerifyAvailableFields($Data, $ObjectType)
    {
        //====================================================================//
        // Verify Fields are Available
        $Fields = Splash::Object($ObjectType)->Fields();
        if ( is_null( $Fields ) ) {
            return False;
        }        

        //====================================================================//
        // Verify List Datas
        $Object = reset($this->objectlist);
        
        if ( $this->isArray($Object,"Object in List") ) {
            //====================================================================//
            // Verify Object Id field is available
            $this->addResult("inlist", 
                            array_key_exists("id",$Object), 
                            False,
                            array("%FieldName%" => "Object Identifier (id)" ));
             
            //====================================================================//
            // Verify all "inlist" fields are available
            foreach ($this->Fields[$ObjectType] as $field) {
                if ($field->inlist) {
                    $this->addResult("inlist", 
                                    array_key_exists($field->id,$Object), 
                                    False,
                                    array("%FieldName%" => $field->name ));
                }
            }
        }          

        
        
        return True;
    }

    public function VerifyMetaInformations($Data, $ObjectType)
    {
        //====================================================================//
        // Verify List Meta Are Available
        $this->assertArrayHasKey( "meta",   $Data,      $ObjectType . " List => Meta Informations are not defined");
        $Meta   =   $Data["meta"];
        
        //====================================================================//
        // Verify List Meta Format
        $this->assertArrayInternalType($Meta,   "current",  "integer",       $ObjectType . " List => Current Object Count not an Integer");
        $this->assertArrayInternalType($Meta,   "total",    "integer",       $ObjectType . " List => Total Object Count not an Integer");
        
        
        //====================================================================//
        // Store List For Next Steps
        $this->objectlistMeta = $ObjectsList["meta"];
        unset($ObjectsList["meta"]);
        if ( is_a($ObjectsList, "ArrayObject") ) {
            $this->objectlist = $ObjectsList->getArrayCopy();
        } else {
            $this->objectlist = $ObjectsList;
        }
        
        
        //====================================================================//
        // Verify List Meta Is Not Empty...
        if ($this->objectlistMeta["total"] == 0 ) {
            $this->addResult("empty", !count($this->objectlist));
            return $this->Complete();
        } 
    
    }

    

        
        


    
    
}
