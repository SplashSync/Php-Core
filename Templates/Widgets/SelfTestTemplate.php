<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace   Splash\Templates\Widgets;

use Exception;
use Splash\Core\SplashCore      as Splash;
use Splash\Models\AbstractWidget;

/**
 * SelfTest Template Widget for Splash Modules
 */
class SelfTestTemplate extends AbstractWidget
{
    /**
     * {@inheritdoc}
     */
    public static array $options = array(
        "Width" => self::SIZE_DEFAULT,
        'UseCache' => true,
        'CacheLifeTime' => 1,
    );

    /**
     * {@inheritdoc}
     */
    protected static string $name = "Server SelfTest";

    /**
     * {@inheritdoc}
     */
    protected static string $description = "Results of your Server SelfTests";

    /**
     * {@inheritdoc}
     */
    protected static string $ico = "fa fa-info-circle";

    //====================================================================//
    // Class Main Functions
    //====================================================================//

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function get(array $parameters = array()): ?array
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
     * @throws Exception
     *
     * @return void
     */
    private function buildNotificationsBlock()
    {
        //====================================================================//
        // Execute Local SelfTest Function
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
