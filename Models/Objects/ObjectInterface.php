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
     * @abstract    Return List Of Available Fields for Splash Object
     *
     * @return      array   $data       List of all available fields
     *                                  All data must match with Splash Data Types
     *                                  Use $this->FieldsFactory()->Create() to create all fields instances
     *                                  Use $this->FieldsFactory()->Publish() to generate resulting array
     */
    public function fields();
    
    /**
     * @abstract    Return List Of Objects with required filters
     *
     * @param       array   $filter                 Filters for Object List.
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
     * @param       array   $id             Object Id.
     * @param       array   $list           List of requested fields
     *
     * @return      array                   Object Data
    */
    public function get($id = null, $list = 0);

        
    /**
     * @abstract     Update or Create requested Object Data
     *
     * @param        array  $id             Object Id.  If NULL, Object needs to be created.
     * @param        array  $list           List of requested fields
     *
     * @return       string $id             Object Id.  If NULL or False, Object wasn't created.
     */
    public function set($id = null, $list = null);

    /**
     * @abstract   Delete requested Object
     *
     * @param      int         $id             Object Id
     *
     * @return     int                         0 if KO, >0 if OK
     */
    public function delete($id = null);
}
