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

namespace Splash\Tests\Tools\Traits;

use Exception;
use Splash\Client\Splash;
use Splash\Models\Helpers\InlineHelper;

/**
 * Splash Test Tools - Objects Faker trait
 */
trait ObjectsFakerTrait
{
    /**
     * Object original Data
     * Used to Protect Required & NotTested fields from Update
     *
     * @var null|array
     */
    protected ?array $originData = null;

    //==============================================================================
    //      FAKE DATA GENERATORS
    //==============================================================================

    /**
     * Generate Fake Object Fields List
     *
     * @param string     $objectType Object Type Name
     * @param null|array $fieldsList Object Field Ids List
     * @param bool       $associate  Include Associated Fields
     * @param bool       $nonTested  Include Non Tested Fields
     *
     * @throws Exception
     *
     * @return array[] Array of Fields
     */
    public function fakeFieldsList(
        string $objectType,
        array $fieldsList = null,
        bool $associate = false,
        bool $nonTested = true
    ): array {
        //====================================================================//
        // Safety Check => $ObjectType is a valid
        $this->assertTrue(
            in_array($objectType, Splash::objects(), true),
            "Invalid Object Type Name. (".$objectType.")"
        );

        //====================================================================//
        // Create Empty Object Data Array
        $outputs = array();

        //====================================================================//
        // Load Object Fields Definition
        $fields = Splash::object($objectType)->fields();
        if (empty($fields)) {
            return $outputs;
        }

        //====================================================================//
        // Generate Fields Data
        foreach ($fields as $field) {
            //====================================================================//
            // Check if Fields is Needed
            if (!$this->isFieldNeeded($field, $fieldsList, $nonTested)) {
                continue;
            }
            //====================================================================//
            // Add Fields to List
            $outputs[$field['id']] = $field;
        }

        //====================================================================//
        // No Associated Fields
        if (!$associate) {
            return $outputs;
        }

        //====================================================================//
        // Add Associated Fields to List
        foreach ($outputs as $outField) {
            //====================================================================//
            // No Associated Field
            if (empty($outField['asso'])) {
                continue;
            }
            //====================================================================//
            // For Associated Fields
            foreach ($fields as $field) {
                if (in_array($field['id'], self::toArray($outField['asso']), true)) {
                    $outputs[$field['id']] = $field;
                }
            }
        }

        return $outputs;
    }

    /**
     * Create Fake/Dummy Object Data
     *
     * @param array[] $fieldsList Object Field List
     *
     * @throws Exception
     *
     * @return array[]
     */
    public function fakeObjectData(array $fieldsList): array
    {
        //====================================================================//
        // Create Dummy Data Array
        $outputs = array();
        if (empty($fieldsList)) {
            return $outputs;
        }

        //====================================================================//
        // Create Dummy Fields Data
        foreach ($fieldsList as $field) {
            //====================================================================//
            // Generate Single Fields Dummy Data (is Not a List Field)
            if (!self::isListField($field['id'])) {
                $choices = self::toArray($field['choices']);
                $options = self::toArray($field['options']);
                $outputs[$field['id']] = $this->isFieldToPreserve($field)
                    ? (isset($this->originData) ? $this->originData[$field['id']] : null)
                    : self::fakeFieldData($field['type'], $choices, $options);

                continue;
            }

            //====================================================================//
            // Generate Dummy List  Data
            $list = self::isListField($field['id']);
            $this->assertIsArray($list);
            $listName = $list["listname"];
            $fieldName = $list["fieldname"];
            $listData = self::fakeListData($field);
            //====================================================================//
            // Create List
            if (!array_key_exists($listName, $outputs)) {
                $outputs[$listName] = array();
            }
            //====================================================================//
            // Parse Data in List
            foreach ($listData as $key => $data) {
                if (!array_key_exists($key, $outputs[$listName])) {
                    $outputs[$listName][$key] = array();
                }
                $outputs[$listName][$key][$fieldName] = $data[$fieldName];
            }
        }

        return $outputs;
    }

