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

namespace Splash\Core\Interfaces\Scopes;

/**
 * Interface for Server Scope Functional Testing
 */
interface TestableInterface
{
    /**
     * Get List of Phpunit Classes to Execute
     *
     * @return class-string[]
     */
    public function getTestClasses(): array;
}
