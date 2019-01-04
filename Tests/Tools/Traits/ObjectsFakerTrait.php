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

namespace Splash\Tests\Tools\Traits;

use ArrayObject;
use Splash\Client\Splash;

/**
 * @abstract    Splash Test Tools - Objects Faker trait
 *
 * @author SplashSync <contact@splashsync.com>
 */
trait ObjectsFakerTrait
{
    //==============================================================================
    //      FAKE DATA GENERATORS
    //==============================================================================
    
    /**
     *   @abstract   Generate Fake Object Fields List
     *
     *   @param      string     $objectType     Object Type Name
     *   @param      array      $fieldsList     Object Field Ids List
     *   @param      bool       $associate      Include Associated Fields
     *
     *   @return     array      $Out            Array of Fields
     */
    public function fakeFieldsList($objectType, $fieldsList = false, $associate = false)
    {
        //====================================================================//
        // Safety Check => $ObjectType is a valid
        $this->assertTrue(
            in_array($objectType, Splash::objects(), true),
            "Invalid Object Type Name. (" . $objectType . ")"
        );

        //====================================================================//
        // Create Empty Object Data Array
        $outputs    = array();
        
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
            if (!$this->isFieldNeeded($field, $fieldsList)) {
                continue;
            }
            //====================================================================//
            // Add Fields to List
            $outputs[$field->id] = $field;
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
            if (empty($outField->asso)) {
                continue;
            }
            //====================================================================//
            // For Associated Fields
            foreach ($fields as $field) {
                if (in_array($field->id, $outField->asso, true)) {
                    $outputs[$field->id] = $field;
                }
            }
        }
        
        return $outputs;
    }
    
    /**
     * @abstract   Create Fake/Dummy Object Data
     *
     * @param      array   $fieldsList     Object Field List
     * @param      array   $originData     Original Object Data
     *
     * @return     array
     */
    public function fakeObjectData($fieldsList, $originData = null)
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
            if (!self::isListField($field->id)) {
                $outputs[$field->id] = (
                    self::isFieldToPreserve($field, $originData) ?
                        $originData[$field->id] :
                        self::fakeFieldData(
                            $field->type,
                            self::toArray($field->choices),
                            self::toArray($field->options)
                        )
                    );

                continue;
            }
            
            //====================================================================//
            // Generate Dummy List  Data
            $list       =   self::isListField($field->id);
            $listName   =   $list["listname"];
            $fieldName  =   $list["fieldname"];
            $listData   =   self::fakeListData($field);
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
     *   @abstract   Create Fake/Dummy Object List Data
     *
     *   @param      ArrayObject   $field          Object Field Definition
     *
     *   @return     array
     */
    public function fakeListData($field)
    {
        //====================================================================//
        // Read Number of Items to Put in Lists
        $nbItems =  $this->settings["ListItems"]?$this->settings["ListItems"]:2;
        //====================================================================//
        // Parse List Identifiers
        $list   =   self::isListField($field->id);
        $type   =   self::isListField($field->type);
        
        //====================================================================//
        // Generate Unik Dummy Fields Data
        $listData = array();
        while (count($listData) < $nbItems) {
            $data           =   self::fakeFieldData(
                $type["fieldname"],
                self::toArray($field->choices),
                self::toArray($field->options)
            );
            $md5            =   md5(serialize($data));
            $listData[$md5] =   $data;
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
     * @abstract   Create Fake Field data
     *
     * @param      string  $type       Object Field Type
     * @param      array   $choices    Object Field Possible Values
     * @param      array   $options     Object Field Values Options
     *
     * @return     array|bool|string
     */
    public function fakeFieldData($type, $choices = null, $options = array())
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
            return $className::fake(array_merge_recursive($this->settings, $options), $id["ObjectType"]);
        }
        
        //====================================================================//
        // Take Values From Given Choices
        if (!empty($choices)) {
            $index = mt_rand(0, count($choices) - 1);
            if (isset($choices[$index]["key"]) && (SPL_T_VARCHAR == $type)) {
                return (string) $choices[$index]["key"];
            }
            if (isset($choices[$index]["key"])) {
                return $choices[$index]["key"];
            }
        }

        //====================================================================//
        // Generate Single Field Data Type is Valid
        return $className::fake(array_merge_recursive($this->settings, $options));
    }
    
    /**
     *   @abstract   Check if Field Need to be in List
     *
     *   @param      ArrayObject    $field          Field Definition
     *   @param      array          $fieldsList     Object Field Ids List
     *
     *   @return     bool
     */
    private function isFieldNeeded($field, $fieldsList = false)
    {
        //====================================================================//
        // Check if Fields is Writable
        if (!$field->write) {
            return false;
        }
        //====================================================================//
        // Check if Fields is Needed
        //====================================================================//

        //====================================================================//
        // Required Field
        if ($field->required) {
            return true;
        }
        //====================================================================//
        // If NO Fields List is Given => Select All Write Fields
        if ((false == $fieldsList) || !is_array($fieldsList)) {
            return true;
        }
        //====================================================================//
        // Field is in Requested List
        if (!in_array($field->id, $fieldsList, true)) {
            return false;
        }

        return true;
    }

    /**
     *   @abstract   Check if Field Need to e in List
     *
     *   @param      ArrayObject    $field          Field Definition
     *   @param      null|array     $originData     Original Object Data
     *
     *   @return     bool
     */
    private static function isFieldToPreserve($field, $originData)
    {
        //====================================================================//
        // Check if Origin Data Exists
        if (empty($originData) || !isset($originData[$field->id]) || empty($originData[$field->id])) {
            return false;
        }
        //====================================================================//
        // Check if Fields Should be Tested or Not
        if (!$field->notest) {
            return false;
        }

        return true;
    }
}
