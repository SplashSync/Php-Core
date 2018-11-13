<?php
/**
 * This file is part of SplashSync Project.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 *  @author    Splash Sync <www.splashsync.com>
 *  @copyright 2015-2017 Splash Sync
 *  @license   GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007
 *
 **/

namespace   Splash\Models\Helpers;

/**
 * @abstract    Helper for List Fields Management
 */
class ListsHelper
{
    //====================================================================//
    // FIELDS LIST IDENTIFIERS MANAGEMENT
    //====================================================================//
   
    /**
     *      @abstract   Create a List Field Identifier String
     *
     *      @param      string      $listName       Field List Name.
     *      @param      string      $fieldName     Field Identifier
     *
     *      @return     string
     */
    public function encode($listName, $fieldName)
    {
        //====================================================================//
        // Safety Checks
        if (empty($listName)) {
            return false;
        }
        if (empty($fieldName)) {
            return false;
        }
        //====================================================================//
        // Create & Return List Field Id Data String
        return   $fieldName . LISTSPLIT . $listName;
    }
    
    /**
     *      @abstract   decode List Field String
     *
     *      @param      string      $listFieldName      List Field Identifier String
     *
     *      @return     string
     */
    private function decode($listFieldName)
    {
        //====================================================================//
        // Safety Checks
        if (empty($listFieldName)) {
            return false;
        }
        //====================================================================//
        // Explode Object String
        $result = explode(LISTSPLIT, $listFieldName);
        //====================================================================//
        // Check result is Valid
        if (count($result) != 2) {
            return false;
        }
        //====================================================================//
        // Return Object Array
        return   $result;
    }
    
    /**
     *      @abstract   Retrieve Field Identifier from an List Field String
     *
     *      @param      string      $listFieldName      List Field Identifier String
     *
     *      @return     string
     */
    public function fieldName($listFieldName)
    {
        //====================================================================//
        // decode
        $result     = $this->decode($listFieldName);
        if (empty($result)) {
            return false;
        }
        //====================================================================//
        // Return Field Identifier
        return   $result[0];
    }

    /**
     *      @abstract   Retrieve List Name from an List Field String
     *
     *      @param      string      $listFieldName      List Field Identifier String
     *
     *      @return     string
     */
    public function listName($listFieldName)
    {
        //====================================================================//
        // decode
        $result     = $this->decode($listFieldName);
        if (empty($result)) {
            return false;
        }
        //====================================================================//
        // Return List Name
        return   $result[1];
    }
    
    //====================================================================//
    // FIELDS LIST DATA MANAGEMENT
    //====================================================================//
    
    /**
     *      @abstract   Validate & Init List before Adding Data
     *
     *      @param      array       $buffer             Object Data Buffer
     *      @param      string      $listName           List Identifier String
     *      @param      string      $fieldName          List Field Identifier String
     *
     *      @return     string
     */
    public function initOutput(&$buffer, $listName, $fieldName)
    {
        //====================================================================//
        // Check List Name
        if ($this->listName($fieldName) !== $listName) {
            return false;
        }
        //====================================================================//
        // Create List Array If Needed
        if (!array_key_exists($listName, $buffer)) {
            $buffer[$listName] = array();
        }
        //====================================================================//
        // decode Field Name
        return $this->fieldName($fieldName);
    }

    /**
     *      @abstract   Add Item Data in Given  Output List
     *
     *      @param      array       $buffer             Object Data Buffer
     *      @param      string      $listName           List Identifier String
     *      @param      string      $fieldName          List Field Identifier String
     *      @param      string|int  $key                List Item Index Key
     *      @param      mixed       $itemData           Item Data
     *
     *      @return     void
     */
    public function insert(&$buffer, $listName, $fieldName, $key, $itemData)
    {
        //====================================================================//
        // Create List Array If Needed
        if (!array_key_exists($listName, $buffer)) {
            $buffer[$listName] = array();
        }
        //====================================================================//
        // Create Line Array If Needed
        if (!array_key_exists($key, $buffer[$listName])) {
            $buffer[$listName][$key] = array();
        }
        //====================================================================//
        // Store Data in Array
        $fieldIndex = explode("@", $fieldName);
        $buffer[$listName][$key][$fieldIndex[0]] = $itemData;
    }
}
