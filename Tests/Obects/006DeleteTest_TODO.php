<?php

use Splash\Client\Splash;
use ArrayObject;

/**
 * @abstract    Objects Test Suite - Fields List Verifications
 *
 * @author SplashSync <contact@splashsync.com>
 */
class O01DescriptionTest extends ObjectsCase {
    
    
    public function testObjectsFromObjectsService()
    {
        //====================================================================//
        //   Execute Action From Splash Server to Module  
        $Data = $this->GenericAction(SPL_S_OBJECTS, SPL_F_OBJECTS, __METHOD__);
        
        //====================================================================//
        //   Verify Response
        $this->VerifyResponse($Data);
        
    }
    
    public function VerifyResponse($Data)
    {
        //====================================================================//
        //   Verify Response
        $this->assertNotEmpty( $Data                        , "Objects List is Empty");
        $this->assertInstanceOf( "ArrayObject" , $Data      , "Objects List is Not an ArrayObject");
        
        //====================================================================//
        // CHECK ITEMS
        foreach ($Data as $ObjectType) {
            $this->assertNotEmpty( $ObjectType              , "Objects Type is Empty");
            $this->assertInternalType("string", $ObjectType , "Objects Type is Not an String. (Given" . print_r($ObjectType , True) . ")");
        }        
    }
    
}
