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

namespace Splash\Core\Fields;

use Splash\Core\Helpers\ListsHelper;
use Splash\Core\Helpers\ObjectsHelper;
use Splash\Core\Models\Fields\AbstractField;

/**
 * Splash Object Field Definition
 */
class ObjectField extends AbstractField
{
    /**
     * Retrieve Base Field Type from Field Type|ID String
     *
     * @param null|string $fieldId List Field Identifier String
     *
     * @return null|string
     */
    public static function baseType(?string $fieldId): ?string
    {
        //====================================================================//
        // Detect List Id Fields
        $fieldId = ListsHelper::fieldName($fieldId) ?? $fieldId;
        //====================================================================//
        // Detect Objects Id Fields
        $fieldId = ObjectsHelper::id((string) $fieldId) ?? $fieldId;

        return $fieldId ?: null;
    }
}