    /**
     * Create Fake/Dummy Object List Data
     *
     * @param array $field Object Field Definition
     *
     * @throws Exception
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.CountInLoopExpression)
     */
    public function fakeListData(array $field): array
    {
        //====================================================================//
        // Read Number of Items to Put in Lists
        $nbItems = $this->settings["ListItems"]?:2;
        //====================================================================//
        // Parse List Identifiers
        $list = self::isListField($field['id']);
        $type = self::isListField($field['type']);
        $this->assertIsArray($list);
        $this->assertIsArray($type);

        //====================================================================//
        // Generate Unique Dummy Fields Data
        $listData = array();
        $nbTry = 0;
        while (count($listData) < $nbItems) {
            //====================================================================//
            // Generate Dummy Fields Data
            $data = self::fakeFieldData(
                $type["fieldname"],
                self::toArray($field['choices']),
                self::toArray($field['options'])
            );
            //====================================================================//
            // Compute Md5
            // or Use Try Count In Case we were not able to generate Unique Data
            $md5 = ($nbTry < 10) ? md5(serialize($data)) : $nbTry;
            $listData[$md5] = $data;
            $nbTry++;
        }
        //====================================================================//
        // Data Set is Not Unique => Add A Warning
        if ($nbTry >= 10) {
            print_r("Unable to Generate Unique List Dataset from Field: ".$field['id'].PHP_EOL);
            print_r("Generated List may Contain Duplicated Values".PHP_EOL);
            print_r("Possible Fix: Ensure Pointed Object List is NOT Empty".PHP_EOL);
        }

        //====================================================================//
        // Create Dummy List Data
        $outputs = array();

        //====================================================================//
        // Create Dummy Fields Data
        for ($i = 0; $i < $nbItems; $i++) {
            $outputs[][$list["fieldname"]] = array_shift($listData);
        }

        return $outputs;
    }

    /**
     * Create Fake Field data
     *
     * @param string     $type    Object Field Type
     * @param null|array $choices Object Field Possible Values
     * @param array      $options Object Field Values Options
     *
     * @return array|bool|string
     */
    public function fakeFieldData(string $type, array $choices = null, array $options = array())
    {
        //====================================================================//
        // Safety Check
        if (empty($type)) {
            return false;
        }
        //====================================================================//
        // Verify Field Type is Valid
        $className = self::isValidType($type);
        if (false == $className) {
            return false;
        }
        //====================================================================//
        // Detects Id Fields    => Cannot Generate Fake for Id Fields Here...
        if (($id = self::isIdField($type))) {
            return $className::fake(array_replace_recursive($this->settings, $options), $id["ObjectType"]);
        }
        //====================================================================//
        // Take Random Values From Given Choices
        if (!empty($choices)) {
            $choiceValue = $this->fakeFieldDataFromChoices($type, $choices);
            if (!empty($choiceValue)) {
                return $choiceValue;
            }
        }
        //====================================================================//
        // Generate Single Field Data Type is Valid
        return $className::fake(array_replace_recursive($this->settings, $options));
    }

    /**
     * Create Fake Field data
     *
     * @param string $type    Object Field Type
     * @param array  $choices Object Field Possible Values
     *
     * @return null|array|bool|string
     */
    public function fakeFieldDataFromChoices(string $type, array $choices)
    {
        // Ensure Choices have numeric Index
        $choices = array_values($choices);
        // Select a Random Index
        $index = mt_rand(0, count($choices) - 1);
        if (isset($choices[$index]["key"]) && (SPL_T_VARCHAR == $type)) {
            return (string) $choices[$index]["key"];
        }
        if (isset($choices[$index]["key"]) && (SPL_T_INLINE == $type)) {
            return InlineHelper::fromArray(array($choices[$index]["key"]));
        }
        if (isset($choices[$index]["key"])) {
            return $choices[$index]["key"];
        }

        return null;
    }

    /**
     * Check if Field Need to be in List
     *
     * @param array      $field      Field Definition
     * @param null|array $fieldsList Object Field Ids List
     * @param bool       $nonTested  Include Non Tested Fields
     *
     * @return bool
     */
    private function isFieldNeeded(array $field, array $fieldsList = null, bool $nonTested = true): bool
    {
        //====================================================================//
        // Check if Fields is Writable
        if (!$field['write']) {
            return false;
        }
        //====================================================================//
        // Check if Fields is Needed
        //====================================================================//

        //====================================================================//
        // Required Field
        if ($field['required']) {
            return true;
        }
        //====================================================================//
        // Non Tested Field
        if ((true == $field['notest']) && (false == $nonTested)) {
            return false;
        }
        //====================================================================//
        // If NO Fields List is Given => Select All Write Fields
        if ((false == $fieldsList) || !is_array($fieldsList)) {
            return true;
        }
        //====================================================================//
        // Field is in Requested List
        if (!in_array($field['id'], $fieldsList, true)) {
            return false;
        }

        return true;
    }

    /**
     * Check if Field Need to e in List
     *
     * @param array $field Field Definition
     *
     * @return bool
     */
    private function isFieldToPreserve(array $field): bool
    {
        //====================================================================//
        // Check if Origin Data Exists
        if (!is_array($this->originData)) {
            return false;
        }
        //====================================================================//
        // Check if Origin Data Exists
        if (!isset($this->originData[$field['id']]) || empty($this->originData[$field['id']])) {
            return false;
        }
        //====================================================================//
        // Check if Fields Should be Tested or Not
        if (!$field['notest']) {
            return false;
        }

        return true;
    }
}
