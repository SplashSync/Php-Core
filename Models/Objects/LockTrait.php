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

use Splash\Core\SplashCore      as Splash;

/**
 * Object Lock Management (Used to prevent unexpected Commit during remote actions)
 */
trait LockTrait
{
    /**
     * @var array<string, null|true>
     */
    private array $locks = array();

    /**
     * {@inheritdoc}
     */
    public function lock(?string $objectId = null): bool
    {
        //====================================================================//
        // Search for Forced Commit Flag in Configuration
        if (isset(Splash::configuration()->server["forcecommit"])
                && (Splash::configuration()->server["forcecommit"])) {
            return true;
        }
        //====================================================================//
        //  Insert Object to Structure
        $this->locks[$objectId ?: "new"] = true;
        //====================================================================//
        //  Log
        Splash::log()->deb("MsgLockObject", static::$name, (string) $objectId);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isLocked(?string $objectId = null): bool
    {
        //====================================================================//
        //  Verify Object Lock
        if (!isset($this->locks[$objectId ?: "new"])) {
            return false;
        }
        //====================================================================//
        //  Log
        Splash::log()->deb("MsgIsLocked", static::$name, (string)  $objectId);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function unLock(?string $objectId = null): bool
    {
        //====================================================================//
        //  Verify Object Lock
        if (!isset($this->locks[$objectId ?: "new"])) {
            return true;
        }
        //====================================================================//
        //  Remove Object Lock
        unset($this->locks[$objectId ?: "new"]);
        //====================================================================//
        //  Log
        Splash::log()->deb("MsgUnlockSuccess", static::$name, (string)  $objectId);

        return true;
    }
}
