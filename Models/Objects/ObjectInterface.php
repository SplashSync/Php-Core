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

/**
 * Splash Objects Interface
 *
 * This is the Core Interface for Generic for All Splash Objects
 * It must be Implemented for ALL Objects Available on Splash
 */
interface ObjectInterface
{
    //====================================================================//
    // Object Definition & Data Access Management
    //====================================================================//

    /**
     * Get Description Array for requested Object Type
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function description();

    /**
     * Return List Of Available Fields for Splash Object
     *
     * All data must match with Splash Data Types
     * Use $this->fieldsFactory()->Create() to create all fields instances
     * Use $this->fieldsFactory()->Publish() to generate resulting array
     *
     * @see If you uses the InteliParser, this Function not Required
     * @since 1.0.0
     *
     * @return array $data       List of all available fields
     */
    public function fields();

    /**
     * Return List Of Objects with required filters
     *
     * Data That May be Send on Parameters Array
     *  =>  $params["max"]              Maximum Number of results
     *  =>  $params["offset"]           List Start Offset
     *  =>  $params["sortfield"]        Field name for sort list (Available fields listed below)
     *  =>  $params["sortorder"]        List Order Constrain (Default = ASC)
     *
     * Metra Data That Must be Included On Result Array
     *  =>  $response["meta"]["total"]     Total Number of results
     *  =>  $response["meta"]["current"]   Total Number of results
     *
     * @param string $filter Filters for Object List.
     * @param array  $params Search parameters for result List.
     *
     * @since 1.0.0
     *
     * @return array List of all Object main data
     */
    public function objectsList($filter = null, $params = null);

    /**
     * Read Requested Object Data
     *
     * Splash will send a list of Fields Ids to Read.
     * Objects Class will Retun Data Array Indexed with those Fields Ids
     *
     * @see If you uses the InteliParser, this Function not Required
     * @since 1.0.0
     *
     * @param string $objectId Object Id.
     * @param array  $fields   List of requested fields
     *
     * @return array|false
     */
    public function get($objectId = null, $fields = array());

    /**
     * Update or Create requested Object Data
     *
     * Splash Sends an Array of Fields Data to Create or Update
     * Data are indexed by Fields Ids
     *
     * If Given ObjectId is null, Object is to Be Created
     *
     * @see If you uses the InteliParser, this Function not Required
     * @since 1.0.0
     *
     * @param string $objectId   Object Id.  If NULL, Object needs to be created.
     * @param array  $objectData List of requested fields
     *
     * @return false|string Object Id.  If False, Object wasn't created.
     */
    public function set($objectId = null, $objectData = null);

    /**
     * Delete requested Object
     *
     * @param string $objectId Object Id
     *
     * @since 1.0.0
     *
     * @return bool
     */
    public function delete($objectId = null);

    /**
     * Return the Identifier of Currently Written Object
     *
     * This function must return the String Identifier of Currently written
     * Object. It may be called aty any time by Splash Module as soon as
     * Load or Create Operation was done.
     *
     * @see If you uses the InteliParser, This behavior will prevent creation
     *      of Duplicate Objects whenever Update fail.
     * @since 2.0.0
     *
     * @return false|string
     */
    public function getObjectIdentifier();

    //====================================================================//
    // Object LOCK Management
    //====================================================================//

    /**
     * Set Lock for a specific object
     *
     * This function is used to prevent further actions
     * on currently edited objects. Node name & Type are
     * single, but Ids have to be stored as list
     *
     * @see Use LockTrait to simply Implement this Feature
     * @since 1.0.0
     *
     * @param null|int|string $objectId Local Object Identifier or Empty if New Object
     *
     * @return bool
     */
    public function lock($objectId = "new");

    /**
     * Get Lock Status for a specific object
     *
     * @param null|int|string $objectId Local Object Identifier or Empty if New Object
     *
     * @since 1.0.0
     *
     * @return bool
     */
    public function isLocked($objectId = "new");

    /**
     * Delete Current active Lock
     *
     * @param null|int|string $objectId Local Object Identifier or Empty if New Object
     *
     * @since 1.0.0
     *
     * @return bool
     */
    public function unLock($objectId = "new");

    //====================================================================//
    // Object Metadata Management
    //====================================================================//

    /**
     * Return Object Status
     *
     * This function may be Overidden by Objects to Enable/Disbale
     * access to an Object from Application
     *
     * Default behavior is Reading static::$DISABLED Flag (Default = false)
     *
     * @example If an Object require a Specific Extension
     *
     * @since 1.0.0
     *
     * @return null|bool
     */
    public static function getIsDisabled();
}
