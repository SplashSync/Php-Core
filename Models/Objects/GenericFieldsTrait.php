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

namespace Splash\Models\Objects;

use DateTime;
use Exception;

/**
 * Generic Doctrine Objects Fields Read & Write Helper
 */
trait GenericFieldsTrait
{
    use ObjectsTrait;

    /**
     * Field name to method Parsing Format
     *
     * @var string
     */
    private static $methodFormat = "camelCase";

    /**
     * Available Parsing methods
     *
     * @var array
     */
    private static $allowedFormats = array(
        "camelCase", "PascalCase", "snake_case"
    );

    /**
     * Convert Local Object to Splash ObjectId String
     *
     * @param string $fieldName  Field Identifier
     * @param string $objectType Splash Object Type
     * @param string $objectName Name of private object to read (Default : "object")
     *
     * @return self
     */
    protected function getGenericObject(string $fieldName, string $objectType, string $objectName = "object"): self
    {
        //====================================================================//
        // Load Pointed Object Id
        $objectId = $this->getObjectId($fieldName, $objectName);
        //====================================================================//
        // Push Object Id to Buffer
        $this->out[$fieldName] = $objectId
                ? self::objects()->encode($objectType, $objectId)
                : null;

        return $this;
    }

    /**
     * Convert Splash ObjectId String to Local Object
     *
     * @param string $fieldName  Field Identifier
     * @param mixed  $fieldData  New Target Object
     * @param string $objectName Name of private object to read (Default : "object")
     * @param bool   $nullable   Can we set Object to Null
     *
     * @return self
     */
    protected function setGenericObject(
        string $fieldName,
        $fieldData,
        string $objectName = "object",
        bool $nullable = true
    ): self {
        //====================================================================//
        // Load New Object Id
        $newId = null;
        if ($fieldData && method_exists($fieldData, "getId") && $fieldData->getId()) {
            $newId = $fieldData->getId();
        }
        //====================================================================//
        // Read Current Object Id
        $currentId = $this->getObjectId($fieldName, $objectName);
        //====================================================================//
        // No Changes
        if ($newId == $currentId) {
            return $this;
        }
        //====================================================================//
        // Check Pointed Object Exists & Has an Id
        if (null == $newId) {
            if ($nullable) {
                //====================================================================//
                // Set Pointed Object to Null
                $this->{$objectName}->{ "set".self::toMethod($fieldName) }(null);
                $this->needUpdate();
            }

            return $this;
        }
        //====================================================================//
        // Update Pointed Object to New Value
        $this->{$objectName}->{ "set".self::toMethod($fieldName)}($fieldData);
        $this->needUpdate();

        return $this;
    }

    /**
     * Common reading of a Field using Generic Getters & Setters
     *
     * @param string $fieldName  Suffix for Getter & Setter (ie: Product => getProduct() & setProduct())
     * @param string $objectName Name of private object to read (Default : "object")
     *
     * @return self
     */
    protected function getGeneric(string $fieldName, string $objectName = "object"): self
    {
        $this->out[$fieldName] = $this->{$objectName}->{ "get".self::toMethod($fieldName)}();

        return $this;
    }

    /**
     * Common Writing of a Field using Generic Getters & Setters
     *
     * @param string $fieldName  Suffix for Getter & Setter (ie: Product => getProduct() & setProduct())
     * @param mixed  $fieldData  Field Data
     * @param string $objectName Name of private object to read (Default : "object")
     *
     * @return self
     */
    protected function setGeneric(string $fieldName, $fieldData, string $objectName = "object"): self
    {
        //====================================================================//
        //  Compare Field Data
        $current = $this->{$objectName}->{ "get".self::toMethod($fieldName)}();
        if ($current == $fieldData) {
            return $this;
        }
        //====================================================================//
        //  Update Field Data
        $this->{$objectName}->{ "set".self::toMethod($fieldName)}($fieldData);
        $this->needUpdate($objectName);

        return $this;
    }

    /**
     * Common reading of a Field using Generic Boolean Getters & Setters
     *
     * @param string $fieldName  Suffix for Getter & Setter (ie: Product => getProduct() & setProduct())
     * @param string $objectName Name of private object to read (Default : "object")
     *
     * @return self
     */
    protected function getGenericBool(string $fieldName, string $objectName = "object"): self
    {
        $this->out[$fieldName] = $this->{$objectName}->{ "is".self::toMethod($fieldName)}();

        return $this;
    }

    /**
     * Common Writing of a Field using Generic Getters & Setters
     *
     * @param string $fieldName  Suffix for Getter & Setter (ie: Product => getProduct() & setProduct())
     * @param mixed  $fieldData  Field Data
     * @param string $objectName Name of private object to read (Default : "object")
     *
     * @return self
     */
    protected function setGenericBool(string $fieldName, $fieldData, string $objectName = "object"): self
    {
        //====================================================================//
        //  Compare Field Data
        $current = $this->{$objectName}->{ "is".self::toMethod($fieldName)}();
        if ($current == $fieldData) {
            return $this;
        }
        //====================================================================//
        //  Update Field Data
        $this->{$objectName}->{ "set".self::toMethod($fieldName)}((bool) $fieldData);
        $this->needUpdate($objectName);

        return $this;
    }

