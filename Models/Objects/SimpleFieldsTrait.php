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
     *  @param        string    $FieldName              Field Identifier / Name
     *  @param        string    $Object                 Name of private object to read (Default : "object")
     *  @param        mixed     $Default                Default Value if unset
     *
     *  @return       self
     */
    protected function getSimple($FieldName, $Object = "Object", $Default = null)
    {
        if (isset($this->{$Object}->$FieldName)) {
            $this->Out[$FieldName] = trim($this->{$Object}->$FieldName);
        } else {
            $this->Out[$FieldName] = $Default;
        }
        return $this;
    }
    
    /**
     *  @abstract     Common Reading of a Single Bool Field
     *
     *  @param        string    $FieldName              Field Identifier / Name
     *  @param        string    $Object                 Name of private object to read (Default : "object")
     *  @param        mixed     $Default                Default Value if unset
     *
     *  @return       self
     */
    protected function getSimpleBool($FieldName, $Object = "Object", $Default = false)
    {
        if (isset($this->{$Object}->$FieldName)) {
            $this->Out[$FieldName] = (bool) trim($this->{$Object}->$FieldName);
        } else {
            $this->Out[$FieldName] = (bool) $Default;
        }
        return $this;
    }
    
    /**
     *  @abstract     Common Reading of a Single Double Field
     *
     *  @param        string    $FieldName              Field Identifier / Name
     *  @param        string    $Object                 Name of private object to read (Default : "object")
     *  @param        mixed     $Default                Default Value if unset
     *
     *  @return       self
     */
    protected function getSimpleDouble($FieldName, $Object = "Object", $Default = 0)
    {
        if (isset($this->{$Object}->$FieldName)) {
            $this->Out[$FieldName] = (double) trim($this->{$Object}->$FieldName);
        } else {
            $this->Out[$FieldName] = (double) $Default;
        }
        return $this;
    }
    
    /**
     *  @abstract     Common Reading of a Single Bit Field
     *
     *  @param        string    $FieldName              Field Identifier / Name
     *  @param        int       $Position               Bit position (Starting form 0)
     *  @param        string    $Object                 Name of private object to read (Default : "object")
     *  @param        mixed     $Default                Default Value if unset
     *
     *  @return       self
     */
    protected function getSimpleBit($FieldName, $Position, $Object = "Object", $Default = false)
    {
        if (isset($this->{$Object}->$FieldName)) {
            $this->Out[$FieldName] = (bool) (($this->{$Object}->$FieldName >> $Position) & 1);
        } else {
            $this->Out[$FieldName] = (bool) $Default;
        }
        return $this;
    }
    
    /**
     *  @abstract     Common Reading of a Single Field
     *                  => If Field Needs to be Updated, do Object Update & Set $this->update to true
     *
     *  @param        string    $FieldName              Field Identifier / Name
     *  @param        mixed     $Data                   Field Data
     *  @param        string    $Object                 Name of private object to read (Default : "object")
     *
     *  @return       self
     */
    protected function setSimple($FieldName, $Data, $Object = "Object")
    {
        //====================================================================//
        //  Compare Field Data
        if (!isset($this->{$Object}->$FieldName) || ($this->{$Object}->$FieldName != $Data)) {
            //====================================================================//
            //  Update Field Data
            $this->{$Object}->$FieldName = $Data;
            $this->needUpdate();
        }
        return $this;
    }
    
    /**
     *  @abstract     Common Writing of a Single Field
     *
     *  @param        string    $FieldName              Field Identifier / Name
     *  @param        mixed     $Data                   Field Data
     *  @param        string    $Object                 Name of private object to read (Default : "object")
     *
     *  @return       SplashObject
     */
    protected function setSimpleFloat($FieldName, $Data, $Object = "Object")
    {
        //====================================================================//
        //  Compare Field Data
        if (!isset($this->{$Object}->$FieldName) || (abs($this->{$Object}->$FieldName - $Data) > 1E-6)) {
            //====================================================================//
            //  Update Field Data
            $this->{$Object}->$FieldName = $Data;
            $this->needUpdate();
        }
        return $this;
    }

    /**
     *  @abstract     Common Writing of a Single Bit Field
     *
     *  @param        string    $FieldName              Field Identifier / Name
     *  @param        int       $Position               Bit position (Starting form 0)
     *  @param        mixed     $Data                   Field Data
     *  @param        string    $Object                 Name of private object to read (Default : "object")
     *
     *  @return       self
     */
    protected function setSimpleBit($FieldName, $Position, $Data, $Object = "Object")
    {
        
        //====================================================================//
        //  Compare Field Data
        if ($this->getSimpleBit($FieldName, $Position, $Object) !== $Data) {
            //====================================================================//
            //  Update Field Data
            if ($Data) {
                $this->{$Object}->$FieldName =  $this->{$Object}->$FieldName | (1 << $Position);
            } else {
                $this->{$Object}->$FieldName =  $this->{$Object}->$FieldName & ~ (1 << $Position);
            }
            $this->needUpdate();
        }
        
        return $this;
    }
    
    /**
     *  @abstract     Common reading of a Field using Generic Getters & Setters
     *
     *  @param        string    $FieldName  Suffix for Getter & Setter (ie: Product => getProduct() & setProduct())
     *  @param        string    $Object     Name of private object to read (Default : "object")
     *
     *  @return       self
     */
    protected function getGeneric($FieldName, $Object = "Object")
    {
        $this->Out[$FieldName] = $this->{$Object}->{ "get" . $FieldName}();
        return $this;
    }
    
    /**
     *  @abstract     Common Writing of a Field using Generic Getters & Setters
     *
     *  @param        string    $FieldName  Suffix for Getter & Setter (ie: Product => getProduct() & setProduct())
     *  @param        mixed     $Data       Field Data
     *  @param        string    $Object     Name of private object to read (Default : "object")
     *
     *  @return       self
     */
    protected function setGeneric($FieldName, $Data, $Object = "Object")
    {
        //====================================================================//
        //  Compare Field Data
        $Current    =   $this->{$Object}->{ "get" . $FieldName}();
        if ($Current == $Data) {
            return $this;
        }
        //====================================================================//
        //  Update Field Data
        $this->{$Object}->{ "set" . $FieldName}($Data);
        $this->needUpdate();
        return $this;
    }
}
