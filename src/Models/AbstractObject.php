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

namespace Splash\Core\Models;

use ReflectionClass;
use Splash\Core\Client\Splash;
use Splash\Core\Dictionary\Objects\SplObjectProps as Props;
use Splash\Core\Interfaces\Object\ObjectInterface;

/**
 * Base Class for Splash Objects.
 */
abstract class AbstractObject implements ObjectInterface
{
    use Objects\FieldsFactoryTrait;
    use Objects\LockTrait;
    use Objects\TranslatorTrait;

    /**
     * Object Disable Flag. Override this flag to disable Object.
     *
     * @var bool
     */
    protected static bool $disabled = false;

    /**
     * Object Name
     *
     * @var string
     */
    protected static string $name = __CLASS__;

    /**
     * Object Description
     *
     * @var string
     */
    protected static string $description = __CLASS__;

    /**
     * Object Icon (FontAwesome or Glyph ico tag)
     *
     * @var string
     */
    protected static string $ico = "fa fa-cubes";

    //====================================================================//
    // Object Synchronization Limitations
    // This Flags are Used by Splash Server to Prevent Unexpected Operations on Remote Server
    //====================================================================//

    /**
     * Allow Creation Of New Local Objects
     *
     * @var bool
     */
    protected static bool $allowPushCreated = true;

    /**
     * Allow Update Of Existing Local Objects
     *
     * @var bool
     */
    protected static bool $allowPushUpdated = true;

    /**
     * Allow To Delete an Existing Local Objects
     *
     * @var bool
     */
    protected static bool $allowPushDeleted = true;

    //====================================================================//
    // Object Synchronization Recommended Configuration
    //
    // This Flags are Used by Splash Server to Setup Default Objects Configuration
    //====================================================================//

    /**
     * Enable Creation Of New Local Objects when Not Existing
     *
     * @var bool
     */
    protected static bool $enablePushCreated = true;

    /**
     * Enable Update Of Existing Local Objects when Modified Remotely
     *
     * @var bool
     */
    protected static bool $enablePushUpdated = true;

    /**
     * Enable Delete Of Existing Local Objects when Deleted Remotely
     *
     * @var bool
     */
    protected static bool $enablePushDeleted = true;

    /**
     * Enable Import Of New Local Objects
     *
     * @var bool
     */
    protected static bool $enablePullCreated = true;

    /**
     * Enable Import of Updates of Local Objects when Modified Locally
     *
     * @var bool
     */
    protected static bool $enablePullUpdated = true;

    /**
     * Enable Delete Of Remotes Objects when Deleted Locally
     *
     * @var bool
     */
    protected static bool $enablePullDeleted = true;

    //====================================================================//
    //  COMMON CLASS INFORMATION
    //====================================================================//

    /**
     * Return type of this Object Class
     *
     * @return string
     */
    public function getType(): string
    {
        $obj = new ReflectionClass($this);

        return pathinfo((string) $obj->getFileName(), PATHINFO_FILENAME);
    }

    /**
     * Return name of this Object Class
     *
     * @return string
     */
    public function getName(): string
    {
        return self::trans(static::$name);
    }

    /**
     * Return Description of this Object Class
     *
     * @return string
     */
    public function getDesc(): string
    {
        return self::trans(static::$description);
    }

    /**
     * {@inheritdoc}
     */
    public static function isDisabled(): bool
    {
        return static::$disabled;
    }

    /**
     * Return Object Icon
     *
     * @return string
     */
    public static function getIcon(): string
    {
        return static::$ico;
    }

    /**
     * {@inheritdoc}
     */
    public function description(): array
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();

        //====================================================================//
        // Build & Return Object Description Array
        $description = array(
            //====================================================================//
            // General Object definition
            //====================================================================//
            // Object Type Name
            Props::TYPE => $this->getType(),
            // Object Display Name
            Props::NAME => $this->getName(),
            // Object Description
            Props::DESC => $this->getDesc(),
            // Object Icon Class (Font Awesome or Glyph. i.e. "fa fa-user")
            Props::ICON => $this->getIcon(),
            // Is This Object Enabled or Not?
            Props::DISABLED => $this->isDisabled(),
            //====================================================================//
            // Object Limitations
            Props::ALLOW_CREATE => static::$allowPushCreated,
            Props::ALLOW_UPDATE => static::$allowPushUpdated,
            Props::ALLOW_DELETE => static::$allowPushDeleted,
            //====================================================================//
            // Object Default Configuration
            Props::PUSH_CREATED => static::$enablePushCreated,
            Props::PUSH_UPDATED => static::$enablePushUpdated,
            Props::PUSH_DELETED => static::$enablePushDeleted,
            Props::PULL_CREATED => static::$enablePullCreated,
            Props::PULL_UPDATED => static::$enablePullUpdated,
            Props::PULL_DELETED => static::$enablePullDeleted
        );

        //====================================================================//
        // Apply Overrides & Return Object Description Array
        return Splash::configurator()->overrideDescription(static::getType(), $description);
    }
}
