<?php
/*
 * This file is part of SplashSync Project.
 *
 * Copyright (C) Splash Sync <www.splashsync.com>
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace   Splash\Components;

use Splash\Core\SplashCore      as Splash;
use ArrayObject;

/**
 * @abstract    This Class is a Generator for Widget Blocks Contents
 * @author      B. Paquier <contact@splashsync.com>
 */

class BlocksFactory
{
    /**
     * Default Option For Commons Blocks
     *
     * @var array
     */
    const COMMONS_OPTIONS = array(
        //==============================================================================
        //      Block BootStrap Width   => 100%
        'Width'             =>      "col-xs-12 col-sm-12 col-md-12 col-lg-12",
        //==============================================================================
        //      Allow Html Contents     => No
        "AllowHtml"         =>      false
    );
    
    
    //====================================================================//
    // Data Storage

    /**
     *      @abstract   New Widget Block Storage
     *      @var        ArrayObject
     */
    private $new;
    
    /**
     *      @abstract   Widget Block List Storage
     *      @var        Array
     */
    private $blocks;
    
    /**
     *      @abstract     Initialise Class
     *      @return         int           <0 if KO, >0 if OK
     */
    public function __construct()
    {
        //====================================================================//
        // Initialize Data Storage
        $this->new            = null;
        $this->fields         = array();
        
        return true;
    }

    //====================================================================//
    //  BLOCKS CONTENTS MANAGEMENT
    //====================================================================//

    /**
     *  @abstract   Create a new block with default parameters
     *
     *  @param      string      $Type       Standard Widget Block Type
     *  @param      array       $Options    Block Options
     *
     *  @return     $this
     */
    private function addBlock($Type, $Options = null)
    {
        //====================================================================//
        // Commit Last Created if not already done
        if (!empty($this->new)) {
            $this->commit();
        }
        //====================================================================//
        // Unset Current
        unset($this->new);
        //====================================================================//
        // Create new empty block
        $this->new          =   new ArrayObject([], ArrayObject::ARRAY_AS_PROPS);
        //====================================================================//
        // Set Block Type
        $this->new->type        =   $Type;
        //====================================================================//
        // Set Block Options
        $this->new->options     =   $Options;
        //====================================================================//
        // Set Block Data
        $this->new->data        =   array();
        
        return $this;
    }
    
    /**
     *  @abstract   Set Block Data Array Key
     *
     *  @param      string      $Name
     *  @param      array       $Value
     *
     *  @return     $this
     */
    public function setData($Name, $Value)
    {
        //====================================================================//
        // Impact Block Data Array
        $this->new->data[$Name]   = $Value;
        
        return $this;
    }
    
    /**
     *  @abstract   Extract Block Data From Content Input Arra
     *
     *  @param      array       $Contents
     *  @param      string      $Name
     *
     *  @return     $this
     */
    public function extractData($Contents, $Name)
    {
        if (isset($Contents[$Name])) {
            $this->setData($Name, $Contents[$Name]);
        }
        
        return $this;
    }
    
    /**
     *  @abstract   Set Block Options Array Key
     *
     *  @param      string      $Name
     *  @param      array       $Value
     *
     *  @return     $this
     */
    public function setOption($Name, $Value)
    {
        //====================================================================//
        // Impact Block Data Array
        $this->new->option[$Name]   = $Value;
        
        return $this;
    }
    
    /**
     *  @abstract   Save Current New Block in list & Clean
     *
     *  @return     bool
     */
    private function commit()
    {
        //====================================================================//
        // Safety Checks
        if (empty($this->new)) {
            return true;
        }
        //====================================================================//
        // Create Field List
        if (empty($this->blocks)) {
            $this->blocks   = array();
        }
        //====================================================================//
        // Insert Field List
        $this->blocks[] = $this->new;
        unset($this->new);
        
        return true;
    }
    
    /**
     *  @abstract   Save Current New Block in list & Clean
     *
     *  @return     int                     <0 if KO, >0 if OK
     */
    public function render()
    {
        //====================================================================//
        // Commit Last Created if not already done
        if (!empty($this->new)) {
            $this->commit();
        }
        //====================================================================//
        // Safety Checks
        if (empty($this->blocks)) {
            return Splash::log()->err("ErrBlocksNoList");
        } //====================================================================//
        // Return fields List
        else {
            $buffer = $this->blocks;
            unset($this->blocks);
            return $buffer;
        }
        
        return false;
    }
    
    //====================================================================//
    //  BLOCKS || SIMPLE TEXT BLOCK
    //====================================================================//
    
