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

namespace Splash\Models;

use Splash\Components\FieldsFactory;

interface ObjectExtensionInterface
{
    /**
     * Get list of Splash Object Types Extended by this Class
     *
     * @return string[]
     */
    public function getExtendedTypes(): array;

    /**
     * Build list of Splash Object Fields
     *
     * @param string        $objectType Object Type Name
     * @param FieldsFactory $factory    Splash Fields Factory
     *
     * @return void
     */
    public function buildExtendedFields(string $objectType, FieldsFactory $factory): void;

    /**
     * Get Splash Object Field Data
     *
     * Possible returns:
     *  - null: This field is not managed by this Extension
     *  - false: This field is managed by this Extension, but reading fails
     *  - true: This field is managed by this Extension, value was reed
     *
     * @param object $object    Current Object
     * @param string $fieldId   ID of Field to Read
     * @param mixed  $fieldData Data of Field to Read
     *
     * @return null|bool
     */
    public function getExtendedFields(object $object, string $fieldId, &$fieldData): ?bool;

    /**
     * Set Splash Object Field Data
     *
     * Possible returns:
     *  - null: This field is not managed by this Extension
     *  - false: This field is managed by this Extension, but unchanged
     *  - true: This field is managed by this Extension, value was changed
     *
     * @param object $object    Current Object
     * @param string $fieldId   ID of Field to Write
     * @param mixed  $fieldData Data of Field to Write
     *
     * @return null|bool
     */
    public function setExtendedFields(object $object, string $fieldId, $fieldData): ?bool;
}
