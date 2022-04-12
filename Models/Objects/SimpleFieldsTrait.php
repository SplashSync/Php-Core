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

namespace   Splash\Models\Objects;

/**
 * Implement Generic Access to Object Simple Fields
 */
trait SimpleFieldsTrait
{
    /**
     * Common Reading of a Single Field
     *
     * @param string $fieldName  Field Identifier / Name
     * @param string $objectName Name of private object to read (Default : "object")
     * @param mixed  $default    Default Value if unset
     *
     * @return self
     */
    protected function getSimple($fieldName, $objectName = "object", $default = null)
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
     * @param mixed  $default    Default Value if unset
     *
     * @return self
     */
    protected function getSimpleBool($fieldName, $objectName = "object", $default = false)
    {
        if (isset($this->{$objectName}->{$fieldName})) {
            $this->out[$fieldName] = (bool) trim($this->{$objectName}->{$fieldName});
        } else {
            $this->out[$fieldName] = (bool) $default;
        }

        return $this;
    }

    /**
     * Common Reading of a Single Double Field
     *
     * @param string $fieldName  Field Identifier / Name
     * @param string $objectName Name of private object to read (Default : "object")
     * @param mixed  $default    Default Value if unset
     *
     * @return self
     */
    protected function getSimpleDouble($fieldName, $objectName = "object", $default = 0)
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
     * @param int    $position   Bit position (Starting form 0)
     * @param string $objectName Name of private object to read (Default : "object")
     * @param mixed  $default    Default Value if unset
     *
     * @return self
     */
    protected function getSimpleBit($fieldName, $position, $objectName = "object", $default = false)
    {
        if (isset($this->{$objectName}->{$fieldName})) {
            $this->out[$fieldName] = (bool) (($this->{$objectName}->{$fieldName} >> $position) & 1);
        } else {
            $this->out[$fieldName] = (bool) $default;
        }

        return $this;
    }

    /**
     * Common Reading of a Single Field
     *                  => If Field Needs to be Updated, do Object Update & Set $this->update to true
     *
     * @param string $fieldName  Field Identifier / Name
     * @param mixed  $fieldData  Field Data
     * @param string $objectName Name of private object to read (Default : "object")
     *
     * @return self
     */
    protected function setSimple($fieldName, $fieldData, $objectName = "object")
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
     *
     * @return self
     */
    protected function setSimpleFloat($fieldName, $fieldData, $objectName = "object")
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
     * @param int    $position   Bit position (Starting form 0)
     * @param mixed  $fieldData  Field Data
     * @param string $objectName Name of private object to read (Default : "object")
     *
     * @return self
     */
    protected function setSimpleBit($fieldName, $position, $fieldData, $objectName = "object")
    {
        //====================================================================//
        //  Compare Field Data
        if ($this->getSimpleBit($fieldName, $position, $objectName) !== $fieldData) {
            //====================================================================//
            //  Update Field Data
            if ($fieldData) {
                $this->{$objectName}->{$fieldName} = $this->{$objectName}->{$fieldName} | (1 << $position);
            } else {
                $this->{$objectName}->{$fieldName} = $this->{$objectName}->{$fieldName} & ~ (1 << $position);
            }
            $this->needUpdate($objectName);
        }

        return $this;
    }
}
