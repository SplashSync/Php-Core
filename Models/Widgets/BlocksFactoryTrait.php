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

namespace   Splash\Models\Widgets;

use Splash\Components\BlocksFactory;
use Splash\Core\SplashCore      as Splash;

/**
 * Implement Access to Block Factory using Splash Static Class
 */
trait BlocksFactoryTrait
{
    /**
     * @var null|BlocksFactory
     */
    private static ?BlocksFactory $blocksFactory;

    /**
     * Get a singleton BlocksFactory Class
     * Access to Widget Block Creation Functions
     *
     * @return BlocksFactory
     */
    public static function blocksFactory(): BlocksFactory
    {
        //====================================================================//
        // Initialize Block Factory Class
        if (!isset(self::$blocksFactory)) {
            self::$blocksFactory = new BlocksFactory();
        }
        //====================================================================//
        // Return Helper Class
        return self::$blocksFactory;
    }
}
