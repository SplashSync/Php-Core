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
     *      @param      string      $ListName       Field List Name.
     *      @param      string      $Identifier     Field Identifier
     *
     *      @return     string
     */
    public function encode($ListName, $Identifier)
    {
        //====================================================================//
        // Safety Checks
        if (empty($ListName)) {
            return false;
        }
        if (empty($Identifier)) {
            return false;
        }
        //====================================================================//
        // Create & Return List Field Id Data String
        return   $Identifier . LISTSPLIT . $ListName;
    }
    
    /**
     *      @abstract   decode List Field String
     *
     *      @param      string      $ListFieldName      List Field Identifier String
     *
     *      @return     string
     */
    private function decode($ListFieldName)
    {
        //====================================================================//
        // Safety Checks
        if (empty($ListFieldName)) {
            return false;
        }
        //====================================================================//
        // Explode Object String
        $Tmp = explode(LISTSPLIT, $ListFieldName);
        //====================================================================//
        // Check result is Valid
        if (count($Tmp) != 2) {
            return false;
        }
        //====================================================================//
        // Return Object Array
        return   $Tmp;
    }
    
    /**
     *      @abstract   Retrieve Field Identifier from an List Field String
     *
     *      @param      string      $ListFieldName      List Field Identifier String
     *
     *      @return     string
     */
    public function fieldName($ListFieldName)
    {
        //====================================================================//
        // decode
        $Result     = $this->decode($ListFieldName);
        if (empty($Result)) {
            return false;
        }
        //====================================================================//
        // Return Field Identifier
        return   $Result[0];
    }

    /**
     *      @abstract   Retrieve List Name from an List Field String
     *
     *      @param      string      $ListFieldName      List Field Identifier String
     *
     *      @return     string
     */
    public function listName($ListFieldName)
    {
        //====================================================================//
        // decode
        $Result     = $this->decode($ListFieldName);
        if (empty($Result)) {
            return false;
        }
        //====================================================================//
        // Return List Name
        return   $Result[1];
    }
    
    //====================================================================//
    // FIELDS LIST DATA MANAGEMENT
    //====================================================================//
    
    /**
     *      @abstract   Validate & Init List before Adding Data
     *
     *      @param      array       $Buffer             Object Data Buffer
     *      @param      string      $ListName           List Identifier String
     *      @param      string      $FieldName          List Field Identifier String
     *
     *      @return     string
     */
    public function initOutput(&$Buffer, $ListName, $FieldName)
    {
        //====================================================================//
        // Check List Name
        if ($this->listName($FieldName) !== $ListName) {
            return false;
        }
        //====================================================================//
        // Create List Array If Needed
        if (!array_key_exists($ListName, $Buffer)) {
            $Buffer[$ListName] = array();
        }
        //====================================================================//
        // decode Field Name
        return $this->fieldName($FieldName);
    }

    /**
     *      @abstract   Add Item Data in Given  Output List
     *
     *      @param      array       $Buffer             Object Data Buffer
     *      @param      string      $ListName           List Identifier String
     *      @param      string      $FieldName          List Field Identifier String
     *      @param      string|int  $Key                List Item Index Key
     *      @param      mixed       $Data               Item Data
     *
     *      @return     string
     */
    public function insert(&$Buffer, $ListName, $FieldName, $Key, $Data)
    {
        //====================================================================//
        // Create List Array If Needed
        if (!array_key_exists($ListName, $Buffer)) {
            $Buffer[$ListName] = array();
        }
        //====================================================================//
        // Create Line Array If Needed
        if (!array_key_exists($Key, $Buffer[$ListName])) {
            $Buffer[$ListName][$Key] = array();
        }
        //====================================================================//
        // Store Data in Array
        $FieldIndex = explode("@", $FieldName);
        $Buffer[$ListName][$Key][$FieldIndex[0]] = $Data;
    }
}
