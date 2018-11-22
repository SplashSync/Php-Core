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
    
    /**
     * @abstract   New Widget Block Storage
     * @var        null|ArrayObject
     */
    private $new;
    
    /**
     * @abstract   Widget Block List Storage
     * @var        Array
     */
    private $blocks;
    
    /**
     * @abstract    Initialise Class
     */
    public function __construct()
    {
        //====================================================================//
        // Initialize Data Storage
        $this->new            = null;
        $this->blocks         = array();
    }

    //====================================================================//
    //  BLOCKS CONTENTS MANAGEMENT
    //====================================================================//

    /**
     *  @abstract   Create a new block with default parameters
     *
     *  @param      string      $blockType       Standard Widget Block Type
     *  @param      array       $blockOptions    Block Options
     *
     *  @return     $this
     */
    private function addBlock($blockType, $blockOptions = null)
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
        $this->new->type        =   $blockType;
        //====================================================================//
        // Set Block Options
        $this->new->options     =   $blockOptions;
        //====================================================================//
        // Set Block Data
        $this->new->data        =   array();
        
        return $this;
    }
    
    /**
     *  @abstract   Set Block Data Array Key
     *
     *  @param      string      $name
     *  @param      string|array       $value
     *
     *  @return     $this
     */
    public function setData($name, $value)
    {
        if (!is_null($this->new)) {
            //====================================================================//
            // Impact Block Data Array
            $this->new->data[$name]   = $value;
        }
        
        return $this;
    }
    
    /**
     *  @abstract   Extract Block Data From Content Input Array
     *
     *  @param      array       $input
     *  @param      string      $index
     *
     *  @return     $this
     */
    public function extractData($input, $index)
    {
        if (isset($input[$index])) {
            $this->setData($index, $input[$index]);
        }
        
        return $this;
    }
    
    /**
     *  @abstract   Set Block Options Array Key
     *
     *  @param      string      $name
     *  @param      array       $value
     *
     *  @return     $this
     */
    public function setOption($name, $value)
    {
        if (!is_null($this->new)) {
            //====================================================================//
            // Impact Block Data Array
            $this->new->option[$name]   = $value;
        }        
        
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
     * @abstract   Save Current New Block, Return List & Clean
     * @return     array|false
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
        //====================================================================//
        // Return fields List
        } else {
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
     *  @param      string      $text           Block Content Text
     *  @param      array       $blockOptions   Block Options
     *
     *  @return     $this
     */
    public function addTextBlock($text, $blockOptions = self::COMMONS_OPTIONS)
    {
        $this->addBlock("TextBlock", $blockOptions);
        $this->setData("text", $text);
        
        return $this;
    }
    
    //====================================================================//
    //  BLOCKS || NOTIFICATIONS BLOCK
    //====================================================================//
    
    /**
     *  @abstract   Create a new Notification Block
     *
     *  @param      array   $contents           Block Contents
     *                          ["error"]       Error Message
     *                          ["warning"]     Warning Message
     *                          ["info"]        Info Message
     *                          ["success"]     Success Message
     *
     *  @param      array   $blockOptions       Block Options
     *
     *  @return     $this
     */
    public function addNotificationsBlock($contents, $blockOptions = self::COMMONS_OPTIONS)
    {
        //====================================================================//
        //  Create Block
        $this->addBlock("NotificationsBlock", $blockOptions);
        //====================================================================//
        //  Add Contents
        if (isset($contents["error"])) {
            $this->setData("error", $contents["error"]);
        }
        if (isset($contents["warning"])) {
            $this->setData("warning", $contents["warning"]);
        }
        if (isset($contents["info"])) {
            $this->setData("info", $contents["info"]);
        }
        if (isset($contents["success"])) {
            $this->setData("success", $contents["success"]);
        }
        
        return $this;
    }
    
    //====================================================================//
    //  BLOCKS || SIMPLE TABLE BLOCK
    //====================================================================//
    
    /**
     *  @abstract   Create a new Table Block
     *
     *  @param      array   $contents           Array of Rows Contents (Text or Html)
     *
     *  @param      array   $blockOptions       Block Options
     *
     *  @return     $this
     */
    public function addTableBlock($contents, $blockOptions = self::COMMONS_OPTIONS)
    {
        $this->addBlock("TableBlock", $blockOptions);
        $this->setData("rows", $contents);
        
        return $this;
    }
    
    
    //====================================================================//
    //  BLOCKS || SPARK INFOS BLOCK
    //====================================================================//
    
    /**
     *  @abstract   Create a new Table Block
     *
     *  @param      array   $contents           Array of Rows Contents (Text or Html)
     *
     *  @param      array   $blockOptions       Block Options
     *
     *  @return     $this
     */
    public function addSparkInfoBlock($contents, $blockOptions = self::COMMONS_OPTIONS)
    {
        $this->addBlock("SparkInfoBlock", $blockOptions);
        
        //====================================================================//
        //  Add Contents
        $this->extractData($contents, "title");
        $this->extractData($contents, "fa_icon");
        $this->extractData($contents, "glyph_icon");
        $this->extractData($contents, "value");
        $this->extractData($contents, "chart");
        
        return $this;
    }

    //====================================================================//
    //  BLOCKS || MORRIS GRAPHS BLOCK
    //====================================================================//
    
    /**
     * @abstract   Create a new Morris Bar Graph Block
     *
     * @param   array   $dataSet            Morris DataSet Array
     * @param   string  $chartType          Rendering Mode
     * @param   array   $chartOptions       Rendering passed Options
     * @param   array   $blockOptions       Block Options
     *
     * @return  $this
     */
    public function addMorrisGraphBlock(
        $dataSet,
        $chartType = "Bar",
        $chartOptions = array(),
        $blockOptions = self::COMMONS_OPTIONS
    ) {
        if (!in_array($chartType, ["Bar", "Area", "Line"])) {
            $blockContents   = array("warning"   => "Wrong Morris Chart Block Type (ie: Bar, Area, Line)");
            $this->addNotificationsBlock($blockContents);
        }
        //====================================================================//
        //  Create Block
        $this->addBlock("Morris" . $chartType . "Block", $blockOptions);
        //====================================================================//
        //  Add Set Chart Data
        $this->setData("dataset", $dataSet);
        
        //====================================================================//
        //  Add Chart Parameters
        $this->extractData($chartOptions, "title");
        $this->extractData($chartOptions, "xkey");
        $this->extractData($chartOptions, "ykeys");
        $this->extractData($chartOptions, "labels");
        
        return $this;
    }

    /**
     * @abstract   Create a new Morris Donut Graph Block
     *
     * @param   array   $dataSet            Morris DataSet Array
     * @param   array   $chartOptions       Rendering passed Options
     * @param   array   $blockOptions       Block Options
     *
     * @return  $this
     */
    public function addMorrisDonutBlock($dataSet, $chartOptions = array(), $blockOptions = self::COMMONS_OPTIONS)
    {
        //====================================================================//
        //  Create Block
        $this->addBlock("MorrisDonutBlock", $blockOptions);
        //====================================================================//
        //  Add Set Chart Data
        $this->setData("dataset", $dataSet);
        //====================================================================//
        //  Add Chart Parameters
        $this->extractData($chartOptions, "title");
        
        return $this;
    }
}
