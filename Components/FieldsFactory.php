<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2019 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace   Splash\Components;

use ArrayObject;
use Splash\Core\SplashCore      as Splash;

/**
 * @abstract    This Class is a Generator for Objects Fields Definition
 *
 * @author      B. Paquier <contact@splashsync.com>
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class FieldsFactory
{
    //==============================================================================
    //      Favorites Sync Modes
    //==============================================================================

    const MODE_BOTH     =           "both";
    const MODE_READ     =           "read";
    const MODE_WRITE    =           "write";
    
    //==============================================================================
    //      Meta Data Access MicroDatas
    //==============================================================================
    
    const META_URL              = "http://splashync.com/schemas";       // Splash Specific Schemas Url.
    const META_OBJECTID         = "ObjectId";                           // Splash Object Id.
    const META_DATECREATED      = "DateCreated";                        // Splash Object Create Date.
    const META_ORIGIN_NODE_ID   = "SourceNodeId";                       // Object Source Server Identifier
    
    /**
     * Default Field Definition Resolver Array
     *
     * @var array
     */
    private static $defaultFields = array(
        //==============================================================================
        //      GENERAL FIELD PROPS
        "required"  =>  false,                  //  Field is Required to Create a New Object (Bool)
        "type"      =>  null,                   //  Field Fomat Type Name
        "id"        =>  null,                   //  Field Object Unique Identifier
        "name"      =>  null,                   //  Field Humanized Name (String)
        "desc"      =>  null,                   //  Field Description (String)
        "group"     =>  null,                   //  Field Section/Group (String)
        //==============================================================================
        //      ACCES PROPS
        "read"      =>  true,                   //  Field is Readable (Bool)
        "write"     =>  true,                   //  Field is Writable (Bool)
        "inlist"    =>  false,                  //  Field is Available in Object List Response (Bool)
        //==============================================================================
        //      SYNC MODE
        "syncmode"  =>  self::MODE_BOTH,        //  Field Favorite Sync Mode (read|write|both)
        //==============================================================================
        //      SCHEMA.ORG IDENTIFICATION
        "itemprop"  =>  null,                   //  Field Unique Schema.Org "Like" Property Name
        "itemtype"  =>  null,                   //  Field Unique Schema.Org Object Url
        "tag"       =>  null,                   //  Field Unique Linker Tags (Self-Generated)
        //==============================================================================
        //      DATA SPECIFIC FORMATS PROPS
        "choices"   =>  array(),                //  Possible Values used in Editor & Debugger Only  (Array)
        //==============================================================================
        //      DATA LOGGING PROPS
        "log"       =>  false,                  //  Field is To Log (Bool)
        //==============================================================================
        //      DEBUGGER PROPS
        "asso"      =>  array(),                //  Associated Fields. Fields to Generate with this field.
        "options"   =>  array(),                //  Fields Constraints to Generate Fake Data during Tests
        "notest"    =>  false,                  //  Do No Perform Tests for this Field
    );
    
    //====================================================================//
    // Data Storage

    /**
     * @abstract   Empty Template Object Field Storage
     *
     * @var Array
     */
    private $empty;

    /**
     * @abstract   New Object Field Storage
     *
     * @var null|ArrayObject
     */
    private $new;
    
    /**
     * @abstract   Object Fields List Storage
     *
     * @var Array
     */
    private $fields;
    
    /**
     * @abstract     Initialise Class
     */
    public function __construct()
    {
        //====================================================================//
        // Initialize Data Storage
        $this->new            = null;
        $this->fields         = array();
        //====================================================================//
        // Initialize Template Field
        $this->empty          = self::$defaultFields;
    }

    //====================================================================//
    //  FIELDS :: DATA TYPES DEFINITION
    //====================================================================//

    /**
     * @abstract   Create a new Field Definition with default parameters
     *
     * @param string $fieldType Standard Data Type (Refer Splash.Inc.php)
     * @param string $fieldId   Local Data Identifier (Shall be unik on local machine)
     * @param string $fieldName Data Name (Will Be Translated by Splash if Possible)
     *
     * @return $this
     */
    public function create($fieldType, $fieldId = null, $fieldName = null)
    {
        //====================================================================//
        // Commit Last Created if not already done
        if (!empty($this->new)) {
            $this->commit();
        }
        //====================================================================//
        // Unset Current
        $this->new = null;
        //====================================================================//
        // Create new empty field
        $this->new          =   new ArrayObject($this->empty, ArrayObject::ARRAY_AS_PROPS);
        //====================================================================//
        // Set Field Type
        $this->new->type    =   $fieldType;
        //====================================================================//
        // Set Field Identifier
        if (!is_null($fieldId)) {
            $this->identifier($fieldId);
        }
        //====================================================================//
        // Set Field Name
        if (!is_null($fieldName)) {
            $this->name($fieldName);
        }

        return $this;
    }
    
    /**
     * @abstract   Set Current New Field Identifier
     *
     * @param string $fieldId Local Data Identifier (Shall be unik on local machine)
     *
     * @return $this
     */
    public function identifier($fieldId)
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {
            Splash::log()->err("ErrFieldsNoNew");
        } else {
            //====================================================================//
            // Update New Field structure
            $this->new->id    = $fieldId;
        }
        
        return $this;
    }
    
    /**
     * @abstract   Update Current New Field set as it inside a list
     *
     * @param string $listName Name of List
     *
     * @return $this
     */
    public function inList($listName)
    {
        //====================================================================//
        // Safety Checks ==> Verify List Name Not Empty
        if (empty($listName)) {
            return $this;
        }
        
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {
            Splash::log()->err("ErrFieldsNoNew");
        } else {
            //====================================================================//
            // Update New Field Identifier
            $this->new->id      =   $this->new->id . LISTSPLIT . $listName;
            //====================================================================//
            // Update New Field Type
            $this->new->type    =   $this->new->type . LISTSPLIT . SPL_T_LIST;
        }
        
        return $this;
    }
    
    /**
     * @abstract   Set Current New Field Name (Translated)
     *
     * @param string $fieldName Data Name (Will Be Translated if Possible)
     *
     * @return $this
     */
    public function name($fieldName)
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {
            Splash::log()->err("ErrFieldsNoNew");
        } else {
            //====================================================================//
            // Update New Field structure
            $this->new->name    = $fieldName;
            if (empty($this->new->desc)) {
                $this->description($fieldName);
            }
        }
        
        return $this;
    }
    
    /**
     * @abstract   Update Current New Field with descriptions (Translated)
     *
     * @param string $fieldDesc Data Description (Will Be Translated if Possible)
     *
     * @return $this
     */
    public function description($fieldDesc)
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {
            Splash::log()->err("ErrFieldsNoNew");
        } else {
            //====================================================================//
            // Update New Field structure
            $this->new->desc    = Splash::trans(trim($fieldDesc));
        }
        
        return $this;
    }
    
    /**
     * @abstract   Update Current New Field with Field Group Name (Translated)
     *
     * @param string $fieldGroup Data Group (Will Be Translated if Possible)
     *
     * @return $this
     */
    public function group($fieldGroup)
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {
            Splash::log()->err("ErrFieldsNoNew");
        } else {
            //====================================================================//
            // Update New Field structure
            $this->new->group    = Splash::trans(trim($fieldGroup));
        }
        
        return $this;
    }
    
    /**
     * @bstract   Update Current New Field set as Read Only Field
     *
     * @return $this
     */
    public function isReadOnly()
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {
            Splash::log()->err("ErrFieldsNoNew");
        } else {
            //====================================================================//
            // Update New Field structure
            $this->new->read    = true;
            $this->new->write   = false;
        }
        
        return $this;
    }
    
    /**
     * @abstract   Update Current New Field set as Write Only Field
     *
     * @return $this
     */
    public function isWriteOnly()
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {
            Splash::log()->err("ErrFieldsNoNew");
        } else {
            //====================================================================//
            // Update New Field structure
            $this->new->read    = false;
            $this->new->write   = true;
        }
        
        return $this;
    }
    
    /**
     * @abstract   Update Current New Field set as required for creation
     *
     * @return $this
     */
    public function isRequired()
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {
            Splash::log()->err("ErrFieldsNoNew");
        } else {
            //====================================================================//
            // Update New Field structure
            $this->new->required  = true;
        }
        
        return $this;
    }
    
    /**
     * @bstract   Signify Server Current New Field Prefer ReadOnly Mode
     *
     * @return $this
     */
    public function setPreferRead()
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {
            Splash::log()->err("ErrFieldsNoNew");
        } else {
            //====================================================================//
            // Update New Field structure
            $this->new->mode    = self::MODE_READ;
        }
        
        return $this;
    }

    /**
     * @bstract   Signify Server Current New Field Prefer WriteOnly Mode
     *
     * @return $this
     */
    public function setPreferWrite()
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {
            Splash::log()->err("ErrFieldsNoNew");
        } else {
            //====================================================================//
            // Update New Field structure
            $this->new->mode    = self::MODE_WRITE;
        }
        
        return $this;
    }
    
    /**
     * @abstract   Update Current New Field set list of associated fields
     *
     * @return $this
     */
    public function association()
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {
            Splash::log()->err("ErrFieldsNoNew");
        } else {
            //====================================================================//
            // Field Clear Fields Associations
            if (!empty($this->new->asso)) {
                $this->new->asso = null;
            }
            
            //====================================================================//
            // Set New Field Associations
            if (!empty(func_get_args())) {
                $this->new->asso  = func_get_args();
            }
        }
        
        return $this;
    }
    
    /**
     * @abstract   Update Current New Field set as available in objects list
     *
     * @return $this
     */
    public function isListed()
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {
            Splash::log()->err("ErrFieldsNoNew");
        } else {
            //====================================================================//
            // Update New Field structure
            $this->new->inlist  = true;
        }
        
        return $this;
    }
    
    /**
     * @abstract   Update Current New Field set as recommended for logging
     *
     * @return $this
     */
    public function isLogged()
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {
            Splash::log()->err("ErrFieldsNoNew");
        } else {
            //====================================================================//
            // Update New Field structure
            $this->new->log  = true;
        }
        
        return $this;
    }
    
    /**
     * @abstract   Update Current New Field set its meta informations for autolinking
     *
     * @param string $itemType Field Microdata Type Url
     * @param string $itemProp Field Microdata Property Name
     *
     * @return $this
     */
    public function microData($itemType, $itemProp)
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {
            Splash::log()->err("ErrFieldsNoNew");
        } else {
            //====================================================================//
            // Update New Field structure
            $this->new->itemtype            = $itemType;
            $this->new->itemprop            = $itemProp;
            $this->setTag($itemProp . IDSPLIT . $itemType);
        }
        
        return $this;
    }
        
    /**
     * @abstract   Update Current New Field set as not possible to test
     *
     * @return $this
     */
    public function isNotTested()
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {
            Splash::log()->err("ErrFieldsNoNew");
        } else {
            //====================================================================//
            // Update New Field structure
            $this->new->notest  = true;
        }
        
        return $this;
    }
    
    /**
     * @abstract   Add Possible Choice to Current New Field Name (Translated)
     *
     * @param array $fieldChoices Possible Choice Array (Value => Decsription)
     *
     * @return $this
     */
    public function addChoices($fieldChoices)
    {
        foreach ($fieldChoices as $value => $description) {
            $this->addChoice($value, $description);
        }

        return $this;
    }

    /**
     * @abstract   Add Possible Choice to Current New Field Name (Translated)
     *
     * @param string $value       Possible Choice Value
     * @param string $description Choice Description for Display (Will Be Translated if Possible)
     *
     * @return $this
     */
    public function addChoice($value, $description)
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {
            Splash::log()->err("ErrFieldsNoNew");
        } else {
            //====================================================================//
            // Update New Field structure
            $this->new->choices[]   = array(
                "key"   =>  $value,
                "value" =>  Splash::trans(trim($description))
            );
        }
        
        return $this;
    }
    
    /**
     * @abstract   Add New Options Array for Current Field
     *
     * @param array $fieldOptions Array of Options (Type => Value)
     *
     * @return $this
     */
    public function addOptions($fieldOptions)
    {
        foreach ($fieldOptions as $type => $value) {
            $this->addOption($type, $value);
        }

        return $this;
    }

    /**
     * @abstract   Add New Option for Current Field
     *
     * @param string      $type  Constrain Type
     * @param bool|string $value Constrain Value
     *
     * @return $this
     */
    public function addOption($type, $value = true)
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {
            Splash::log()->err("ErrFieldsNoNew");
        } elseif (empty($type)) {
            Splash::log()->err("Field Option Type Cannot be Empty");
        } else {
            //====================================================================//
            // Update New Field structure
            $this->new->options[$type]   = $value;
        }

        return $this;
    }
    
    /**
     * @abstract   Save Current New Field in list & Clean current new field
     *
     * @return false|array[ArrayObject]
     */
    public function publish()
    {
        //====================================================================//
        // Commit Last Created if not already done
        if (!empty($this->new)) {
            $this->commit();
        }
        //====================================================================//
        // Safety Checks
        if (empty($this->fields)) {
            return Splash::log()->err("ErrFieldsNoList");
            //====================================================================//
        // Return fields List
        }
        $buffer = $this->fields;
        $this->fields = array();

        return $buffer;
    }
    
    /**
     * @abstract   Seach for a Field by unik tag
     *
     * @param array  $fieldList Array Of Field definition
     * @param string $fieldTag  Field Unik Tag
     *
     * @return ArrayObject|false
     */
    public function seachtByTag($fieldList, $fieldTag)
    {
        //====================================================================//
        // Safety Checks
        if (!count($fieldList)) {
            return false;
        }
        if (empty($fieldTag)) {
            return false;
        }
        //====================================================================//
        // Walk Through List and select by Tag
        foreach ($fieldList as $field) {
            if ($field["tag"] == $fieldTag) {
                return $field;
            }
        }

        return false;
    }
    /**
     * @abstract   Seach for a Field by id
     *
     * @param array  $fieldList Array Of Field definition
     * @param string $fieldId   Field Identifier
     *
     * @return ArrayObject|false
     */
    public function seachtById($fieldList, $fieldId)
    {
        //====================================================================//
        // Safety Checks
        if (!count($fieldList)) {
            return false;
        }
        if (empty($fieldId)) {
            return false;
        }
        //====================================================================//
        // Walk Through List and select by Tag
        foreach ($fieldList as $field) {
            if ($field["id"] == $fieldId) {
                return $field;
            }
        }

        return false;
    }
    
    /**
     * @abstract   Update Current New Field set its unik tag for autolinking
     *
     * @param string $fieldTag Field Unik Tag
     *
     * @return $this
     */
    protected function setTag($fieldTag)
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {
            Splash::log()->err("ErrFieldsNoNew");
        } else {
            //====================================================================//
            // Update New Field structure
            $this->new->tag     = md5($fieldTag);
        }
        
        return $this;
    }
    
    /**
     * @abstract   Verify Current New Field data
     *
     * @return bool
     */
    private function verify()
    {
        //====================================================================//
        // If new Field is Empty
        if (!isset($this->new) || empty($this->new)) {
            return false;
        }

        return $this->validate($this->new);
    }
    
    /**
     * @abstract    Validate Field Definition
     *
     * @param ArrayObject $field
     *
     * @return boolean
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function validate($field)
    {
        //====================================================================//
        // Verify - Field Type is Not Empty
        if (empty($field->type) || !is_string($field->type)) {
            return Splash::log()->err("ErrFieldsNoType");
        }
        //====================================================================//
        // Verify - Field Id is Not Empty
        if (empty($field->id) || !is_string($field->id)) {
            return Splash::log()->err("ErrFieldsNoId");
        }
        //====================================================================//
        // Verify - Field Id No Spacial Chars
        if ($field->id !== preg_replace('/[^a-zA-Z0-9-_@]/u', '', $field->id)) {
            Splash::log()->war("ErrFieldsInvalidId", $field->id);

            return false;
        }
        //====================================================================//
        // Verify - Field Name is Not Empty
        if (empty($field->name) || !is_string($field->name)) {
            return Splash::log()->err("ErrFieldsNoName");
        }
        //====================================================================//
        // Verify - Field Desc is Not Empty
        if (empty($field->desc) || !is_string($field->desc)) {
            return Splash::log()->err("ErrFieldsNoDesc");
        }

        return true;
    }
    
    /**
     * @abstract   Save Current New Field in list & Clean current new field
     *
     * @return bool
     */
    private function commit()
    {
        //====================================================================//
        // Safety Checks
        if (empty($this->new)) {
            return true;
        }
        //====================================================================//
        // Create Field List
        if (empty($this->fields)) {
            $this->fields   = array();
        }
        //====================================================================//
        // Validate New Field
        if (!$this->verify()) {
            $this->new = null;

            return false;
        }
        //====================================================================//
        // Insert Field List
        $this->fields[] = $this->new;
        $this->new = null;
        
        return true;
    }
}
