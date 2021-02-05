<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2021 Splash Sync  <www.splashsync.com>
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
 * This class implements Intelligent Parser to Access Objects Data
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
     * This variable is used to store Object Array during Set Operations
     * Each time a field is imported, unset it from this buffer
     * to control all fields were imported at the end of Set Operation
     *
     * @var ArrayObject
     */
    protected $in;

    /**
     * Get Operations Output Buffer
     *
     * This variable is used to store Object Array during Get Operations
     *
     * @var ArrayObject
     */
    protected $out;

    /**
     * Work Object Class
     *
     * This variable is used to store current working Object during Set & Get Operations
     *
     * @var mixed
     */
    protected $object;

    /**
     * Buffer for All Available Class Fields Building Methods
     *
     * @var array
     */
    protected static $classBuildMethods;

    /**
     * Buffer for All Available Class Getter Methods
     *
     * @var array
     */
    protected static $classGetMethods;

    /**
     * Buffer for All Available Class Setter Methods
     *
     * @var array
     */
    protected static $classSetMethods;

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
        Splash::log()->trace();

        foreach ($this->identifyBuildMethods() as $method) {
            $this->{$method}();
        }

        //====================================================================//
        // Publish Fields from Factory
        $fields = $this->fieldsFactory()->publish();

        //====================================================================//
        // Override Fields from Local Configurator
        return Splash::configurator()->overrideFields(self::getType(), $fields);
    }

    /**
     * {@inheritdoc}
     */
    public function get($objectId = null, $fieldsList = null)
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();
        //====================================================================//
        // Init Reading
        $this->in = $fieldsList;
        //====================================================================//
        // Load Object
        $this->object = $this->load($objectId);
        if (!is_object($this->object)) {
            return false;
        }
        //====================================================================//
        // Init Response Array
        $this->out = array( "id" => $objectId );
        //====================================================================//
        // Run Through All Requested Fields
        //====================================================================//
        $fields = is_a($this->in, "ArrayObject") ? $this->in->getArrayCopy() : $this->in;
        foreach ($fields as $key => $fieldName) {
            //====================================================================//
            // Read Requested Fields
            foreach ($this->identifyGetMethods() as $method) {
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
        Splash::log()->trace();
        //====================================================================//
        // Init Reading
        $newObjectId = null;         // If Object Created, we MUST Return Object Id
        $this->in = $list;        // Store List of Field to Write in Buffer
        $this->isUpdated();             // Clear Updated Flag before Writing

        //====================================================================//
        // Load or Create Requested Object
        //====================================================================//
        $this->object = $objectId ? $this->load($objectId) : $this->create();
        //====================================================================//
        // Safety Check => Object Now Loaded
        if (!is_object($this->object)) {
            return false;
        }
        //====================================================================//
        // New Object Created => Store new Object Identifier
        if (!$objectId) {
            $newObjectId = $this->getObjectIdentifier();
        }

        //====================================================================//
        // Execute Write Operations on Object
        //====================================================================//
        if (false == $this->setObjectData()) {
            return $newObjectId ? $newObjectId : false;
        }

        //====================================================================//
        // Update Requested Object
        //====================================================================//
        $update = $this->update($this->isToUpdate());
        //====================================================================//
        // Update Fail ?
        if (empty($update)) {
            //====================================================================//
            // If New Object => Return Object Identifier
            // Existing Object => Return False
            return $newObjectId ? $newObjectId : false;
        }

        return $update;
    }

    //====================================================================//
    //  VERIFY FUNCTIONS
    //====================================================================//

    /**
     * Check Required Fields are Available
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
                    $field["name"]."(".$field["id"].")"
                );
            }
        }

        return true;
    }

    //====================================================================//
    //  TOOLING FUNCTIONS
    //====================================================================//

    /**
     * Execute Write Operations on Object
     *
     * @return bool
     */
    private function setObjectData()
    {
        //====================================================================//
        // Walk on All Requested Fields
        //====================================================================//
        $fields = is_a($this->in, "ArrayObject") ? $this->in->getArrayCopy() : $this->in;
        foreach ($fields as $fieldName => $fieldData) {
            //====================================================================//
            // Write Requested Fields
            foreach ($this->identifySetMethods() as $method) {
                $this->{$method}($fieldName, $fieldData);
            }
        }

        //====================================================================//
        // Verify Requested Fields List is now Empty => All Fields Writen Successfully
        //====================================================================//
        if (count($this->in)) {
            foreach ($this->in as $fieldName => $fieldData) {
                Splash::log()->err("ErrLocalWrongField", __CLASS__, __FUNCTION__, $fieldName);
            }

            return false;
        }

        return true;
    }

    /**
     * Identify Generic Fields Building Functions
     *
     * @return array
     */
    private function identifyBuildMethods()
    {
        //====================================================================//
        // Load Methods From Cache
        if (!isset(static::$classBuildMethods)) {
            static::$classBuildMethods = self::identifyMethods("build");
        }

        return static::$classBuildMethods;
    }

    /**
     * Identify Generic Fields Getter Functions
     *
     * @return array
     */
    private function identifyGetMethods()
    {
        //====================================================================//
        // Load Methods From Cache
        if (!isset(static::$classGetMethods)) {
            static::$classGetMethods = self::identifyMethods("get");
        }

        return static::$classGetMethods;
    }

    /**
     * Identify Generic Fields Getter Functions
     *
     * @return array
     */
    private function identifySetMethods()
    {
        //====================================================================//
        // Load Methods From Cache
        if (!isset(static::$classSetMethods)) {
            static::$classSetMethods = self::identifyMethods("set");
        }

        return static::$classSetMethods;
    }

    /**
     * Identify Generic Functions
     *
     * @param mixed $prefix
     *
     * @return array
     */
    private static function identifyMethods($prefix)
    {
        //====================================================================//
        // Prepare List of Available Methods
        $result = array();
        foreach (get_class_methods(__CLASS__) as $method) {
            if (0 !== strpos($method, $prefix)) {
                continue;
            }
            if (false === strpos($method, "Fields")) {
                continue;
            }
            $result[] = $method;
        }

        return $result;
    }

    /**
     * Check Required Fields
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
        $listName = self::lists()->ListName($fieldId);
        $fieldName = self::lists()->FieldName($fieldId);
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
