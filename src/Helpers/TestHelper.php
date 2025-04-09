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

namespace Splash\Core\Helpers;

use Exception;
use Splash\Core\Client\Splash;
use Splash\Core\Components\CommitsManager;

/**
 * Helper for PhpUnit Tests
 */
class TestHelper
{
    /**
     * Simulate Commit For PhpUnits Tests (USE WITH CARE)
     * Only PhpUnit Tests are Impacted by This Action
     *
     * @param string           $objectType Object Type Name
     * @param array|int|string $local      Object Local ID or Array of Local ID
     * @param string           $action     Action Type (See SplOperations)
     * @param null|string      $user       User Name
     * @param null|string      $comment    Operation Comment for Logs
     *
     * @return void
     */
    public static function simObjectCommit(
        string $objectType,
        $local,
        string $action,
        ?string $user = null,
        ?string $comment = null
    ): void {
        try {
            CommitsManager::simSessionCommit($objectType, $local, $action, $user, $comment);
        } catch (Exception $e) {
            Splash::log()->report($e);
        }
    }
}
