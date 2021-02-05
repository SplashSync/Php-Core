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
     *
     * @var bool
     */
    private $update = false;

    /**
     * Set Custom Updated Flag
     *
     * @abstract This flag is set when an update is done during Set Operation.
     *           Using this flag is useful to reduce exchanges with databases
     *
     * @var bool
     */
    private $custom = array();

    //====================================================================//
    //  Update Flag Management
    //====================================================================//

    /**
     * @abstract    Flag Object For Database Update
     *
     * @param string $custom Custom Falg Name
     *
     * @return self
     */
    protected function needUpdate($custom = "object")
    {
        if (self::isCustom($custom)) {
            $this->custom[$custom] = true;
        } else {
            $this->update = true;
        }

        return $this;
    }

    /**
     * @abstract    Clear Update Flag
     *
     * @param string $custom Custom Falg Name
     *
     * @return self
     */
    protected function isUpdated($custom = "object")
    {
        if (self::isCustom($custom)) {
            $this->custom[$custom] = false;
        } else {
            $this->update = false;
        }

        return $this;
    }

    /**
     * @abstract    is Database Update Needed
     *
     * @param string $custom Custom Falg Name
     *
     * @return bool
     */
    protected function isToUpdate($custom = "object")
    {
        if (self::isCustom($custom)) {
            return isset($this->custom[$custom]) ? $this->custom[$custom] : false;
        }

        return $this->update;
    }

    /**
     * @abstract    is Custom Flag Request
     *
     * @param string $custom Custom Falg Name
     *
     * @return bool
     */
    private function isCustom($custom)
    {
        if ("object" == $custom) {
            return false;
        }
        if (is_null($custom) || !is_scalar($custom) || empty($custom)) {
            return false;
        }

        return true;
    }
}