    /**
     *  @abstract   Create a new Text Block
     *
     *  @param      string      $Text       Block Content Text
     *  @param      array       $Options    Block Options
     *
     *  @return     $this
     */
    public function addTextBlock($Text, $Options = self::COMMONS_OPTIONS)
    {
        $this->addBlock("TextBlock", $Options);
        $this->setData("text", $Text);
        
        return $this;
    }
    
    //====================================================================//
    //  BLOCKS || NOTIFICATIONS BLOCK
    //====================================================================//
    
    /**
     *  @abstract   Create a new Notification Block
     *
     *  @param      array   $Contents           Block Contents
     *                          ["error"]       Error Message
     *                          ["warning"]     Warning Message
     *                          ["info"]        Info Message
     *                          ["success"]     Success Message
     *
     *  @param      array   $Options            Block Options
     *
     *  @return     $this
     */
    public function addNotificationsBlock($Contents, $Options = self::COMMONS_OPTIONS)
    {
        //====================================================================//
        //  Create Block
        $this->addBlock("NotificationsBlock", $Options);
        //====================================================================//
        //  Add Contents
        if (isset($Contents["error"])) {
            $this->setData("error", $Contents["error"]);
        }
        if (isset($Contents["warning"])) {
            $this->setData("warning", $Contents["warning"]);
        }
        if (isset($Contents["info"])) {
            $this->setData("info", $Contents["info"]);
        }
        if (isset($Contents["success"])) {
            $this->setData("success", $Contents["success"]);
        }
        
        return $this;
    }
    
    //====================================================================//
    //  BLOCKS || SIMPLE TABLE BLOCK
    //====================================================================//
    
    /**
     *  @abstract   Create a new Table Block
     *
     *  @param      array   $Contents           Array of Rows Contents (Text or Html)
     *
     *  @param      array   $Options            Block Options
     *
     *  @return     $this
     */
    public function addTableBlock($Contents, $Options = self::COMMONS_OPTIONS)
    {
        $this->addBlock("TableBlock", $Options);
        $this->setData("rows", $Contents);
        
        return $this;
    }
    
    
    //====================================================================//
    //  BLOCKS || SPARK INFOS BLOCK
    //====================================================================//
    
    /**
     *  @abstract   Create a new Table Block
     *
     *  @param      array   $Contents           Array of Rows Contents (Text or Html)
     *
     *  @param      array   $Options            Block Options
     *
     *  @return     $this
     */
    public function addSparkInfoBlock($Contents, $Options = self::COMMONS_OPTIONS)
    {
        $this->addBlock("SparkInfoBlock", $Options);
        
        //====================================================================//
        //  Add Contents
        $this->extractData($Contents, "title");
        $this->extractData($Contents, "fa_icon");
        $this->extractData($Contents, "glyph_icon");
        $this->extractData($Contents, "value");
        $this->extractData($Contents, "chart");
        
        return $this;
    }

    //====================================================================//
    //  BLOCKS || MORRIS GRAPHS BLOCK
    //====================================================================//
    
    /**
     *  @abstract   Create a new Morris Bar Graph Block
     *
     *  @param      array   $Contents           Array of Rows Contents (Text or Html)
     *
     *  @param      array   $Options            Block Options
     *
     *  @return     $this
     */
    public function addMorrisGraphBlock(
        $DataSet,
        $Type = "Bar",
        $ChartOptions = array(),
        $Options = self::COMMONS_OPTIONS
    ) {
        if (!in_array($Type, ["Bar", "Area", "Line"])) {
            $Contents   = array("warning"   => "Wrong Morris Chart Block Type (ie: Bar, Area, Line)");
            $this->BlocksFactory()->addNotificationsBlock($Contents);
        }
        //====================================================================//
        //  Create Block
        $this->addBlock("Morris" . $Type . "Block", $Options);
        //====================================================================//
        //  Add Set Chart Data
        $this->setData("dataset", $DataSet);
        
        //====================================================================//
        //  Add Chart Parameters
        $this->extractData($ChartOptions, "title");
        $this->extractData($ChartOptions, "xkey");
        $this->extractData($ChartOptions, "ykeys");
        $this->extractData($ChartOptions, "labels");
        
        return $this;
    }

    /**
     *  @abstract   Create a new Morris Donut Graph Block
     *
     *  @param      array   $Contents           Array of Rows Contents (Text or Html)
     *
     *  @param      array   $Options            Block Options
     *
     *  @return     $this
     */
    public function addMorrisDonutBlock($DataSet, $ChartOptions = array(), $Options = self::COMMONS_OPTIONS)
    {
        //====================================================================//
        //  Create Block
        $this->addBlock("MorrisDonutBlock", $Options);
        //====================================================================//
        //  Add Set Chart Data
        $this->setData("dataset", $DataSet);
        //====================================================================//
        //  Add Chart Parameters
        $this->extractData($ChartOptions, "title");
        
        return $this;
    }
}
