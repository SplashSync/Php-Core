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

namespace   Splash\Models\Helpers;

use Exception;
use Splash\Components\CommitsManager;

/**
 * Helper for PhpUnit Tests
 */
class TestHelper
{
    /**
     * Simulate Commit For PhpUnits Tests (USE WITH CARE)
     * Only PhpUnit Tests are Impacted by This Action
     *
     * @param string           $objectType object Type Name
     * @param array|int|string $local      object Local Id or Array of Local Id
     * @param string           $action     Action Type (SPL_A_UPDATE, or SPL_A_CREATE, or SPL_A_DELETE)
     * @param string           $user       User Name
     * @param null|string      $comment    Operation Comment for Logs
     *
     * @throws Exception
     *
     * @return void
     */
    public static function simObjectCommit(
        string $objectType,
        $local,
        string $action,
        string $user = 'PhpUnit',
        string $comment = null
    ): void {
        CommitsManager::simSessionCommit($objectType, $local, $action, $user, $comment);
    }
}
