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

/**
 * @abstract    Implement Generic Access to Object Simple Fields
 */
trait SimpleFieldsTrait
{

    /**
     *  @abstract     Common Reading of a Single Field
     *
     *  @param        string    $fieldName              Field Identifier / Name
     *  @param        string    $objectName             Name of private object to read (Default : "object")
     *  @param        mixed     $default                Default Value if unset
     *
     *  @return       self
     */
    protected function getSimple($fieldName, $objectName = "Object", $default = null)
    {
        if (isset($this->{$objectName}->$fieldName)) {
            $this->Out[$fieldName] = trim($this->{$objectName}->$fieldName);
        } else {
            $this->Out[$fieldName] = $default;
        }
        return $this;
    }
    
    /**
     *  @abstract     Common Reading of a Single Bool Field
     *
     *  @param        string    $fieldName              Field Identifier / Name
     *  @param        string    $objectName             Name of private object to read (Default : "object")
     *  @param        mixed     $default                Default Value if unset
     *
     *  @return       self
     */
    protected function getSimpleBool($fieldName, $objectName = "Object", $default = false)
    {
        if (isset($this->{$objectName}->$fieldName)) {
            $this->Out[$fieldName] = (bool) trim($this->{$objectName}->$fieldName);
        } else {
            $this->Out[$fieldName] = (bool) $default;
        }
        return $this;
    }
    
    /**
     *  @abstract     Common Reading of a Single Double Field
     *
     *  @param        string    $fieldName              Field Identifier / Name
     *  @param        string    $objectName             Name of private object to read (Default : "object")
     *  @param        mixed     $default                Default Value if unset
     *
     *  @return       self
     */
    protected function getSimpleDouble($fieldName, $objectName = "Object", $default = 0)
    {
        if (isset($this->{$objectName}->$fieldName)) {
            $this->Out[$fieldName] = (double) trim($this->{$objectName}->$fieldName);
        } else {
            $this->Out[$fieldName] = (double) $default;
        }
        return $this;
    }
    
    /**
     *  @abstract     Common Reading of a Single Bit Field
     *
     *  @param        string    $fieldName              Field Identifier / Name
     *  @param        int       $position               Bit position (Starting form 0)
     *  @param        string    $objectName             Name of private object to read (Default : "object")
     *  @param        mixed     $default                Default Value if unset
     *
     *  @return       self
     */
    protected function getSimpleBit($fieldName, $position, $objectName = "Object", $default = false)
    {
        if (isset($this->{$objectName}->$fieldName)) {
            $this->Out[$fieldName] = (bool) (($this->{$objectName}->$fieldName >> $position) & 1);
        } else {
            $this->Out[$fieldName] = (bool) $default;
        }
        return $this;
    }
    
    /**
     *  @abstract     Common Reading of a Single Field
     *                  => If Field Needs to be Updated, do Object Update & Set $this->update to true
     *
     *  @param        string    $fieldName              Field Identifier / Name
     *  @param        mixed     $fieldData              Field Data
     *  @param        string    $objectName             Name of private object to read (Default : "object")
     *
     *  @return       self
     */
    protected function setSimple($fieldName, $fieldData, $objectName = "Object")
    {
        //====================================================================//
        //  Compare Field Data
        if (!isset($this->{$objectName}->$fieldName) || ($this->{$objectName}->$fieldName != $fieldData)) {
            //====================================================================//
            //  Update Field Data
            $this->{$objectName}->$fieldName = $fieldData;
            $this->needUpdate($objectName);
        }
        return $this;
    }
    
    /**
     *  @abstract     Common Writing of a Single Field
     *
     *  @param        string    $fieldName              Field Identifier / Name
     *  @param        mixed     $fieldData              Field Data
     *  @param        string    $objectName             Name of private object to read (Default : "object")
     *
     *  @return       self
     */
    protected function setSimpleFloat($fieldName, $fieldData, $objectName = "Object")
    {
        //====================================================================//
        //  Compare Field Data
        if (!isset($this->{$objectName}->$fieldName) || (abs($this->{$objectName}->$fieldName - $fieldData) > 1E-6)) {
            //====================================================================//
            //  Update Field Data
            $this->{$objectName}->$fieldName = $fieldData;
            $this->needUpdate($objectName);
        }
        return $this;
    }

    /**
     *  @abstract     Common Writing of a Single Bit Field
     *
     *  @param        string    $fieldName              Field Identifier / Name
     *  @param        int       $position               Bit position (Starting form 0)
     *  @param        mixed     $fieldData              Field Data
     *  @param        string    $objectName             Name of private object to read (Default : "object")
     *
     *  @return       self
     */
    protected function setSimpleBit($fieldName, $position, $fieldData, $objectName = "Object")
    {
        //====================================================================//
        //  Compare Field Data
        if ($this->getSimpleBit($fieldName, $position, $objectName) !== $fieldData) {
            //====================================================================//
            //  Update Field Data
            if ($fieldData) {
                $this->{$objectName}->$fieldName =  $this->{$objectName}->$fieldName | (1 << $position);
            } else {
                $this->{$objectName}->$fieldName =  $this->{$objectName}->$fieldName & ~ (1 << $position);
            }
            $this->needUpdate($objectName);
        }
        return $this;
    }
    
    /**
     * @abstract    Common reading of a Field using Generic Getters & Setters
     *
     * @param   string  $fieldName      Suffix for Getter & Setter (ie: Product => getProduct() & setProduct())
     * @param   string  $objectName     Name of private object to read (Default : "object")
     *
     * @return  self
     */
    protected function getGeneric($fieldName, $objectName = "Object")
    {
        $this->Out[$fieldName] = $this->{$objectName}->{ "get" . $fieldName}();
        return $this;
    }
    
    /**
     * @abstract    Common Writing of a Field using Generic Getters & Setters
     *
     * @param       string      $fieldName      Suffix for Getter & Setter (ie: Product => getProduct() & setProduct())
     * @param       mixed       $fieldData      Field Data
     * @param       string      $objectName     Name of private object to read (Default : "object")
     *
     * @return      self
     */
    protected function setGeneric($fieldName, $fieldData, $objectName = "Object")
    {
        //====================================================================//
        //  Compare Field Data
        $current    =   $this->{$objectName}->{ "get" . $fieldName}();
        if ($current == $fieldData) {
            return $this;
        }
        //====================================================================//
        //  Update Field Data
        $this->{$objectName}->{ "set" . $fieldName}($fieldData);
        $this->needUpdate($objectName);
        return $this;
    }
}
