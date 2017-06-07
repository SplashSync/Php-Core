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

namespace   Splash\Models;

use Splash\Core\SplashCore      as Splash;
use Splash\Models\Objects\ObjectInterface;
use Splash\Models\Objects\FieldsFactoryTrait;
use Splash\Models\Objects\LockTrait;
use Splash\Models\Objects\TranslatorTrait;

/**
 * @abstract    Base Class for class for Splash Objects.
 */
abstract class AbstractObject implements ObjectInterface
{
    use FieldsFactoryTrait;
    use LockTrait;
    use TranslatorTrait;    
    
    /**
     *  Object Disable Flag. Override this flag to disable Object.
     */
    protected static    $DISABLED        =  False;
    /**
     *  Object Name
     */
    protected static    $NAME            =  __CLASS__;
    /**
     *  Object Description 
     */
    protected static    $DESCRIPTION     =  __CLASS__;
    /**
     *  Object Icon (FontAwesome or Glyph ico tag) 
     */
    protected static    $ICO            =  "fa fa-cubes";
    /**
     *  Object Synchronization Limitations 
     *  
     *  This Flags are Used by Splash Server to Prevent Unexpected Operations on Remote Server
     */
    protected static    $ALLOW_PUSH_CREATED         =  TRUE;        // Allow Creation Of New Local Objects
    protected static    $ALLOW_PUSH_UPDATED         =  TRUE;        // Allow Update Of Existing Local Objects
    protected static    $ALLOW_PUSH_DELETED         =  TRUE;        // Allow Delete Of Existing Local Objects
    /**
     *  Object Synchronization Recommended Configuration 
     */
    protected static    $ENABLE_PUSH_CREATED       =  TRUE;         // Enable Creation Of New Local Objects when Not Existing
    protected static    $ENABLE_PUSH_UPDATED       =  TRUE;         // Enable Update Of Existing Local Objects when Modified Remotly
    protected static    $ENABLE_PUSH_DELETED       =  TRUE;         // Enable Delete Of Existing Local Objects when Deleted Remotly
    protected static    $ENABLE_PULL_CREATED       =  TRUE;         // Enable Import Of New Local Objects 
    protected static    $ENABLE_PULL_UPDATED       =  TRUE;         // Enable Import of Updates of Local Objects when Modified Localy
    protected static    $ENABLE_PULL_DELETED       =  TRUE;         // Enable Delete Of Remotes Objects when Deleted Localy
    
    //====================================================================//
    //  COMMON CLASS INFORMATIONS
    //====================================================================//

    /**
     *      @abstract   Return type of this Object Class
     */
    public static function getType()
    {
        return pathinfo(__FILE__,PATHINFO_FILENAME);
    }
    
    /**
     *      @abstract   Return name of this Object Class
     */
    public function getName()
    {
        return self::Trans(static::$NAME);
    }

    /**
     *      @abstract   Return Description of this Object Class
     */
    public function getDesc()
    {
        return self::Trans(static::$DESCRIPTION);
    }
    
    /**
     *      @abstract   Return Object Status
     */
    public static function getIsDisabled()
    {
        return static::$DISABLED;
    }
    
    /**
     *      @abstract   Return Object Icon
     */
    public static function getIcon()
    {
        return static::$ICO;
    }
    
    /**
     *  @abstract   Get Description Array for requested Object Type
     * 
     *  @return     array
     */    
    public function Description()
    {
        //====================================================================//
        // Stack Trace
        Splash::Log()->Trace(__CLASS__,__FUNCTION__);  
        
        //====================================================================//
        // Build & Return Object Description Array
        return array(
            //====================================================================//
            // General Object definition
            "type"          =>  $this->getType(),                   // Object Type Name
            "name"          =>  $this->getName(),                   // Object Display Neme
            "description"   =>  $this->getDesc(),                   // Object Descritioon
            "icon"          =>  $this->getIcon(),                   // Object Icon
            "disabled"      =>  $this->getIsDisabled(),              // Is This Object Enabled or Not?
            //====================================================================//
            // Object Limitations
            "allow_push_created"      =>  (bool) static::$ALLOW_PUSH_CREATED,
            "allow_push_updated"      =>  (bool) static::$ALLOW_PUSH_UPDATED,
            "allow_push_deleted"      =>  (bool) static::$ALLOW_PUSH_DELETED,
            //====================================================================//
            // Object Default Configuration
            "enable_push_created"     =>  (bool) static::$ENABLE_PUSH_CREATED,
            "enable_push_updated"     =>  (bool) static::$ENABLE_PUSH_UPDATED,
            "enable_push_deleted"     =>  (bool) static::$ENABLE_PUSH_DELETED,
            "enable_pull_created"     =>  (bool) static::$ENABLE_PULL_CREATED,
            "enable_pull_updated"     =>  (bool) static::$ENABLE_PULL_UPDATED,
            "enable_pull_deleted"     =>  (bool) static::$ENABLE_PULL_DELETED
        );
    }     
   
}
