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

namespace Splash\Core\Models\Fields\Field;

use Splash\Core\Client\Splash;
use Splash\Core\Dictionary\SplFields;

/**
 * General Object Field Validations
 */
trait FieldValidationTrait
{
    /**
     * Validate Field Definition
     */
    public function isValid(): bool
    {
        //====================================================================//
        // Verify - Field Type is Not Empty
        if (empty($this->getType())) {
            return Splash::log()->err("ErrFieldsNoType");
        }
        //====================================================================//
        // Verify - Field Id
        if (!self::isValidIdentifier($this->getIdentifier())) {
            return false;
        }
        //====================================================================//
        // Verify - Field Name is Not Empty
        if (empty($this->getName())) {
            return Splash::log()->err("ErrFieldsNoName", $this->id);
        }
        //====================================================================//
        // Verify - Field Desc is Not Empty
        if (empty($this->getDesc())) {
            return Splash::log()->err("ErrFieldsNoDesc", $this->id);
        }

        return true;
    }

    /**
     * Verify a Field ID
     *
     * @param string $fieldId Field Identifier
     *
     * @return bool
     */
    public static function isValidIdentifier(string $fieldId): bool
    {
        //====================================================================//
        // Field Id is Not Empty
        if (empty($fieldId)) {
            return Splash::log()->err("ErrFieldsNoId");
        }
        //====================================================================//
        // Verify - Field Id includes No Spécial Chars
        if ($fieldId !== preg_replace('/[^a-zA-Z0-9-_@]/u', '', $fieldId)) {
            Splash::log()->war("ErrFieldsInvalidId", $fieldId);

            return false;
        }

        return true;
    }

    /**
     * Verify an Iso Language Code
     *
     * @param string $isoCode Language ISO Code (i.e en_US | fr_FR)
     */
    public static function isValidIsoCode(string $isoCode): bool
    {
        if ((strlen($isoCode) < 2) || (strlen($isoCode) > 5)) {
            return Splash::log()->err("Language ISO Code is Invalid: ".$isoCode);
        }

        return true;
    }

    /**
     * Verify Field Type Allow Primary Flag
     */
    public static function isValidPrimaryType(string $type): bool
    {
        static $types = array(
            SplFields::VARCHAR, SplFields::TEXT,
            SplFields::EMAIL, SplFields::PHONE, SplFields::URL,
            SplFields::COUNTRY,
        );

        return in_array($type, $types, true);
    }

    /**
     * Verify Field Type Allow Multi-Lang Flag
     */
    protected static function isValidMultiLangType(string $type): bool
    {
        static $types = array(
            SplFields::VARCHAR, SplFields::TEXT, SplFields::INLINE,
            SplFields::BOOL, SplFields::INT, SplFields::DOUBLE,
            SplFields::URL
        );

        return in_array($type, $types, true);
    }
}
