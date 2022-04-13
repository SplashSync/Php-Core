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

namespace Splash\Models;

use Exception;
use Splash\Models\Objects\ObjectInterface;

/**
 * Local Objects Provider Interface.
 * Used to Override Core Objects Mapper (by Files)
 */
interface ObjectsProviderInterface
{
    /**
     * Build list of Available Objects
     *
     * @return string[]
     */
    public function objects(): array;

    /**
     * Get Splash Specific Object Class
     * This function is a router for all local object classes & functions
     *
     * @param string $objectType Specify Object Class Name
     *
     * @throws Exception
     *
     * @return ObjectInterface
     */
    public function object(string $objectType): ObjectInterface;
}
