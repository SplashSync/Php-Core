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
     *      @abstract   Set Lock for a specific object
     * 
     *                  This function is used to prevent further actions 
     *                  on currently edited objects. Node name & Type are
     *                  single, but Ids have to be stored as list
     * 
     *      @param      int         $Identifier     Local Object Identifier or Empty if New Object
     * 
     *      @return     bool
     */
    function Lock($Identifier = "new")
    {
        //====================================================================//
        // Search for Forced Commit Flag in Configuration
        if (array_key_exists("forcecommit",Splash::Configuration()->server) && (Splash::Configuration()->server["forcecommit"]) ) {
            return True;
        }
        
        //====================================================================//
        // Verify Object Identifier is not Empty
        if ( !$Identifier ) {
            $Identifier = "new";
        }
        
        //====================================================================//
        //  Init Lock Structure 
        if ( !isset($this->locks) )    {
            $this->locks = new ArrayObject(array(),  ArrayObject::ARRAY_AS_PROPS);
        }
        
        //====================================================================//
        //  Insert Object to Structure 
        $this->locks->offsetSet($Identifier,True);
        
        //====================================================================//
        //  Log 
        Splash::Log()->Deb("MsgLockObject", static::$NAME ,$Identifier);
        
        return True;        
    }   

    /**
     *      @abstract   Get Lock Status for a specific object
     * 
     *      @param      int         $Identifier     Local Object Identifier or Empty if New Object
     * 
     *      @return     bool      
     */
    function isLocked($Identifier = "new")
    {
        Splash::Log()->Deb("MsgisLockedStart", static::$NAME ,$Identifier);
        
        //====================================================================//
        // Verify Object Identifier is not Empty
        if ( !$Identifier ) {
            $Identifier = "new";
        }
        
        //====================================================================//
        //  Verify Lock Structure Exits
        if ( !isset($this->locks) ) { 
            return False; 
        }
        
        //====================================================================//
        //  Verify Object Exits
        if ( !$this->locks->offsetExists($Identifier) )        
        { 
            return False; 
        }
        
        //====================================================================//
        //  Log 
        Splash::Log()->Deb("MsgisLocked", static::$NAME ,$Identifier);
        return True;        
    }   
    
    /**
     *      @abstract   Delete Current active Lock 
     * 
     *      @param      int         $Identifier     Local Object Identifier or Empty if New Object
     * 
     *      @return     bool      
     */
    function Unlock($Identifier = "new")
    {
        //====================================================================//
        // Verify Object Identifier is not Empty
        if ( !$Identifier ) {
            $Identifier = "new";
        }
        
        //====================================================================//
        //  Verify Object Already Locked
        if ( !$this->isLocked($Identifier) )    { 
            return True; 
        }
        
        //====================================================================//
        //  Remove Object Lock
        $this->locks->offsetUnset($Identifier);
        
        //====================================================================//
        //  Log 
        Splash::Log()->Deb("MsgUnlockSuccess", static::$NAME ,$Identifier);
        
        return True;        
    }  
}

?>