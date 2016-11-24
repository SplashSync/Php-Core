<?php

use Splash\Tests\Tools\BaseClass;


/**
 * @abstract    Admin Test Suite - Get Objects List Client Verifications
 *
 * @author SplashSync <contact@splashsync.com>
 */
class A03ObjectsTest extends BaseClass {
    
    public function testObjectsAction()
    {
        //====================================================================//
        //   Execute Action From Splash Server to Module  
        $Data = $this->GenericAction(SPL_S_ADMIN, SPL_F_GET_OBJECTS, __METHOD__);
        
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
