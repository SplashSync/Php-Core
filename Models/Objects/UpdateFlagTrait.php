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
 * @abstract    This class implements Objects Update Flag
 */
trait UpdateFlagTrait
{
    
    /**
     * Set Operations Updated Flag
     *
     * @abstract This flag is set when an update is done during Set Operation.
     *           Using this flag is useful to reduce exchanges with databases
     * @var bool
     */
    private $Update         = false;
    
    /**
     * Set Custom Updated Flag
     *
     * @abstract This flag is set when an update is done during Set Operation.
     *           Using this flag is useful to reduce exchanges with databases
     * @var bool
     */
    private $Custom         = array();
    
    //====================================================================//
    //  Update Flag Management
    //====================================================================//

    /**
     * @abstract    Flag Object For Database Update
     * @param       string  $Custom     Custom Falg Name
     * @return      self
     */
    protected function needUpdate($Custom = "Object")
    {
        if (self::isCustom($Custom)) {
            $this->Custom[$Custom]   =   true;
        } else {
            $this->Update   =   true;
        }
        return $this;
    }

    /**
     * @abstract    Clear Update Flag
     * @param       string  $Custom     Custom Falg Name
     * @return      self
     */
    protected function isUpdated($Custom = "Object")
    {
        if (self::isCustom($Custom)) {
            $this->Custom[$Custom]   =   false;
        } else {
            $this->Update   =   false;
        }
        return $this;
    }
    
    /**
     * @abstract    is Database Update Needed
     * @param       string  $Custom     Custom Falg Name
     * @return      bool
     */
    protected function isToUpdate($Custom = "Object")
    {
        if (self::isCustom($Custom)) {
            return isset($this->Custom[$Custom]) ? $this->Custom[$Custom] : false;
        } else {
            return $this->Update;
        }
    }
    
    /**
     * @abstract    is Custom Flag Request
     * @param       string  $Custom     Custom Falg Name
     * @return      bool
     */
    private function isCustom($Custom)
    {
        if ($Custom == "Object") {
            return false;
        }
        if (is_null($Custom) || !is_scalar($Custom) || empty($Custom)) {
            return false;
        }
        return true;
    }
}
