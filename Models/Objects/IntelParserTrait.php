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

namespace   Splash\Models\Objects;

use Splash\Components\ExtensionsManager;
use Splash\Core\SplashCore      as Splash;
use TypeError;

/**
 * This class implements Intelligent Parser to Access Objects Data
 */
trait IntelParserTrait
{
    use FieldsFactoryTrait;
    use UpdateFlagTrait;
    use ExtensionFieldsTrait;

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
     * @var array<string, null|array<string, null|array|scalar>|scalar>
     */
    protected array $in;

    /**
     * Get Operations Output Buffer
     *
     * This variable is used to store Object Array during Get Operations
     *
     * @var array<string, null|array<string, null|array|scalar>|scalar>
     */
    protected array $out;

    /**
     * Work Object Class
     *
     * This variable is used to store current working Object during Set & Get Operations
     *
     * @var object
     */
    protected object $object;

    /**
     * Buffer for All Available Class Fields Building Methods
     *
     * @var null|string[]
     */
    protected static ?array $classBuildMethods;

    /**
     * Buffer for All Available Class Getter Methods
     *
     * @var null|string[]
     */
    protected static ?array $classGetMethods;

    /**
     * Buffer for All Available Class Setter Methods
     *
     * @var null|string[]
     */
    protected static ?array $classSetMethods;

    //====================================================================//
    // Class Main Functions
    //====================================================================//

    /**
     * {@inheritdoc}
     */
    public function fields(): array
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();
        //====================================================================//
        // Walk on Fields Building Methods
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
    public function get(string $objectId, array $fieldsList): ?array
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();
        //====================================================================//
        // Init Reading
        $this->in = $fieldsList;
        //====================================================================//
        // Load Object
        $object = $this->load($objectId);
        if (!is_object($object)) {
            return null;
        }
        $this->object = $object;
        //====================================================================//
        // Check if Object is Filtered
        if (ExtensionsManager::isFiltered(self::getType(), $objectId, $this->object)) {
            return Splash::log()->errNull("IsFilteredByExt");
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

            return null;
        }
        //====================================================================//
        // Return Data
        //====================================================================//
        return $this->out;
    }

    /**
     * {@inheritdoc}
     */
    public function set(?string $objectId, array $list): ?string
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();
        //====================================================================//
        // Init Reading
        $newObjectId = null;        // If Object Created, we MUST Return Object ID
        $this->in = $list;          // Store List of Field to Write in Buffer
        $this->isUpdated();         // Clear Updated Flag before Writing

        //====================================================================//
        // Load or Create Requested Object
        //====================================================================//
        $object = $objectId ? $this->load($objectId) : $this->create();
        //====================================================================//
        // Safety Check => Object Now Loaded
        if (!is_object($object)) {
            return null;
        }
        $this->object = $object;
        //====================================================================//
        // New Object Created => Store new Object Identifier
        if (!$objectId) {
            $newObjectId = $this->getObjectIdentifier();
        }
        //====================================================================//
        // Execute Write Operations on Object
        //====================================================================//
        if (false == $this->setObjectData()) {
            return $newObjectId ?: null;
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
            return $newObjectId ?: null;
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
    public function verifyRequiredFields(): bool
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
    // Mandatory Interfaces Functions
    //====================================================================//

    /**
     * Load Request Object
     *
     * @param string $objectId Object id
     *
     * @return null|object
     */
    abstract protected function load(string $objectId);

    /**
     * Create Request Object
     *
     * @return null|object
     */
    abstract protected function create();

    /**
     * Update Request Object
     *
     * @param bool $needed Is This Update Needed
     *
     * @return false|string Object ID
     */
    abstract protected function update(bool $needed): ?string;

    //====================================================================//
    //  TOOLING FUNCTIONS
    //====================================================================//

    /**
     * Execute Write Operations on Object
     *
     * @return bool
     */
    private function setObjectData(): bool
    {
        //====================================================================//
        // Walk on All Requested Fields
        //====================================================================//
        $fields = $this->in;
        foreach ($fields as $fieldName => $fieldData) {
            //====================================================================//
            // Replace Empty Stings by Null
            $fieldData = ("" === $fieldData) ? null : $fieldData;
            //====================================================================//
            // Write Requested Fields
            foreach ($this->identifySetMethods() as $method) {
                try {
                    $this->{$method}($fieldName, $fieldData);
                } catch (TypeError $ex) {
                    //====================================================================//
                    // Detect Typed Fields Set Type Error
                    // Setter Functions may for $fieldData types
                    $exFile = $ex->getTrace()[0]['file'] ?? "";
                    $exFunction = $ex->getTrace()[0]['function'] ?? "";
                    if ((__FILE__ == $exFile) && ($exFunction == $method)) {
                        continue;
                    }

                    throw $ex;
                }
            }
        }

        //====================================================================//
        // Verify Requested Fields List is now Empty => All Fields Written Successfully
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
    private function identifyBuildMethods(): array
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
    private function identifyGetMethods(): array
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
    private function identifySetMethods(): array
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
    private static function identifyMethods($prefix): array
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
    private function verifyRequiredFieldIsAvailable(string $fieldId): bool
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
        $listName = self::lists()->listName($fieldId);
        $fieldName = self::lists()->fieldName($fieldId);
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
