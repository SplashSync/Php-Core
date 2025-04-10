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
use Splash\Core\Interfaces\Fields\FieldInterface;
use Splash\Core\Models\Fields\FieldCoreTrait;

/**
 * Splash Object Field Definition
 */
class ObjectField extends ArrayObject implements FieldInterface
{
    use FieldCoreTrait;

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
     * Push Field Inside a List
     *
     * @param string $listName
     *
     * @return self
     */
    public function setInlist(string $listName): self
    {
        //====================================================================//
        // Safety Checks ==> Verify List Name Not Empty
        if (empty($listName)) {
            return $this;
        }
        //====================================================================//
        // Update New Field Identifier
        $this->setIdentifier((string) ListsHelper::encode($listName, $this->id));
        //====================================================================//
        // Update New Field Type
        $this->setType((string) ListsHelper::encode(SplFields::LIST, $this->type));

        return $this;
    }

    /**
     * Set Metadata for Auto-Mapping
     *
     * @param string $itemType
     * @param string $itemProp
     *
     * @return self
     */
    public function setMicroData(string $itemType, string $itemProp): self
    {
        $this
            ->setItemType($itemType)
            ->setItemProp($itemProp)
            ->setTag(self::toTag($itemType, $itemProp))
        ;

        return $this;
    }

    /**
     * Build Field Tag from Metadata
     *
     * @param string $itemType
     * @param string $itemProp
     *
     * @return string
     */
    public static function toTag(string $itemType, string $itemProp): string
    {
        return md5((string) ObjectsHelper::encode($itemType, $itemProp));
    }

    /**
     * Configure for Multi-Lang
     *
     * @param null|string $isoCode Language ISO Code (i.e en_US | fr_FR)
     *
     * @return $this
     */
    public function setMultiLang(?string $isoCode, bool $isDefault): self
    {
        //====================================================================//
        // Safety Checks ==> Verify Language ISO Code
        if (!ObjectField::isValidIsoCode((string) $isoCode)) {
            return $this;
        }
        //====================================================================//
        // Safety Checks ==> Verify Field Type is Allowed
        if (!in_array($this->type, self::MULTILANG_TYPES, true)) {
            Splash::log()->err("ErrFieldsWrongLang");
            Splash::log()->err("Received: ".$this->type);

            return $this;
        }
        //====================================================================//
        // Default Language ==> Only Setup Language Option
        $this->addOption("language", (string) $isoCode);
        //====================================================================//
        // Other Language ==> Complete Field Setup
        if (!$isDefault) {
            $this->setIdentifier($this->id."_".$isoCode);
            if (!empty($this->itemtype)) {
                $this->setMicroData($this->itemtype."/".$isoCode, $this->itemprop);
            }
        }

        return $this;
    }

    /**
     * Verify an Iso Language Code
     *
     * @param string $isoCode Language ISO Code (i.e en_US | fr_FR)
     *
     * @return bool
     */
    public static function isValidIsoCode(string $isoCode): bool
    {
        if ((strlen($isoCode) < 2) || (strlen($isoCode) > 5)) {
            return Splash::log()->err("Language ISO Code is Invalid: ".$isoCode);
        }

        return true;
    }

    /**
     * Verify a Field Id
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
     * @return  array{
     *         type: string,
     *         id: string,
     *         name: string,
     *         desc: string,
     *         group: string,
     *         required: null|bool|string,
     *         read: null|bool|string,
     *         write: null|bool|string,
     *         index: null|bool|string,
     *         inlist: null|bool|string,
     *         hlist: null|bool|string,
     *         log: null|bool|string,
     *         notest: null|bool|string,
     *         primary: null|bool|string,
     *         syncmode: string,
     *         itemprop: null|string,
     *         itemtype: null|string,
     *         tag: null|string,
     *         choices: null|array{ key: string, value: scalar},
     *         asso: null|string[],
     *         options: array<string, scalar>
     *         }
     */
    public function toArray(): array
    {
        /** @phpstan-ignore-next-line */
        return $this->getArrayCopy();
    }

    //==============================================================================
    //  Generic Setters
    //==============================================================================




