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

namespace Splash\Tests\Tools\Components;

use Splash\Client\CommitEvent;
use Splash\Components\CommitsManager;

class TestCommitsManager extends CommitsManager
{
    /**
     * Force Intelligent Commits Mode
     *
     * @param bool $intelMode
     *
     * @return bool
     */
    public static function forceIntelMode(bool $intelMode): bool
    {
        return self::$intelCommitMode = $intelMode;
    }

    /**
     * {@inheritDoc}
     */
    protected static function isTravisMode(CommitEvent $commitEvent): bool
    {
        return false;
    }
}
