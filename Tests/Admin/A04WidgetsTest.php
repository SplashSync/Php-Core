<?php

use Splash\Tests\Tools\BaseClass;


/**
 * @abstract    Admin Test Suite - Get Objects List Client Verifications
 *
 * @author SplashSync <contact@splashsync.com>
 */
class A04WidgetsTest extends BaseClass {
    
    public function testWidgetsActionFromAdmin()
    {
        
        //====================================================================//
        //   Execute Action From Splash Server to Module  
        $Data = $this->GenericAction(SPL_S_ADMIN, SPL_F_GET_WIDGETS, __METHOD__);
        
        //====================================================================//
        //   Verify Response
        $this->assertNotEmpty( $Data                        , "Widgets List is Empty");
        $this->assertInstanceOf( "ArrayObject" , $Data      , "Widgets List is Not an ArrayObject");
        
        //====================================================================//
        // CHECK ITEMS
        foreach ($Data as $WidgetType) {
            $this->assertNotEmpty( $WidgetType              , "Widgets Type is Empty");
            $this->assertInternalType("string", $WidgetType , "Widgets Type is Not an String. (Given" . print_r($WidgetType , True) . ")");
        }        
        
    }
    
    public function testWidgetsActionFromWidgets()
    {
        
        //====================================================================//
        //   Execute Action From Splash Server to Module  
        $Data = $this->GenericAction(SPL_S_WIDGETS, SPL_F_WIDGET_LIST, __METHOD__);
        
        //====================================================================//
        //   Verify Response
        $this->assertNotEmpty( $Data                        , "Widgets List is Empty");
        $this->assertInstanceOf( "ArrayObject" , $Data      , "Widgets List is Not an ArrayObject");
        
        //====================================================================//
        // CHECK ITEMS
        foreach ($Data as $WidgetType) {
            $this->assertNotEmpty( $WidgetType              , "Widgets Type is Empty");
            $this->assertInternalType("string", $WidgetType , "Widgets Type is Not an String. (Given" . print_r($WidgetType , True) . ")");
        }        
        
    }
    
    
}
