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

use ArrayObject;
use Splash\Client\Splash;

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
     * @param string           $comment    Operation Comment for Historics
     *
     * @return void
     */
    public static function simObjectCommit($objectType, $local, $action, $user = 'PhpUnit', $comment = '')
    {
        if (empty($comment)) {
            $comment = 'Commit Simulated';
        }
        $params = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        $params->type = $objectType;            // Type of the Object
        $params->id = $local;                   // Id of Modified object
        $params->action = $action;              // Action Type On this Object
        $params->user = $user;                  // Operation User Name for Historics
        $params->comment = $comment;            // Operation Comment for Historics

        Splash::$commited[] = $params;
    }
}
