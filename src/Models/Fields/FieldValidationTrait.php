<?php

namespace Splash\Core\Models\Fields;

use Splash\Core\Client\Splash;
use Splash\Core\Dictionary\SplFields;

/**
 * General Object Field Validations
 */
trait FieldValidationTrait
{
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
    protected static function isValidIsoCode(string $isoCode): bool
    {
        if ((strlen($isoCode) < 2) || (strlen($isoCode) > 5)) {
            return Splash::log()->err("Language ISO Code is Invalid: ".$isoCode);
        }

        return true;
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