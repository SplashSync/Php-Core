<?php
/**
 * This file is part of SplashSync Project.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 *  @author    Splash Sync <www.splashsync.com>
 *  @copyright 2015-2017 Splash Sync
 *  @license   GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007
 *
 **/

namespace   Splash\Models\Objects;

use Splash\Core\SplashCore      as Splash;

use ArrayObject;

/**
 * @abstract    Object Lock Management (Used to prevent unexpected Commit during remote actions)
 */
trait LockTrait
{
    /**
     * @var null|ArrayObject
     */
    private $locks = null;
    
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
        Splash::log()->deb("MsgLockObject", static::$NAME, $objectId);
        
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isLocked($objectId = "new")
    {
        Splash::log()->deb("MsgisLockedStart", static::$NAME, $objectId);
        
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
        Splash::log()->deb("MsgisLocked", static::$NAME, $objectId);
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
        //  Verify Object Already Locked
        if (!$this->isLocked($objectId)) {
            return true;
        }
        //====================================================================//
        //  Remove Object Lock
        $this->locks->offsetUnset($objectId);
        //====================================================================//
        //  Log
        Splash::log()->deb("MsgUnlockSuccess", static::$NAME, $objectId);
        return true;
    }
}
