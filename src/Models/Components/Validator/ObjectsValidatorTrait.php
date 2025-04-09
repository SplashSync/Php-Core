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
use Splash\Core\Components\ExtensionsManager;
use Splash\Core\Dictionary\SplDefinition;
use Splash\Core\Interfaces\Local\ObjectsProviderInterface;
use Splash\Core\Interfaces\Object\ObjectInterface;

/**
 * Collection of Validator Methods Focused on Objects Classes
 */
trait ObjectsValidatorTrait
{
    /**
     * List of Validated Local Objects
     *
     * @var array<string, bool>
     */
    private array $validObjectTypes = array();

    /**
     * Verify this parameter is a valid object type name
     *
     * @param string $objectType Object Class/Type Name
     *
     * @return bool
     */
    public function isValidObject(string $objectType): bool
    {
        //====================================================================//
        // Verify Result in Cache
        if (isset($this->validObjectTypes[$objectType])) {
            return $this->validObjectTypes[$objectType];
        }

        $this->validObjectTypes[$objectType] = false;
        //====================================================================//
        // Check if Object Manager is Extension Object
        if (in_array($objectType, array_keys(ExtensionsManager::getObjects()), true)) {
            return $this->validObjectTypes[$objectType] = true;
        }
        //====================================================================//
        // Verify Local Core Class Exist & Is Valid
        if (!$this->isValidLocalClass()) {
            return false;
        }

        //====================================================================//
        // Check if Object Manager has NO Override
        try {
            if (!(Splash::local() instanceof ObjectsProviderInterface)) {
                //====================================================================//
                // Verify Object File Exist & is Valid
                if (!$this->isValidObjectFile($objectType)) {
                    return false;
                }
            }
        } catch (Exception $e) {
            return Splash::log()->errTrace($e->getMessage());
        }

        //====================================================================//
        // Verify Object Class Exist & is Valid
        return $this->validObjectTypes[$objectType] = $this->isValidObjectClass($objectType);
    }

    /**
     * Verify this Object Type is valid in Local System
     *
     * @param string $objectType Object Type Name String
     */
    public static function isValidObjectType(string $objectType): bool
    {
        try {
            return in_array($objectType, Splash::objects(), true);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Verify Object Identifier
     *
     * @param mixed $objectId Object Identifier
     *
     * @return bool
     */
    public function isValidObjectId(mixed $objectId): bool
    {
        //====================================================================//
        // Checks Id is not Null
        if (is_null($objectId)) {
            return Splash::log()->err('ErrEmptyObjectId');
        }
        //====================================================================//
        // Checks Id is String or Int
        if (!is_string($objectId) && !is_numeric($objectId)) {
            return Splash::log()->err('ErrWrongObjectId');
        }
        //====================================================================//
        // Checks List Not Empty
        if (is_numeric($objectId) && ($objectId < 0)) {
            return Splash::log()->err('ErrNegObjectId');
        }

        return Splash::log()->deb('MsgObjectIdOk');
    }

    /**
     * Verify Object Field List
     *
     * @param null|array $fieldsList Object Field List
     *
     * @return bool
     */
    public function isValidObjectFieldsList(?array $fieldsList): bool
    {
        //====================================================================//
        // Checks List Not Empty
        if (empty($fieldsList)) {
            return Splash::log()->err('ErrEmptyFieldList');
        }

        return Splash::log()->deb('MsgFieldListOk');
    }

    /**
     * Verify a Local Object File is Valid.
     *
     * @param string $objectType Object Type Name
     *
     * @return bool
     */
    private function isValidObjectFile(string $objectType): bool
    {
        //====================================================================//
        // Verify Local Path Exist
        if (!$this->isValidLocalPath()) {
            return false;
        }
        //====================================================================//
        // Guess Object File Path
        $filename = realpath(Splash::getLocalPath().'/Objects/'.$objectType.'.php');
        //====================================================================//
        // Verify Object File Exist
        if (!$filename || !file_exists($filename)) {
            $msg = 'Local Object File Not Found.</br>';
            $msg .= 'Current Filename : '.$filename;

            return Splash::log()->err($msg);
        }

        return true;
    }

    /**
     * Verify Availability of a Local Object Class.
     *
     * @param string $objectType Object Type Name
     *
     * @return bool
     */
    private function isValidObjectClass(string $objectType): bool
    {
        //====================================================================//
        // Check if Object Manager has Overrides
        try {
            $local = Splash::local();
            if ($local instanceof ObjectsProviderInterface) {
                //====================================================================//
                // Retrieve Object Manager ClassName
                $className = get_class($local->object($objectType));
            } else {
                //====================================================================//
                // Guess Object ClassName
                $className = SplDefinition::OBJECTS_PREFIX.$objectType;
            }
        } catch (Exception $e) {
            return Splash::log()->errTrace($e->getMessage());
        }
        //====================================================================//
        // Verify Splash Local Core Class Exists
        if (!class_exists($className)) {
            return Splash::log()->err(Splash::trans('ErrLocalClass', $objectType));
        }
        //====================================================================//
        // Verify Local Object Class Implements ObjectInterface
        if (!is_subclass_of($className, ObjectInterface::class)) {
            return Splash::log()->err(
                Splash::trans('ErrLocalClass', $className, ObjectInterface::class)
            );
        }
        //====================================================================//
        // Check Object Disable Flag with Configurator Overrides)
        if (Splash::configurator()->isDisabled($objectType, $className::isDisabled())) {
            return false;
        }

        return true;
    }
}
