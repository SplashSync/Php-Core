<?php
/*
 * Copyright (C) 2011-2014  Bernard Paquier       <bernard.paquier@gmail.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 *
*/
                    
//====================================================================//
// *******************************************************************//
//                     SPLASH FOR PHP APPLICATIONS                    //
// *******************************************************************//
//====================================================================//

namespace   Splash\Templates\Widgets;

use Splash\Models\WidgetBase;
use Splash\Core\SplashCore      as Splash;

/**
 * @abstract    SelfTest Template Widget for Splash Modules
 *
 * @author B. Paquier <contact@splashsync.com>
 */
class SelfTestTemplate extends WidgetBase
{
    /**
     * @abstract  Widget Name
     */
    protected static $NAME            =  "Server SelfTest";
    
    /**
     * @abstract  Widget Description
     */
    protected static $DESCRIPTION     =  "Results of your Server SelfTests";
    
    /**
     * @abstract  Widget Icon (FontAwesome or Glyph ico tag)
     */
    protected static $ICO     =  "fa fa-info-circle";
    
    //====================================================================//
    // Define Standard Options for this Widget
    // Override this array to change default options for your widget
    public static $OPTIONS       = array(
        "Width"     =>      self::SIZE_DEFAULT,
        'UseCache'      =>  true,
        'CacheLifeTime' =>  1,
    );
    
    //====================================================================//
    // Class Main Functions
    //====================================================================//
    
    /**
     *      @abstract   Return Widget Customs Options
     */
    public function Options()
    {
        return self::$OPTIONS;
    }
        
    /**
     * @abstract    Return requested Customer Data
     *
     * @param       array   $params               Widget Inputs Parameters
     *
     * @return      array
     */
    public function Get($params=null)
    {
        //====================================================================//
        // Stack Trace
        Splash::Log()->Trace(__CLASS__, __FUNCTION__);
        
        //====================================================================//
        // Setup Widget Core Informations
        //====================================================================//

        $this->setTitle($this->getName());
        $this->setIcon($this->getIcon());
        
        //====================================================================//
        // Build Intro Text Block
        //====================================================================//
        $this->buildIntroBlock();
        
        //====================================================================//
        // Build Inputs Block
        //====================================================================//
        $this->buildNotificationsBlock();

        //====================================================================//
        // Set Blocks to Widget
        $this->setBlocks($this->BlocksFactory()->Render());

        //====================================================================//
        // Publish Widget
        return $this->Render();
    }
        

    //====================================================================//
    // Blocks Generation Functions
    //====================================================================//

    /**
    *   @abstract     Block Building - Text Intro
    */
    private function buildIntroBlock()
    {
        //====================================================================//
        // Into Text Block
        $this->BlocksFactory()->addTextBlock("This widget show results of Local Server SelfTest");
    }
    
    /**
    *   @abstract     Block Building - Notifications Parameters
    */
    private function buildNotificationsBlock()
    {
        //====================================================================//
        // Execute Loacl SelfTest Function
        Splash::SelfTest();
        //====================================================================//
        // Get Log
        $Log = Splash::Log();
        //====================================================================//
        // If test was passed
        if (empty($Log->err)) {
            $this->BlocksFactory()->addNotificationsBlock(["success" => "Self-Test Passed!"]);
        }
        //====================================================================//
        // Add Error Notifications
        foreach ($Log->err as $Text) {
            $this->BlocksFactory()->addNotificationsBlock(["error" => $Text]);
        }
        //====================================================================//
        // Add Warning Notifications
        foreach ($Log->war as $Text) {
            $this->BlocksFactory()->addNotificationsBlock(["warning" => $Text]);
        }
        //====================================================================//
        // Add Success Notifications
        foreach ($Log->msg as $Text) {
            $this->BlocksFactory()->addNotificationsBlock(["success" => $Text]);
        }
        //====================================================================//
        // Add Debug Notifications
        foreach ($Log->deb as $Text) {
            $this->BlocksFactory()->addNotificationsBlock(["info" => $Text]);
        }
    }
    
    //====================================================================//
    // Class Tooling Functions
    //====================================================================//
}
