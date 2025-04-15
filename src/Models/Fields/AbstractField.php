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

namespace Splash\Core\Models\Fields;

use Exception;
use Splash\Core\Dictionary\Fields\SplFieldProps;
use Splash\Core\Dictionary\Fields\SplFieldProps as Props;
use Splash\Core\Interfaces\Fields\FieldInterface;
use Splash\Core\Models\Fields\Field as Traits;

/**
 * Base Class for Splash Object Field Definition
 *
 * @phpstan-import-type FIELD from FieldInterface
 */
abstract class AbstractField implements FieldInterface
{
    use Traits\FieldCoreTrait;
    use Traits\FieldSynchronizationTrait;
    use Traits\FieldSyncModeTrait;
    use Traits\FieldMetadataTrait;
    use Traits\FieldListingTrait;
    use Traits\FieldOptionsTrait;
    use Traits\FieldTestTrait;
    use Traits\FieldValidationTrait;

    /**
     * @inheritdoc
     */
    public function __construct(string $type, ?string $identifier = null)
    {
        $this->setType($type);
        if (!empty($identifier)) {
            $this->setIdentifier($identifier);
        }
    }

    //==============================================================================
    // Data Export Methods
    //==============================================================================

    /**
     * (NATIVE) Convert Field to String
     *  => Return Field ID
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * Convert Field to String
     */
    public function toString(): string
    {
        return $this->getIdentifier();
    }

    /**
     * Convert Field Definition to Array
     *  => Export of Field Definition
     *
     * @return FIELD
     */
    public function toArray(): array
    {
        return array(
            //==============================================================================
            //      GENERAL FIELD PROPS
            //==============================================================================
            //  Field Format Type Name
            SplFieldProps::TYPE => $this->getType(),
            //  Field Object Unique Identifier
            "id" => $this->getIdentifier(),
            //  Field Humanized Name (String)
            "name" => $this->getName(),
            //  Field Description (String)
            "desc" => $this->getDesc(),
            //  Field Section/Group (String)
            "group" => $this->getGroup(),
            //==============================================================================
            //      ACCESS PROPS
            //==============================================================================
            //  Field is Required to Create a New Object (Bool)
            "required" => $this->isRequired(),
            //  Field is Readable (Bool)
            "read" => $this->isRead(),
            //  Field is Writable (Bool)
            "write" => $this->isWrite(),
            //  Field Should be Indexed for Text Search (Bool)
            "index" => $this->isIndex(),
            //  Field is Available in Object List Response (Bool)
            "inlist" => $this->isListed(),
            //  Field is Available in Object List but Hidden (Bool)
            "hlist" => $this->isListHidden(),
            //  Field is To Log (Bool)
            "log" => $this->isLogged(),
            //==============================================================================
            // SYNC MODE
            //==============================================================================
            //  Field is a Primary Key (Bool)
            "primary" => $this->isPrimary(),
            //  Field Favorite Sync Mode (read|write|both)
            "syncmode" => $this->getSyncMode(),
            //==============================================================================
            // METADATA
            //==============================================================================
            //  Field Unique Schema.Org Object Url
            "itemtype" => $this->getItemtype(),
            //  Field Unique Schema.Org "Like" Property Name
            "itemprop" => $this->getItemprop(),
            //  Field Unique Linker Tags (Self-Generated)
            "tag" => $this->getTag(),
            //==============================================================================
            // TESTING PROPS
            //==============================================================================
            //  Possible Values used in Editor & Debugger Only  (Array)
            "choices" => $this->getRawChoices(),
            //  Associated Fields. Fields to Generate with this field.
            "asso" => $this->getAssociations(),
            //  Fields Constraints to Generate Fake Data during Tests
            "options" => $this->getOptions(),
            //  Do No Perform Tests for this Field
            "notest" => $this->isNotTested(),
        );
    }

    //==============================================================================
    //  Data Import Methods
    //==============================================================================

    /**
     * Create a new Field from an Array of Values
     *
     * @throws Exception
     */
    public static function fromArray(array $values): static
    {
        //==============================================================================
        // Safety Check
        $identifier = $values[Props::ID] ?? null;
        $type = $values[Props::TYPE] ?? null;
        if (!$identifier || !$type || !is_string($type) || !is_string($identifier)) {
            throw new Exception("Invalid Field Definition, Missing Identifier or Type");
        }
        //==============================================================================
        // Create a new Field
        $field = new static($type, $identifier);
        //==============================================================================
        // Import Field Definition
        $field->update($values);

        return $field;
    }

    /**
     * Update of Field Definition from an Array of Values
     */
    public function update(array $values): static
    {
        $this
            ->updateCoreValues($values)
            ->updateSynchronizationValues($values)
            ->updateSyncModeValues($values)
            ->updateListingValues($values)
            ->updateMetadataValues($values)
            ->updateOptionsValues($values)
            ->updateChoicesValues($values)
            ->updateTestValues($values)
        ;

        return $this;
    }

    /**
     * Update a string value in the provided array using a callable setter.
     *
     * The method checks if the provided key exists in the array and contains a non-empty string value.
     * If so, it invokes the callable setter with the value.
     *
     * @param array    $values An array containing the data to be processed.
     * @param string   $key    The key in the array whose value needs to be updated.
     * @param callable $setter The setter function to apply the value if conditions are met.
     */
    protected function updateStringValue(array $values, string $key, callable $setter): void
    {
        $fieldValue = $values[$key] ?? null;
        if (!empty($fieldValue) && is_string($fieldValue)) {
            $setter($fieldValue);
        }
    }

    /**
     * Updates a boolean value in the provided data array.
     *
     * This method checks if the specified key exists within the array and if the
     * corresponding value is scalar. If both conditions are met, it casts the value
     * to a boolean and applies the provided setter callable to update the value.
     *
     * @param array    $values The data array containing the key-value pairs.
     * @param string   $key    The key to look for in the data array.
     * @param callable $setter The callable to apply the boolean value update.
     */
    protected function updateBooleanValue(array $values, string $key, callable $setter): void
    {
        if (isset($values[$key]) && is_scalar($values[$key])) {
            $setter((bool) $values[$key]);
        }
    }
}
