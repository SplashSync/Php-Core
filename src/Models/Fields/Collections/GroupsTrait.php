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

namespace Splash\Core\Models\Fields\Collections;

use Splash\Core\Helpers\ListsHelper;
use Splash\Core\Models\Fields\AbstractField;

/**
 * A set of Fields Collection Groups Building Method
 */
trait GroupsTrait
{
    /**
     * Default Group Name for Fields without Group
     */
    private static string $defaultGroup = "- -";

    /**
     * Collection of Groups associated with the Fields
     */
    private array $groups;

    /**
     * Get Collection Fields Groups
     *
     * @return array<string, string[]>
     */
    public function getGroups(): array
    {
        //====================================================================//
        // Already Done
        if (isset($this->groups)) {
            return $this->groups;
        }
        $groups = array(
            self::$defaultGroup => array(),
        );
        //====================================================================//
        // Walk on Collection Fields
        foreach ($this as $field) {
            //====================================================================//
            // Populate Fields Groups
            $groupName = $this->getGroupName($field);
            $groups[$groupName] ??= array();
            $groups[$groupName][] = $field->getIdentifier();
        }

        //====================================================================//
        // Remove Empty Groups && Return
        return $this->groups = array_filter($groups);
    }

    /**
     * Get Collection Fields Groups Names
     *
     * @return string[]
     */
    public function getGroupNames(): array
    {
        return array_keys($this->getGroups());
    }

    /**
     * Get Group Name for Field
     */
    protected function getGroupName(AbstractField $field): string
    {
        //==============================================================================
        // Identify List Field Definition
        $listName = ListsHelper::listName($field->getIdentifier());
        if ($listName) {
            return "@".ucwords(html_entity_decode($listName));
        }

        //==============================================================================
        // Simple Field Definition
        return ucwords(html_entity_decode($field->getGroup() ?: self::$defaultGroup));
    }
}
