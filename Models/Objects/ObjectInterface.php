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
     * @return string[]
     */
    public function description(): array;

    /**
     * Return List Of Available Fields for Splash Object
     *
     * All data must match with Splash Data Types
     * Use $this->fieldsFactory()->Create() to create all fields instances
     * Use $this->fieldsFactory()->Publish() to generate resulting array
     *
     * @remark  If you use the IntelliParser, this Function not Required
     *
     * @since 1.0.0
     *
     * @return22 array<string, array<string, scalar|array<string, scalar>>> List of all available fields
     *
     * @return  array<array{
     *          type: string,
     *          id: string,
     *          name: string,
     *          desc: string,
     *          group: string,
     *          required: null|bool|string,
     *          read: null|bool|string,
     *          write: null|bool|string,
     *          inlist: null|bool|string,
     *          log: null|bool|string,
     *          notest: null|bool|string,
     *          syncmode: string,
     *          itemprop: null|string,
     *          itemtype: null|string,
     *          tag: null|string,
     *          choices: null|array{ key: string, value: scalar},
     *          asso: null|string[],
     *          options: array<string, scalar>
     *          }>
     */
    public function fields(): array;

    /**
     * Return List Of Objects with required filters
     *
     * Data That May be sent on Parameters Array
     *  =>  $params["max"]              Maximum Number of results
     *  =>  $params["offset"]           List Start Offset
     *  =>  $params["sortfield"]        Field name for sort list (Available fields listed below)
     *  =>  $params["sortorder"]        List Order Constrain (Default = ASC)
     *
     * Metadata That Must be Included On Result Array
     *  =>  $response["meta"]["total"]     Total Number of results
     *  =>  $response["meta"]["current"]   Total Number of results
     *
     * @param null|string $filter Filters for Object List.
     * @param array       $params Search parameters for result List.
     *
     * @return array List of all Object main data
     *
     * @since 1.0.0
     */
    public function objectsList(string $filter = null, array $params = array()): array;

    /**
     * Read Requested Object Data
     *
     * Splash will send a list of Fields Ids to Read.
     * Objects Class will Return Data Array Indexed with those Fields Ids
     *
     * @param string   $objectId Object ID.
     * @param string[] $fields   List of requested fields
     *
     * @return null|array<string, null|array<string, null|array|scalar>|scalar>
     *
     * @remark  If you use the IntelliParser, this Function not Required
     *
     * @since 1.0.0
     */
    public function get(string $objectId, array $fields): ?array;

    /**
     * Update or Create requested Object Data
     *
     * Splash Sends an Array of Fields Data to Create or Update
     * Data are indexed by Fields Ids
     *
     * If Given ObjectId is null, Object is to Be Created
     *
     * @param null|string                                                 $objectId
     * @param array<string, null|array<string, null|array|scalar>|scalar> $objectData
     *
     * @return null|string Object ID or Null if Object wasn't created.
     *
     * @remark  If you use the IntelliParser, this Function not Required
     *
     * @since 1.0.0
     */
    public function set(?string $objectId, array $objectData): ?string;

    /**
     * Delete requested Object
     *
     * @param string $objectId Object ID
     *
     * @since 1.0.0
     *
     * @return bool
     */
    public function delete(string $objectId): bool;

    /**
     * Return the Identifier of Currently Written Object
     *
     * This function must return the String Identifier of Currently written
     * Object. It may be called aty any time by Splash Module as soon as
     * Load or Create Operation was done.
     *
     * @return null|string
     *
     * @remark  If you use the IntelliParser, This behavior will prevent creation
     *          of Duplicate Objects whenever Update fail.
     *
     * @since 1.6.0
     */
    public function getObjectIdentifier(): ?string;

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
     * @param null|string $objectId Local Object Identifier or NULL if New Object
     *
     * @return bool
     *
     * @remark Use LockTrait to simply Implement this Feature
     *
     * @since 1.0.0
     */
    public function lock(?string $objectId = null): bool;

    /**
     * Get Lock Status for a specific object
     *
     * @param null|string $objectId Local Object Identifier or Empty if New Object
     *
     * @since 1.0.0
     *
     * @return bool
     */
    public function isLocked(?string $objectId = null): bool;

    /**
     * Delete Current active Lock
     *
     * @param null|string $objectId Local Object Identifier or NULL if New Object
     *
     * @since 1.0.0
     *
     * @return bool
     */
    public function unLock(?string $objectId = null): bool;

    //====================================================================//
    // Object Metadata Management
    //====================================================================//

    /**
     * Return Object Status
     *
     * This function may be Override by Objects to Enable/Disable
     * access to an Object from Application
     *
     * Default behavior is Reading static::$DISABLED Flag (Default = false)
     *
     * @example If an Object require a Specific Extension
     *
     * @since 1.0.0
     *
     * @return bool
     */
    public static function isDisabled(): bool;
}
