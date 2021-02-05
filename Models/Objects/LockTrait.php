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

namespace   Splash\Models\Objects;

use ArrayObject;
use Splash\Core\SplashCore      as Splash;

/**
 * @abstract    Object Lock Management (Used to prevent unexpected Commit during remote actions)
 */
trait LockTrait
{
    /**
     * @var null|ArrayObject
     */
    private $locks;

    /**
     * {@inheritdoc}
     */
    public function lock($objectId = "new")
    {
        //====================================================================//
        // Search for Forced Commit Flag in Configuration
        if (array_key_exists("forcecommit", Splash::configuration()->server)
                && (Splash::configuration()->server["forcecommit"])) {
            return true;
        }

        //====================================================================//
        // Verify Object Identifier is not Empty
        if (!$objectId) {
            $objectId = "new";
        }

        //====================================================================//
        //  Init Lock Structure
        if (is_null($this->locks)) {
            $this->locks = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        }

        //====================================================================//
        //  Insert Object to Structure
        $this->locks->offsetSet($objectId, true);

        //====================================================================//
        //  Log
        Splash::log()->deb("MsgLockObject", static::$NAME, (string) $objectId);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isLocked($objectId = "new")
    {
        Splash::log()->deb("MsgisLockedStart", static::$NAME, (string)  $objectId);

        //====================================================================//
        // Verify Object Identifier is not Empty
        if (!$objectId) {
            $objectId = "new";
        }
        //====================================================================//
        //  Verify Lock Structure Exits
        if (is_null($this->locks)) {
            return false;
        }
        //====================================================================//
        //  Verify Object Exits
        if (!$this->locks->offsetExists($objectId)) {
            return false;
        }
        //====================================================================//
        //  Log
        Splash::log()->deb("MsgisLocked", static::$NAME, (string)  $objectId);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function unLock($objectId = "new")
    {
        //====================================================================//
        // Verify Object Identifier is not Empty
        if (!$objectId) {
            $objectId = "new";
        }
        //====================================================================//
        //  Verify Lock Structure Exits
        if (is_null($this->locks)) {
            return true;
        }
        //====================================================================//
        //  Verify Object Already Locked
        if (!$this->isLocked($objectId)) {
            return true;
        }
        //====================================================================//
        //  Remove Object Lock
        $this->locks->offsetUnset($objectId);
        //====================================================================//
        //  Log
        Splash::log()->deb("MsgUnlockSuccess", static::$NAME, (string)  $objectId);

        return true;
    }
}
