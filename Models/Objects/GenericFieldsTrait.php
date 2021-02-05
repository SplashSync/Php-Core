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

namespace Splash\Models\Objects;

use DateTime;

/**
 * Generic Doctrine Objects Fields Read & Write Helper
 */
trait GenericFieldsTrait
{
    use ObjectsTrait;

    /**
     * Convert Local Object to Splash ObjectId String
     *
     * @param string $fieldName  Field Identifier
     * @param string $objectType Splash Object Type
     * @param string $objectName Name of private object to read (Default : "object")
     *
     * @return self
     */
    protected function getGenericObject($fieldName, $objectType, $objectName = "object")
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
    protected function setGenericObject($fieldName, $fieldData, $objectName = "object", $nullable = true)
    {
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
                $this->{$objectName}->{ "set".$fieldName}(null);
                $this->needUpdate();
            }

            return $this;
        }
        //====================================================================//
        // Update Pointed Object to New Value
        $this->{$objectName}->{ "set".$fieldName}($fieldData);
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
    protected function getGeneric($fieldName, $objectName = "object")
    {
        $this->out[$fieldName] = $this->{$objectName}->{ "get".$fieldName}();

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
    protected function setGeneric($fieldName, $fieldData, $objectName = "object")
    {
        //====================================================================//
        //  Compare Field Data
        $current = $this->{$objectName}->{ "get".$fieldName}();
        if ($current == $fieldData) {
            return $this;
        }
        //====================================================================//
        //  Update Field Data
        $this->{$objectName}->{ "set".$fieldName}($fieldData);
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
    protected function getGenericBool($fieldName, $objectName = "object")
    {
        $this->out[$fieldName] = $this->{$objectName}->{ "is".$fieldName}();

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
    protected function setGenericBool($fieldName, $fieldData, $objectName = "object")
    {
        //====================================================================//
        //  Compare Field Data
        $current = $this->{$objectName}->{ "is".$fieldName}();
        if ($current == $fieldData) {
            return $this;
        }
        //====================================================================//
        //  Update Field Data
        $this->{$objectName}->{ "set".$fieldName}((bool) $fieldData);
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
    protected function getGenericDate($fieldName, $objectName = "object")
    {
        $date = $this->{$objectName}->{ "get".$fieldName}();
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
     * @return self
     */
    protected function setGenericDate($fieldName, $fieldData, $objectName = "object")
    {
        //====================================================================//
        //  Compare Field Data
        $current = $this->{$objectName}->{ "get".$fieldName}();
        if (($current instanceof DateTime) && ($current->format(SPL_T_DATECAST) == $fieldData)) {
            return $this;
        }
        //====================================================================//
        //  Update Field Data
        $this->{$objectName}->{ "set".$fieldName}($fieldData ? new DateTime($fieldData) : null);
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
    protected function getGenericDateTime($fieldName, $objectName = "object")
    {
        $date = $this->{$objectName}->{ "get".$fieldName}();
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
     * @return self
     */
    protected function setGenericDateTime($fieldName, $fieldData, $objectName = "object")
    {
        //====================================================================//
        //  Compare Field Data
        $current = $this->{$objectName}->{ "get".$fieldName}();
        if (($current instanceof DateTime) && ($current->format(SPL_T_DATETIMECAST) == $fieldData)) {
            return $this;
        }
        //====================================================================//
        //  Update Field Data
        $this->{$objectName}->{ "set".$fieldName}($fieldData ? new DateTime($fieldData) : null);
        $this->needUpdate($objectName);

        return $this;
    }

    /**
     * Convert Local Object to Splash ObjectId String
     *
     * @param string $fieldName  Field Identifier
     * @param string $objectName Name of private object to read (Default : "object")
     *
     * @return null|string
     */
    private function getObjectId($fieldName, $objectName = "object")
    {
        //====================================================================//
        // Load Pointed Object
        $object = $this->{$objectName}->{ "get".$fieldName}();
        //====================================================================//
        // Check Pointed Object Exists & Has an Id
        if (!$object || !method_exists($object, "getId") || !$object->getId()) {
            return null;
        }
        //====================================================================//
        // Return Object Id
        return (string) $object->getId();
    }
}
