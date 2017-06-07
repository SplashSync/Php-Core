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
 * @abstract    This class implements Objects Update Flag
 */
trait UpdateFlagTrait
{
    
    /**
     * Set Operations Updated Flag
     * 
     * @abstract This flag is set when an update is done during Set Operation.
     *           Using this flag is useful to reduce exchanges with databases
     * @var bool
     */
    private     $Update         = False;
    
    
    //====================================================================//
    //  Update Flag Management
    //====================================================================//

    /**
     * @abstract    Flag Object For Database Update
     * 
     * @return      self
     */
    protected function needUpdate()
    {
        $this->Update   =   True;
        return $this;
    }    

    /**
     * @abstract    Clear Update Flag
     * 
     * @return      self
     */
    protected function isUpdated()
    {
        $this->Update   =   True;
        return $this;
    }    
    
    /**
     * @abstract    is Database Update Needed
     * 
     * @return      bool
     */
    protected function isToUpdate()
    {
        return $this->Update;
    }    
}
