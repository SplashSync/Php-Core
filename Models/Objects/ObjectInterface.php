<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2018 Splash Sync  <www.splashsync.com>
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
 * @abstract    Splash Objects Interface
 */
interface ObjectInterface
{
    /**
     *  @abstract   Get Description Array for requested Object Type
     *
     *  @return     array
     */
    public function description();
            
    /**
     * @abstract    Return List Of Available Fields for Splash Object
     *
     * @return      array   $data       List of all available fields
     *                                  All data must match with Splash Data Types
     *                                  Use $this->fieldsFactory()->Create() to create all fields instances
     *                                  Use $this->fieldsFactory()->Publish() to generate resulting array
     */
    public function fields();
    
    /**
     * @abstract    Return List Of Objects with required filters
     *
     * @param       string  $filter                 Filters for Object List.
     * @param       array   $params                 Search parameters for result List.
     *                      $params["max"]              Maximum Number of results
     *                      $params["offset"]           List Start Offset
     *                      $params["sortfield"]        Field name for sort list (Available fields listed below)
     *                      $params["sortorder"]        List Order Constrain (Default = ASC)
     *
     * @return      array   $data                   List of all Object main data
     *                       $data["meta"]["total"]     Total Number of results
     *                       $data["meta"]["current"]   Total Number of results
     */
    public function objectsList($filter = null, $params = null);
    
    /**
     * @abstract    Return requested Object Data
     *
     * @param   string  $objectId           Object Id.
     * @param   array   $fields             List of requested fields
     *
     * @return  false|array
     */
    public function get($objectId = null, $fields = 0);

    /**
     * @abstract     Update or Create requested Object Data
     *
     * @param   string  $objectId           Object Id.  If NULL, Object needs to be created.
     * @param   array   $objectData         List of requested fields
     *
     * @return  false|string        Object Id.  If False, Object wasn't created.
     */
    public function set($objectId = null, $objectData = null);

    /**
     * @abstract   Delete requested Object
     *
     * @param      string   $objectId       Object Id
     *
     * @return     false|string
     */
    public function delete($objectId = null);
    
    /**
     * @abstract   Set Lock for a specific object
     *
     *                  This function is used to prevent further actions
     *                  on currently edited objects. Node name & Type are
     *                  single, but Ids have to be stored as list
     *
     * @param      null|int|string   $objectId     Local Object Identifier or Empty if New Object
     *
     * @return     bool
     */
    public function lock($objectId = "new");

    /**
     * @abstract   Get Lock Status for a specific object
     *
     * @param      null|int|string   $objectId     Local Object Identifier or Empty if New Object
     *
     * @return     bool
     */
    public function isLocked($objectId = "new");
    
    /**
     * @abstract   Delete Current active Lock
     *
     * @param      null|int|string   $objectId     Local Object Identifier or Empty if New Object
     *
     * @return     bool
     */
    public function unLock($objectId = "new");
    
    /**
     * @abstract   Return Object Status
     */
    public static function getIsDisabled();
}
