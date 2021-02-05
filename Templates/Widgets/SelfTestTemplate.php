<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2021 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

//====================================================================//
// *******************************************************************//
//                     SPLASH FOR PHP APPLICATIONS                    //
// *******************************************************************//
//====================================================================//

namespace   Splash\Templates\Widgets;

use ArrayObject;
use Splash\Core\SplashCore      as Splash;
use Splash\Models\AbstractWidget;

/**
 * SelfTest Template Widget for Splash Modules
 *
 * @author B. Paquier <contact@splashsync.com>
 */
class SelfTestTemplate extends AbstractWidget
{
    /**
     * Define Standard Options for this Widget
     * Override this array to change default options for your widget
     *
     * @var array
     */
    public static $OPTIONS = array(
        "Width" => self::SIZE_DEFAULT,
        'UseCache' => true,
        'CacheLifeTime' => 1,
    );

    /**
     * Widget Name
     *
     * @var string
     */
    protected static $NAME = "Server SelfTest";

    /**
     * Widget Description
     *
     * @var string
     */
    protected static $DESCRIPTION = "Results of your Server SelfTests";

    /**
     * Widget Icon (FontAwesome or Glyph ico tag)
     *
     * @var string
     */
    protected static $ICO = "fa fa-info-circle";

    //====================================================================//
    // Class Main Functions
    //====================================================================//

    /**
     * Return Widget Customs Options
     *
     * @return array
     */
    public function options()
    {
        return self::$OPTIONS;
    }

    /**
     * @param array $params
     *
     * @return array|ArrayObject
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function get($params = array())
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();

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
     * Block Building - Text Intro
     *
     * @return void
     */
    private function buildIntroBlock()
    {
        //====================================================================//
        // Into Text Block
        $this->blocksFactory()->addTextBlock("This widget show results of Local Server SelfTest");
    }

    /**
     * Block Building - Notifications Parameters
     *
     * @return void
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
            $this->blocksFactory()->addNotificationsBlock(array("success" => "Self-Test Passed!"));
        }
        //====================================================================//
        // Add Error Notifications
        foreach ($logs->err as $message) {
            $this->blocksFactory()->addNotificationsBlock(array("error" => $message));
        }
        //====================================================================//
        // Add Warning Notifications
        foreach ($logs->war as $message) {
            $this->blocksFactory()->addNotificationsBlock(array("warning" => $message));
        }
        //====================================================================//
        // Add Success Notifications
        foreach ($logs->msg as $message) {
            $this->blocksFactory()->addNotificationsBlock(array("success" => $message));
        }
        //====================================================================//
        // Add Debug Notifications
        foreach ($logs->deb as $message) {
            $this->blocksFactory()->addNotificationsBlock(array("info" => $message));
        }
    }
}
