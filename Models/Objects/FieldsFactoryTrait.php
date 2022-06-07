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

namespace   Splash\Models\Objects;

use Splash\Components\FieldsFactory;
use Splash\Core\SplashCore      as Splash;

/**
 * Implement Access to Fields Factory using Splash Static Class
 */
trait FieldsFactoryTrait
{
    /**
     * @var null|FieldsFactory
     */
    private static ?FieldsFactory $fieldsFactory = null;

    /**
     * Get a singleton FieldsFactory Class
     * Access to Object Fields Creation Functions
     *
     * @return FieldsFactory
     */
    public static function fieldsFactory(): FieldsFactory
    {
        //====================================================================//
        // Initialize Field Factory Class
        if (isset(self::$fieldsFactory)) {
            return self::$fieldsFactory;
        }
        //====================================================================//
        // Initialize Class
        self::$fieldsFactory = new FieldsFactory();
        //====================================================================//
        //  Load Translation File
        Splash::translator()->load("objects");

        return self::$fieldsFactory;
    }
}
