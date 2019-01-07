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

namespace   Splash\Models\Objects;

use ArrayObject;
use Splash\Core\SplashCore      as Splash;

/**
 * @abstract    This class implements Intelligent Parser to Access Objects Data
 */
trait IntelParserTrait
{
    use FieldsFactoryTrait;
    use UpdateFlagTrait;
    
    //====================================================================//
    // General Class Variables
    //====================================================================//
    
    /**
     * Set Operations Input Buffer
     *
     * @abstract This variable is used to store Object Array during Set Operations
     *              Each time a field is imported, unset it from this buffer
     *              to control all fields were imported at the end of Set Operation
     *
     * @var ArrayObject
     */
    protected $in;
    
    /**
     * Get Operations Output Buffer
     *
     * @abstract This variable is used to store Object Array during Get Operations
     *
     * @var ArrayObject
     */
    protected $out;
    
    /**
     * Work Object Class
     *
     * @abstract This variable is used to store current working Object during Set & Get Operations
     *
     * @var mixed
     */
    protected $object;
    
    //====================================================================//
    // Class Main Functions
    //====================================================================//
    
    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace(__CLASS__, __FUNCTION__);
        
        foreach ($this->identifyFunctions("build") as $method) {
            $this->{$method}();
        }
        
        //====================================================================//
        // Publish Fields
        return $this->fieldsFactory()->publish();
    }
    
    /**
     * {@inheritdoc}
     */
    public function get($objectId = null, $fieldsList = null)
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace(__CLASS__, __FUNCTION__);
        //====================================================================//
        // Init Reading
        $this->in = $fieldsList;
        //====================================================================//
        // Load Object
        $this->object   =   $this->load($objectId);
        if (!is_object($this->object)) {
            return false;
        }
        //====================================================================//
        // Init Response Array
        $this->out  =   array( "id" => $objectId );
        //====================================================================//
        // Run Through All Requested Fields
        //====================================================================//
        $fields = is_a($this->in, "ArrayObject") ? $this->in->getArrayCopy() : $this->in;
        foreach ($fields as $key => $fieldName) {
            //====================================================================//
            // Read Requested Fields
            foreach ($this->identifyFunctions("get") as $method) {
                $this->{$method}($key, $fieldName);
            }
        }
        //====================================================================//
        // Verify Requested Fields List is now Empty => All Fields Read Successfully
        if (count($this->in)) {
            foreach ($this->in as $fieldName) {
                Splash::log()->err("ErrLocalWrongField", __CLASS__, __FUNCTION__, $fieldName);
            }

            return false;
        }
        //====================================================================//
        // Return Data
        //====================================================================//
        return $this->out;
    }
    
    /**
     * {@inheritdoc}
     */
    public function set($objectId = null, $list = null)
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace(__CLASS__, __FUNCTION__);
        //====================================================================//
        // Init Reading
        $this->in           =   $list;
        $this->isUpdated();
        //====================================================================//
        // Init Object
        if ($objectId) {
            $this->object   =   $this->load($objectId);
        } else {
            $this->object   =   $this->create();
        }
        if (!is_object($this->object)) {
            return false;
        }
        //====================================================================//
        // Run Throw All Requested Fields
        //====================================================================//
        $fields = is_a($this->in, "ArrayObject") ? $this->in->getArrayCopy() : $this->in;
        foreach ($fields as $fieldName => $fieldData) {
            //====================================================================//
            // Write Requested Fields
            foreach ($this->identifyFunctions("set") as $method) {
                $this->{$method}($fieldName, $fieldData);
            }
        }
        //====================================================================//
        // Verify Requested Fields List is now Empty => All Fields Writen Successfully
        if (count($this->in)) {
            foreach ($this->in as $fieldName => $fieldData) {
                Splash::log()->err("ErrLocalWrongField", __CLASS__, __FUNCTION__, $fieldName);
            }

            return false;
        }
        
        return $this->update($this->isToUpdate());
    }
    
    //====================================================================//
    //  TOOLING FUNCTION
    //====================================================================//

    /**
     * @abstract    Identify Generic Functions
     *
     * @param mixed $prefix
     *
     * @return self
     */
    public function identifyFunctions($prefix)
    {
        $result = array();
        foreach (get_class_methods(__CLASS__) as $method) {
            if (0 !== strpos($method, $prefix)) {
                continue;
            }
            if (false === strpos($method, "Fields")) {
                continue;
            }
            $result[]   =   $method;
        }

        return $result;
    }
    
    /**
     * @abstract    Check Required Fields
     *
     * @return bool
     */
    public function verifyRequiredFields()
    {
        foreach ($this->Fields() as $field) {
            //====================================================================//
            // Field is NOT required
            if (!$field["required"]) {
                continue;
            }
            //====================================================================//
            // Field is Required but not available
            if (!$this->verifyRequiredFieldIsAvailable($field["id"])) {
                return Splash::log()->err(
                    "ErrLocalFieldMissing",
                    __CLASS__,
                    __FUNCTION__,
                    $field["name"] . "(" . $field["id"] . ")"
                );
            }
        }

        return true;
    }

    /**
     * @abstract    Check Required Fields
     *
     * @param string $fieldId Object Field Identifier
     *
     * @return bool
     */
    private function verifyRequiredFieldIsAvailable($fieldId)
    {
        //====================================================================//
        // Detect List Field Names
        if (!method_exists($this, "Lists") || !self::lists()->listName($fieldId)) {
            //====================================================================//
            // Simple Field is Required but not available
            if (empty($this->in[$fieldId])) {
                return false;
            }

            return true;
        }
        //====================================================================//
        // List Field is required
        $listName   = self::lists()->ListName($fieldId);
        $fieldName  = self::lists()->FieldName($fieldId);
        //====================================================================//
        // Check List is available
        if (empty($this->in[$listName])) {
            return false;
        }
        //====================================================================//
        // list is a List...
        if (!is_array($this->in[$listName]) && !is_a($this->in[$listName], "ArrayObject")) {
            return false;
        }
        //====================================================================//
        // Check Field is Available
        foreach ($this->in[$listName] as $item) {
            if (empty($item[$fieldName])) {
                return false;
            }
        }

        return true;
    }
}
