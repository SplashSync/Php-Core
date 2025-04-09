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

namespace Splash\Core\Models\Components\Validator;

use Exception;
use Splash\Core\Client\Splash;
use Splash\Core\Dictionary\SplDefinition;
use Splash\Core\Interfaces\Local\WidgetsProviderInterface;
use Splash\Core\Interfaces\Widgets\WidgetInterface;

/**
 * Collection of Validator Methods Focused on Widgets Classes
 */
trait WidgetsValidatorTrait
{
    /**
     * List of Validated Local Widgets
     *
     * @var array<string, bool>
     */
    private array $validWidgetTypes = array();

    /**
     * Verify this parameter is a valid widget type name
     *
     * @param string $widgetType Widget Class/Type Name
     *
     * @return bool
     */
    public function isValidWidget(string $widgetType): bool
    {
        //====================================================================//
        // Verify Result in Cache
        if (isset($this->validWidgetTypes[$widgetType])) {
            return $this->validWidgetTypes[$widgetType];
        }
        $this->validWidgetTypes[$widgetType] = false;

        //====================================================================//
        // Verify Local Core Class Exist & Is Valid
        if (!$this->isValidLocalClass()) {
            return false;
        }

        //====================================================================//
        // Check if Widget Manager has NOT Overrides
        try {
            if (!(Splash::local() instanceof WidgetsProviderInterface)) {
                //====================================================================//
                // Verify Widget File Exist & is Valid
                if (!$this->isValidWidgetFile($widgetType)) {
                    return false;
                }
            }
        } catch (Exception $e) {
            return Splash::log()->errTrace($e->getMessage());
        }

        //====================================================================//
        // Verify Widget Class Exist & is Valid
        return $this->validWidgetTypes[$widgetType] = $this->isValidWidgetClass($widgetType);
    }

    /**
     * Verify a Local Widget File is Valid.
     *
     * @param string $widgetType Widget Type Name
     *
     * @return bool
     */
    private function isValidWidgetFile(string $widgetType): bool
    {
        //====================================================================//
        // Verify Local Path Exist
        if (!$this->isValidLocalPath()) {
            return false;
        }
        //====================================================================//
        // Guess Widget File Path
        $filename = realpath(Splash::getLocalPath().'/Widgets/'.$widgetType.'.php');
        //====================================================================//
        // Verify Widget File Exist
        if (!$filename || !file_exists($filename)) {
            $msg = 'Local Widget File Not Found.</br>';
            $msg .= 'Current Filename : '.$filename;

            return Splash::log()->err($msg);
        }

        return true;
    }

    /**
     * Verify Availability of a Local Widget Class.
     *
     * @param string $widgetType Widget Type Name
     *
     * @return bool
     */
    private function isValidWidgetClass(string $widgetType): bool
    {
        //====================================================================//
        // Check if Widget Manager has Override
        try {
            $local = Splash::local();
            if ($local instanceof WidgetsProviderInterface) {
                //====================================================================//
                // Retrieve Widget Manager ClassName
                $className = get_class($local->widget($widgetType));
            } else {
                //====================================================================//
                // Guess Widget ClassName
                $className = SplDefinition::WIDGETS_PREFIX.$widgetType;
            }
        } catch (Exception $e) {
            return Splash::log()->errTrace($e->getMessage());
        }

        //====================================================================//
        // Verify Splash Local Core Class Exists
        if (!class_exists($className)) {
            return Splash::log()->err(Splash::trans('ErrLocalClass', $widgetType));
        }
        //====================================================================//
        // Verify Local Widget Class Implements WidgetInterface
        if (!is_subclass_of($className, WidgetInterface::class)) {
            return Splash::log()->err(
                Splash::trans('ErrLocalClass', $className, WidgetInterface::class)
            );
        }

        //====================================================================//
        // Read Object Disable Flag
        return !$className::isDisabled();
    }
}
