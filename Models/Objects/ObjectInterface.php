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
     * @param       array   $Filter                 Filters for Object List.
     * @param       array   $Params                 Search parameters for result List.
     *                      $params["max"]              Maximum Number of results
     *                      $params["offset"]           List Start Offset
     *                      $params["sortfield"]        Field name for sort list (Available fields listed below)
     *                      $params["sortorder"]        List Order Constrain (Default = ASC)
     *
     * @return      array   $data                   List of all Object main data
     *                       $data["meta"]["total"]     Total Number of results
     *                       $data["meta"]["current"]   Total Number of results
     */
    public function objectsList($Filter = null, $Params = null);
    
    /**
     * @abstract    Return requested Object Data
     *
     * @param   string  $ObjectId           Object Id.
     * @param   array   $Fields             List of requested fields
     *
     * @return  array                       Object Data
    */
    public function get($ObjectId = null, $Fields = 0);

    /**
     * @abstract     Update or Create requested Object Data
     *
     * @param   string  $ObjectId           Object Id.  If NULL, Object needs to be created.
     * @param   array   $Data               List of requested fields
     *
     * @return  string                      Object Id.  If NULL or False, Object wasn't created.
     */
    public function set($ObjectId = null, $Data = null);

    /**
     * @abstract   Delete requested Object
     *
     * @param      string   $ObjectId       Object Id
     *
     * @return     string|false
     */
    public function delete($ObjectId = null);
    
    /**
     * @abstract   Set Lock for a specific object
     *
     *                  This function is used to prevent further actions
     *                  on currently edited objects. Node name & Type are
     *                  single, but Ids have to be stored as list
     *
     * @param      int|string   $Identifier     Local Object Identifier or Empty if New Object
     *
     * @return     bool
     */
    public function lock($Identifier = "new");

    /**
     * @abstract   Get Lock Status for a specific object
     *
     * @param      int|string   $Identifier     Local Object Identifier or Empty if New Object
     *
     * @return     bool
     */
    public function isLocked($Identifier = "new");
    
    /**
     * @abstract   Delete Current active Lock
     *
     * @param      int|string   $Identifier     Local Object Identifier or Empty if New Object
     *
     * @return     bool
     */
    public function unLock($Identifier = "new");
    
    /**
     * @abstract   Return Object Status
     */
    public static function getIsDisabled();
}