    /**
     * Common reading of a Date Field using Generic Getters & Setters
     *
     * @param string $fieldName  Suffix for Getter & Setter (ie: Product => getProduct() & setProduct())
     * @param string $objectName Name of private object to read (Default : "object")
     *
     * @return self
     */
    protected function getGenericDate(string $fieldName, string $objectName = "object"): self
    {
        $date = $this->{$objectName}->{ "get".self::toMethod($fieldName)}();
        $this->out[$fieldName] = $date ? $date->format(SPL_T_DATECAST) : "";

        return $this;
    }

    /**
     * Common Writing of a Date Field using Generic Getters & Setters
     *
     * @param string $fieldName  Suffix for Getter & Setter (ie: Product => getProduct() & setProduct())
     * @param mixed  $fieldData  Field Data
     * @param string $objectName Name of private object to read (Default : "object")
     *
     * @throws Exception
     *
     * @return self
     */
    protected function setGenericDate(string $fieldName, $fieldData, string $objectName = "object"): self
    {
        //====================================================================//
        //  Compare Field Data
        $current = $this->{$objectName}->{ "get".self::toMethod($fieldName)}();
        if (($current instanceof DateTime) && ($current->format(SPL_T_DATECAST) == $fieldData)) {
            return $this;
        }
        //====================================================================//
        //  Update Field Data
        $this->{$objectName}->{ "set".self::toMethod($fieldName)}($fieldData ? new DateTime($fieldData) : null);
        $this->needUpdate($objectName);

        return $this;
    }

    /**
     * Common reading of a DateTime Field using Generic Getters & Setters
     *
     * @param string $fieldName  Suffix for Getter & Setter (ie: Product => getProduct() & setProduct())
     * @param string $objectName Name of private object to read (Default : "object")
     *
     * @return self
     */
    protected function getGenericDateTime(string $fieldName, string $objectName = "object"): self
    {
        $date = $this->{$objectName}->{ "get".self::toMethod($fieldName)}();
        $this->out[$fieldName] = $date ? $date->format(SPL_T_DATETIMECAST) : "";

        return $this;
    }

    /**
     * Common Writing of a DateTime Field using Generic Getters & Setters
     *
     * @param string $fieldName  Suffix for Getter & Setter (ie: Product => getProduct() & setProduct())
     * @param mixed  $fieldData  Field Data
     * @param string $objectName Name of private object to read (Default : "object")
     *
     * @throws Exception
     *
     * @return self
     */
    protected function setGenericDateTime(string $fieldName, $fieldData, string $objectName = "object"): self
    {
        //====================================================================//
        //  Compare Field Data
        $current = $this->{$objectName}->{ "get".self::toMethod($fieldName)}();
        if (($current instanceof DateTime) && ($current->format(SPL_T_DATETIMECAST) == $fieldData)) {
            return $this;
        }
        //====================================================================//
        //  Update Field Data
        $this->{$objectName}->{ "set".self::toMethod($fieldName)}($fieldData ? new DateTime($fieldData) : null);
        $this->needUpdate($objectName);

        return $this;
    }

    /**
     * Select Method Name Building Format
     *
     * @param string $format Method Name Format
     *
     * @throws Exception
     *
     * @return void
     */
    protected static function setGenericMethodsFormat(string $format): void
    {
        //====================================================================//
        // Safety Check
        if (!in_array($format, self::$allowedFormats, true)) {
            throw new Exception(sprintf("Method Name Building Format is invalid: %s", $format));
        }
        self::$methodFormat = $format;
    }

    /**
     * Convert Local Object to Splash ObjectId String
     *
     * @param string $fieldName  Field Identifier
     * @param string $objectName Name of private object to read (Default : "object")
     *
     * @return null|string
     */
    private function getObjectId(string $fieldName, string $objectName = "object"): ?string
    {
        //====================================================================//
        // Load Pointed Object
        $object = $this->{$objectName}->{ "get".self::toMethod($fieldName)}();
        //====================================================================//
        // Check Pointed Object Exists & Has an Id
        if (!$object || !method_exists($object, "getId") || !$object->getId()) {
            return null;
        }
        //====================================================================//
        // Return Object Id
        return (string) $object->getId();
    }

    /**
     * Convert FieldName to Generic Method Name
     *
     * @param string $fieldName Suffix for Getter & Setter (ie: Product => getProduct() & setProduct())
     *
     * @return string
     */
    private static function toMethod(string $fieldName): string
    {
        switch (self::$methodFormat) {
            case "snake_case":
                return $fieldName;
            default:
            case "camelCase":
            case "PascalCase":
                return ucwords(str_replace("_", "", $fieldName));
        }
    }
}
