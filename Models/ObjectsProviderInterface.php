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

namespace Splash\Models;

use Splash\Models\Objects\ObjectInterface;

/**
 * @abstract    Local Objects Provider Interface.
 *              Used to Override Core Objects Mapper (by Files)
 */
interface ObjectsProviderInterface
{
    /**
     * @abstract   Build list of Available Objects
     *
     * @return string[]
     */
    public function objects();

    /**
     * @abstract   Get Specific Object Class
     *             This function is a router for all local object classes & functions
     *
     * @params     string   $type       Specify Object Class Name
     *
     * @param null|mixed $objectType
     *
     * @return ObjectInterface
     */
    public function object($objectType = null);
}
