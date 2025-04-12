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

use ArrayObject;
use Splash\Core\Client\Splash;
use Splash\Core\Dictionary\Fields\SplSyncMode;
use Splash\Core\Dictionary\SplFields;
use Splash\Core\Helpers\ListsHelper;
use Splash\Core\Helpers\ObjectsHelper;
use Splash\Core\Helpers\StringConverter;
use Splash\Core\Dictionary\Fields\SplFieldProps;
use Splash\Core\Interfaces\Fields\FieldInterface;
use Splash\Core\Models\Fields\FieldCoreTrait;
use Splash\Core\Models\Fields\FieldMetadataTrait;
use Splash\Core\Models\Fields\FieldOptionsTrait;
use Splash\Core\Models\Fields\FieldSynchronizationTrait;
use Splash\Core\Models\Fields\FieldListingTrait;
use Splash\Core\Models\Fields\FieldSyncModeTrait;
use Splash\Core\Models\Fields\FieldTestTrait;
use Splash\Core\Models\Fields\FieldValidationTrait;

/**
 * Splash Object Field Definition
 *
 * @phpstan-import-type FIELD from FieldInterface
 */
class ObjectField extends ArrayObject implements FieldInterface
{
    use FieldCoreTrait;
    use FieldSynchronizationTrait;
    use FieldSyncModeTrait;
    use FieldMetadataTrait;
    use FieldListingTrait;
    use FieldOptionsTrait;
    use FieldTestTrait;
    use FieldValidationTrait;

    const PRIMARY_TYPES = array(
        SplFields::VARCHAR, SplFields::TEXT,
        SplFields::EMAIL, SplFields::PHONE, SplFields::URL,
        SplFields::COUNTRY,
    );

    //==============================================================================
    //  Allowed Multi-lang Fields Types
    //==============================================================================

    const MULTILANG_TYPES = array(
        SplFields::VARCHAR, SplFields::TEXT, SplFields::INLINE,
        SplFields::BOOL, SplFields::INT, SplFields::DOUBLE,
        SplFields::URL
    );

    //==============================================================================
    //  Field Definition
    //==============================================================================

    /**
     * Default Field Definition Resolver Array
     *
     * @var array
     */
    private static $default = array(
        //==============================================================================
        //      GENERAL FIELD PROPS
        "type" => null,                     //  Field Format Type Name
        "id" => null,                       //  Field Object Unique Identifier
        "name" => null,                     //  Field Humanized Name (String)
        "desc" => null,                     //  Field Description (String)
        "group" => null,                    //  Field Section/Group (String)
        //==============================================================================
        //      ACCESS PROPS
        "required" => false,                //  Field is Required to Create a New Object (Bool)
        "read" => true,                     //  Field is Readable (Bool)
        "write" => true,                    //  Field is Writable (Bool)
        "index" => false,                   //  Field Should be Indexed for Text Search (Bool)
        "inlist" => false,                  //  Field is Available in Object List Response (Bool)
        "hlist" => false,                   //  Field is Available in Object List but Hidden (Bool)
        //==============================================================================
        //      SYNC MODE
        "primary" => false,                 //  Field is a Primary Key (Bool)
        "syncmode" => SplSyncMode::BOTH,      //  Field Favorite Sync Mode (read|write|both)
        //==============================================================================
        //      SCHEMA.ORG IDENTIFICATION
        "itemprop" => null,                 //  Field Unique Schema.Org "Like" Property Name
        "itemtype" => null,                 //  Field Unique Schema.Org Object Url
        "tag" => null,                      //  Field Unique Linker Tags (Self-Generated)
        //==============================================================================
        //      DATA SPECIFIC FORMATS PROPS
        "choices" => array(),               //  Possible Values used in Editor & Debugger Only  (Array)
        //==============================================================================
        //      DATA LOGGING PROPS
        "log" => false,                     //  Field is To Log (Bool)
        //==============================================================================
        //      DEBUGGER PROPS
        "asso" => array(),                  //  Associated Fields. Fields to Generate with this field.
        "options" => array(),               //  Fields Constraints to Generate Fake Data during Tests
        "notest" => false,                  //  Do No Perform Tests for this Field
    );

    //==============================================================================
    //  Main Methods
    //==============================================================================

    /**
     * @inheritdoc
     */
    public function __construct(string $type, ?string $identifier = null)
    {
        parent::__construct(self::$default, ArrayObject::ARRAY_AS_PROPS);
        $this->setType($type);
        if (!empty($identifier)) {
            $this->setIdentifier($identifier);
        }
    }









    /**
     * Validate Field Definition
     *
     * @return bool
     */
    public function validate(): bool
    {
        //====================================================================//
        // Verify - Field Type is Not Empty
        if (empty($this->type)) {
            return Splash::log()->err("ErrFieldsNoType");
        }
        //====================================================================//
        // Verify - Field Id
        if (!self::isValidIdentifier($this->id)) {
            return false;
        }
        //====================================================================//
        // Verify - Field Name is Not Empty
        if (empty($this->name)) {
            return Splash::log()->err("ErrFieldsNoName", $this->id);
        }
        //====================================================================//
        // Verify - Field Desc is Not Empty
        if (empty($this->desc)) {
            return Splash::log()->err("ErrFieldsNoDesc", $this->id);
        }

        return true;
    }

    /**
     * Convert Field Definition to Array
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
            "choices" => $this->getChoices(),
            //  Associated Fields. Fields to Generate with this field.
            "asso" => $this->getAssociations(),
            //  Fields Constraints to Generate Fake Data during Tests
            "options" => $this->getOptions(),
            //  Do No Perform Tests for this Field
            "notest" => $this->isNotTested(),
        );
    }

    //==============================================================================
    //  Generic Setters
    //==============================================================================













}
