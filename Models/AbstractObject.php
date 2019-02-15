<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2019 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace   Splash\Models;

use Splash\Core\SplashCore      as Splash;
use Splash\Models\Objects;

/**
 * Base Class for class for Splash Objects.
 *
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
abstract class AbstractObject implements Objects\ObjectInterface
{
    use Objects\FieldsFactoryTrait;
    use Objects\LockTrait;
    use Objects\TranslatorTrait;
    use Objects\PricesTrait;
    use Objects\ImagesTrait;
    use Objects\ObjectsTrait;
    use Objects\ListsTrait;
    
    /**
     *  Object Disable Flag. Override this flag to disable Object.
     */
    protected static $DISABLED        =  false;
    
    /**
     *  Object Name
     */
    protected static $NAME            =  __CLASS__;
    
    /**
     *  Object Description
     */
    protected static $DESCRIPTION     =  __CLASS__;
    
    /**
     *  Object Icon (FontAwesome or Glyph ico tag)
     */
    protected static $ICO            =  "fa fa-cubes";
    
    /**
     *  Object Synchronization Limitations
     *
     *  This Flags are Used by Splash Server to Prevent Unexpected Operations on Remote Server
     */
    // Allow Creation Of New Local Objects
    protected static $ALLOW_PUSH_CREATED         =  true;
    // Allow Update Of Existing Local Objects
    protected static $ALLOW_PUSH_UPDATED         =  true;
    // Allow Delete Of Existing Local Objects
    protected static $ALLOW_PUSH_DELETED         =  true;
    
    /**
     *  Object Synchronization Recommended Configuration
     */
    // Enable Creation Of New Local Objects when Not Existing
    protected static $ENABLE_PUSH_CREATED       =  true;
    // Enable Update Of Existing Local Objects when Modified Remotly
    protected static $ENABLE_PUSH_UPDATED       =  true;
    // Enable Delete Of Existing Local Objects when Deleted Remotly
    protected static $ENABLE_PUSH_DELETED       =  true;
    // Enable Import Of New Local Objects
    protected static $ENABLE_PULL_CREATED       =  true;
    // Enable Import of Updates of Local Objects when Modified Localy
    protected static $ENABLE_PULL_UPDATED       =  true;
    // Enable Delete Of Remotes Objects when Deleted Localy
    protected static $ENABLE_PULL_DELETED       =  true;
    
    //====================================================================//
    //  COMMON CLASS INFORMATIONS
    //====================================================================//

    /**
     * Return type of this Object Class
     *
     * @return string
     */
    public static function getType()
    {
        return pathinfo(__FILE__, PATHINFO_FILENAME);
    }
    
    /**
     * Return name of this Object Class
     *
     * @return string
     */
    public function getName()
    {
        return self::trans(static::$NAME);
    }

    /**
     * Return Description of this Object Class
     *
     * @return string
     */
    public function getDesc()
    {
        return self::trans(static::$DESCRIPTION);
    }
    
    /**
     * {@inheritdoc}
     */
    public static function getIsDisabled()
    {
        return static::$DISABLED;
    }
    
    /**
     * Return Object Icon
     *
     * @return string
     */
    public static function getIcon()
    {
        return static::$ICO;
    }
    
    /**
     * {@inheritdoc}
     */
    public function description()
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace(__CLASS__, __FUNCTION__);
        
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
