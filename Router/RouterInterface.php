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

namespace Splash\Router;

/**
 * Splash Router Interface
 */
interface RouterInterface
{
    /**
     * Task execution router. Receive task detail and execute required task operations.
     *
     * @param array $task Full Task Request Array
     *
     * @return null|array Task results, or False if KO
     */
    public static function action(array$task): ?array;
}
