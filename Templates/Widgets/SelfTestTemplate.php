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
    public function options()
    {
        return self::$OPTIONS;
    }
        
    /**
     * @abstract    Return requested Customer Data
     *
     * @param       array   $params               Widget Inputs Parameters
     *
     * @return      array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function get($params = null)
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace(__CLASS__, __FUNCTION__);
        
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
        $blocks = $this->blocksFactory()->render();
        if ($blocks) {
            $this->setBlocks($blocks);
        }

        //====================================================================//
        // Publish Widget
        return $this->render();
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
        $this->blocksFactory()->addTextBlock("This widget show results of Local Server SelfTest");
    }
    
    /**
    *   @abstract     Block Building - Notifications Parameters
    */
    private function buildNotificationsBlock()
    {
        //====================================================================//
        // Execute Loacl SelfTest Function
        Splash::selfTest();
        //====================================================================//
        // Get Log
        $logs = Splash::log();
        //====================================================================//
        // If test was passed
        if (empty($logs->err)) {
            $this->blocksFactory()->addNotificationsBlock(["success" => "Self-Test Passed!"]);
        }
        //====================================================================//
        // Add Error Notifications
        foreach ($logs->err as $message) {
            $this->blocksFactory()->addNotificationsBlock(["error" => $message]);
        }
        //====================================================================//
        // Add Warning Notifications
        foreach ($logs->war as $message) {
            $this->blocksFactory()->addNotificationsBlock(["warning" => $message]);
        }
        //====================================================================//
        // Add Success Notifications
        foreach ($logs->msg as $message) {
            $this->blocksFactory()->addNotificationsBlock(["success" => $message]);
        }
        //====================================================================//
        // Add Debug Notifications
        foreach ($logs->deb as $message) {
            $this->blocksFactory()->addNotificationsBlock(["info" => $message]);
        }
    }
    
    //====================================================================//
    // Class Tooling Functions
    //====================================================================//
}