    /**
     * Set Field Required Flag
     *
     * @param bool $required
     *
     * @return self
     */
    public function setRequired(bool $required): self
    {
        $this->required = $required;

        return $this;
    }

    /**
     * Set Field Readable Flag
     *
     * @param bool $read
     *
     * @return self
     */
    public function setRead(bool $read): self
    {
        $this->read = $read;

        return $this;
    }

    /**
     * Set Field Writable Flag
     *
     * @param bool $write
     *
     * @return self
     */
    public function setWrite(bool $write): self
    {
        $this->write = $write;

        return $this;
    }

    /**
     * Set Field Should be Indexed Flag
     *
     * @param bool $index
     *
     * @return self
     */
    public function setIndex(bool $index): self
    {
        $this->index = $index;

        return $this;
    }

    /**
     * Set Field Primary Flag
     *
     * @param bool $primary
     *
     * @return self
     */
    public function setPrimary(bool $primary): self
    {
        $this->primary = $primary;

        return $this;
    }

    /**
     * Set Field In Object List Flag
     *
     * @param bool $inlist
     *
     * @return self
     */
    public function setIsListed(bool $inlist): self
    {
        $this->inlist = $inlist;

        return $this;
    }

    /**
     * Set Field In Hidden Object List Flag
     *
     * This field is in Objects List but Hidden.
     * This improves reading of lists, but makes field usable for analyzes.
     *
     * @param bool $hlist
     *
     * @return self
     */
    public function setHiddenList(bool $hlist): self
    {
        $this->hlist = $hlist;

        return $this;
    }

    /**
     * Set Field Recommended for Logging Flag
     *
     * @param bool $log
     *
     * @return self
     */
    public function setIsLogged(bool $log): self
    {
        $this->log = $log;

        return $this;
    }

    /**
     * Set Field Excluded from General Unit Tests
     * May be tested by Custom Tests Suites
     *
     * @param bool $noTest
     *
     * @return self
     */
    public function setIsNotTested(bool $noTest): self
    {
        $this->notest = $noTest;

        return $this;
    }

    /**
     * Set Field Preferred Sync Mode
     *
     * @param string $syncMode
     *
     * @return self
     */
    public function setSyncMode(string $syncMode): self
    {
        $this->syncmode = $syncMode;

        return $this;
    }

    /**
     * Add Field Possible Key/Value Choice
     *
     * @param string $value
     * @param string $description
     *
     * @return self
     */
    public function addChoice(string $value, string $description): self
    {
        $this->choices[] = array(
            "key" => $value,
            "value" => StringConverter::toUtf8($description)
        );

        return $this;
    }

    /**
     * Set list of associated fields
     *
     * @param array $association
     *
     * @return self
     */
    public function setAssociation(array $association): self
    {
        $this->asso = $association;

        return $this;
    }

    /**
     * Add an Option for Units Tests
     *
     * @param string                $key
     * @param bool|float|int|string $value
     *
     * @return self
     */
    public function addOption(string $key, $value = true): self
    {
        //====================================================================//
        // Safety Checks ==> Verify Key
        if (empty($key)) {
            Splash::log()->err("Field Option Type Cannot be Empty");
        } else {
            //====================================================================//
            // Update New Field structure
            $this->options[$key] = $value;
        }

        return $this;
    }

    //==============================================================================
    //  Private Setters
    //==============================================================================

    /**
     * Set Field Type
     *
     * @param string $type
     */
    private function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * Set Metadata Type for Auto-Mapping
     *
     * @param string $itemType
     *
     * @return self
     */
    private function setItemType(string $itemType): self
    {
        $this->itemtype = $itemType;

        return $this;
    }

    /**
     * Set Metadata Property for Auto-Mapping
     *
     * @param string $itemProp
     *
     * @return self
     */
    private function setItemProp(string $itemProp): self
    {
        $this->itemprop = $itemProp;

        return $this;
    }

    /**
     * Set Field Auto-mapping Tag
     *
     * @param string $tag
     */
    private function setTag(string $tag): void
    {
        $this->tag = $tag;
    }


}
