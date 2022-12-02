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
 * This class implements Objects Update Flag
 */
trait UpdateFlagTrait
{
    /**
     * Set Operations Updated Flag
     *
     * @note    This flag is set when an update is done during Set Operation.
     *          Using this flag is useful to reduce exchanges with databases
     *
     * @var bool
     */
    private bool $update = false;

    /**
     * Set Custom Updated Flag
     *
     * @note    This flag is set when an update is done during Set Operation.
     *          Using this flag is useful to reduce exchanges with databases
     *
     * @var bool
     */
    private $custom = array();

    //====================================================================//
    //  Update Flag Management
    //====================================================================//

    /**
     * Flag Object For Database Update
     *
     * @param string $custom Custom Flag Name
     *
     * @return void
     */
    protected function needUpdate(string $custom = "object"): void
    {
        if (self::isCustom($custom)) {
            $this->custom[$custom] = true;
        } else {
            $this->update = true;
        }
    }

    /**
     * Clear Update Flag
     *
     * @param string $custom Custom Flag Name
     *
     * @return void
     */
    protected function isUpdated(string $custom = "object"): void
    {
        if (self::isCustom($custom)) {
            $this->custom[$custom] = false;
        } else {
            $this->update = false;
        }
    }

    /**
     * Is Database Update Needed
     *
     * @param string $custom Custom Flag Name
     *
     * @return bool
     */
    protected function isToUpdate(string $custom = "object"): bool
    {
        if (self::isCustom($custom)) {
            return $this->custom[$custom] ?? false;
        }

        return $this->update;
    }

    /**
     * Is Custom Flag Request
     *
     * @param string $custom Custom Flag Name
     *
     * @return bool
     */
    private function isCustom(string $custom): bool
    {
        if ("object" == $custom) {
            return false;
        }
        if (empty($custom)) {
            return false;
        }

        return true;
    }
}
