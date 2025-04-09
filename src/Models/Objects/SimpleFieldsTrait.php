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

namespace Splash\Core\Models\Objects;

/**
 * Implement Generic Access to Object Simple Fields (Properties)
 */
trait SimpleFieldsTrait
{
    /**
     * Common Reading of a Single Field
     *
     * @param string     $fieldName  Field Identifier / Name
     * @param string     $objectName Name of private object to read (Default : "object")
     * @param null|mixed $default    Default Value if unset
     */
    protected function getSimple(string $fieldName, string $objectName = "object", mixed $default = null): self
    {
        if (isset($this->{$objectName}->{$fieldName})) {
            $this->out[$fieldName] = trim($this->{$objectName}->{$fieldName});
        } else {
            $this->out[$fieldName] = $default;
        }

        return $this;
    }

    /**
     * Common Reading of a Single Bool Field
     *
     * @param string $fieldName  Field Identifier / Name
     * @param string $objectName Name of private object to read (Default : "object")
     * @param bool   $default    Default Value if unset
     */
    protected function getSimpleBool(string $fieldName, string $objectName = "object", bool $default = false): self
    {
        if (isset($this->{$objectName}->{$fieldName})) {
            $this->out[$fieldName] = (bool) trim($this->{$objectName}->{$fieldName});
        } else {
            $this->out[$fieldName] = $default;
        }

        return $this;
    }

    /**
     * Common Reading of a Single Double Field
     *
     * @param string $fieldName  Field Identifier / Name
     * @param string $objectName Name of private object to read (Default : "object")
     * @param float  $default    Default Value if unset
     */
    protected function getSimpleDouble(string $fieldName, string $objectName = "object", float $default = 0.0): self
    {
        if (isset($this->{$objectName}->{$fieldName})) {
            $this->out[$fieldName] = (double) trim($this->{$objectName}->{$fieldName});
        } else {
            $this->out[$fieldName] = (double) $default;
        }

        return $this;
    }

    /**
     * Common Reading of a Single Bit Field
     *
     * @param string $fieldName  Field Identifier / Name
     * @param int    $position   Byte position (Starting form 0)
     * @param string $objectName Name of private object to read (Default : "object")
     * @param mixed  $default    Default Value if unset
     */
    protected function getSimpleBit(
        string $fieldName,
        int $position,
        string $objectName = "object",
        bool $default = false
    ): self {
        if (isset($this->{$objectName}->{$fieldName})) {
            $this->out[$fieldName] = (bool) (($this->{$objectName}->{$fieldName} >> $position) & 1);
        } else {
            $this->out[$fieldName] = (bool) $default;
        }

        return $this;
    }

    /**
     * Common Writing of a Single Field
     *  => If Field Needs to be Updated, do Object Update & Set $this->update to true
     *
     * @param string $fieldName  Field Identifier / Name
     * @param mixed  $fieldData  Field Data
     * @param string $objectName Name of private object to read (Default : "object")
     */
    protected function setSimple(string $fieldName, mixed $fieldData, string $objectName = "object"): self
    {
        //====================================================================//
        //  Compare Field Data
        if (!isset($this->{$objectName}->{$fieldName}) || ($this->{$objectName}->{$fieldName} != $fieldData)) {
            //====================================================================//
            //  Update Field Data
            $this->{$objectName}->{$fieldName} = $fieldData;
            $this->needUpdate($objectName);
        }

        return $this;
    }

    /**
     * Common Writing of a Single Field
     *
     * @param string $fieldName  Field Identifier / Name
     * @param mixed  $fieldData  Field Data
     * @param string $objectName Name of private object to read (Default : "object")
     */
    protected function setSimpleFloat(string $fieldName, mixed $fieldData, string $objectName = "object"): self
    {
        //====================================================================//
        //  Compare Field Data
        if (!isset($this->{$objectName}->{$fieldName})
                || (abs($this->{$objectName}->{$fieldName} - $fieldData) > 1E-6)) {
            //====================================================================//
            //  Update Field Data
            $this->{$objectName}->{$fieldName} = $fieldData;
            $this->needUpdate($objectName);
        }

        return $this;
    }

    /**
     * Common Writing of a Single Bit Field
     *
     * @param string $fieldName  Field Identifier / Name
     * @param int    $position   Byte position (Starting form 0)
     * @param mixed  $fieldData  Field Data
     * @param string $objectName Name of private object to read (Default : "object")
     */
    protected function setSimpleBit(
        string $fieldName,
        int $position,
        mixed $fieldData,
        string $objectName = "object"
    ): self {
        $current = (bool) (($this->{$objectName}->{$fieldName} ?? 0 >> $position) & 1);
        $new = !empty($fieldData);
        //====================================================================//
        // Compare Field Data
        if ($current !== $new) {
            //====================================================================//
            // Update Field Data
            if ($new) {
                $this->{$objectName}->{$fieldName} = $this->{$objectName}->{$fieldName} | (1 << $position);
            } else {
                $this->{$objectName}->{$fieldName} = $this->{$objectName}->{$fieldName} & ~ (1 << $position);
            }
            $this->needUpdate($objectName);
        }

        return $this;
    }
}
