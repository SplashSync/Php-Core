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

/**
 * @abstract    This class is a base class for all Splash Widgets.
 * @author      B. Paquier <contact@splashsync.com>
 */

namespace   Splash\Models;

use Splash\Core\SplashCore      as Splash;

use Splash\Components\FieldsFactory;
use Splash\Components\BlocksFactory;

use Splash\Models\Widgets\DatesManagerTrait;

//====================================================================//
//********************************************************************//
//====================================================================//
//  SPLASH WIDGET BASE CLASS
//====================================================================//
//********************************************************************//
//====================================================================//

abstract class WidgetBase
{
    use DatesManagerTrait;
    
    //====================================================================//
    // *******************************************************************//
    //  WIDGET GENERICS PARAMETERS
    // *******************************************************************//
    //====================================================================//

    const SIZE_XS       = "col-sm-6 col-md-4 col-lg-3";
    const SIZE_SM       = "col-sm-6 col-md-6 col-lg-4";
    const SIZE_DEFAULT  = "col-sm-12 col-md-6 col-lg-6";
    const SIZE_M        = "col-sm-12 col-md-6 col-lg-6";
    const SIZE_L        = "col-sm-12 col-md-6 col-lg-8";
    const SIZE_XL       = "col-sm-12 col-md-12 col-lg-12";
    
    /**
     * @var Static Class Storage
     */
    protected static $fields;
    
    /**
     * @var Static Class Storage
     */
    protected static $blocks;
    
    /**
     *  Widget Disable Flag. Override this flag to disable Widget.
     */
    protected static $DISABLED        =  false;
    
    /**
     *  Widget Name
     */
    protected static $NAME            =  __CLASS__;
    
    /**
     *  Widget Description
     */
    protected static $DESCRIPTION     =  __CLASS__;

    /**
     *  Widget Icon (FontAwesome or Glyph ico tag)
     */
    protected static $ICO     =  "fa fa-info";

    //====================================================================//
    // Define Standard Options for this Widget
    // Override this array to change default options for your widget
    public static $OPTIONS       = array(
    );

    
    //====================================================================//
    // General Class Variables
    //====================================================================//
    
    
    /**
     * Get Operations Output Buffer
     *
     * @abstract This variable is used to store Widget Array during Get Operations
     *
     * @var ArrayObject
     */
    private $Out            = array();
    
    
    //====================================================================//
    //  STATIC CLASS ACCESS
    //  Creation & Acces to all subclasses Instances
    //====================================================================//
    
    /**
     *      @abstract   Get a singleton FieldsFactory Class
     *                  Acces to Object Fields Creation Functions
     *      @return     SplashFieldsFactory
     */
    public static function FieldsFactory()
    {
        //====================================================================//
        // Initialize Field Factory Class
        if (isset(self::$fields)) {
            return self::$fields;
        }
        
        //====================================================================//
        // Initialize Class
        self::$fields        = new FieldsFactory();
        
        //====================================================================//
        //  Load Translation File
        Splash::Translator()->Load("objects");
        
        return self::$fields;
    }
    
    /**
     *      @abstract   Get a singleton BlocksFactory Class
     *                  Acces to Widgets Contents Blocks Functions
     *      @return     SplashBlocksFactory
     */
    public static function BlocksFactory()
    {
        //====================================================================//
        // Initialize Field Factory Class
        if (isset(self::$blocks)) {
            return self::$blocks;
        }
        
        //====================================================================//
        // Initialize Class
        self::$blocks        = new BlocksFactory();
        
        return self::$blocks;
    }
    
    //====================================================================//
    //  COMMON CLASS INFORMATIONS
    //====================================================================//

    /**
     *      @abstract   Return type of this Widget Class
     */
    public static function getType()
    {
        return pathinfo(__FILE__, PATHINFO_FILENAME);
    }
    
    /**
     *      @abstract   Return name of this Widget Class
     */
    public function getName()
    {
        return self::Trans(static::$NAME);
    }

    /**
     *      @abstract   Return Description of this Widget Class
     */
    public function getDesc()
    {
        return self::Trans(static::$DESCRIPTION);
    }
    
    /**
     *      @abstract   Return Widget Status
     */
    public static function getIsDisabled()
    {
        return static::$DISABLED;
    }
    
    /**
     *      @abstract   Return Widget Icon
     */
    public static function getIcon()
    {
        return static::$ICO;
    }

    /**
     *      @abstract   Return Widget Defaults Options
     */
    public static function getOptions()
    {
        return static::$OPTIONS;
    }
    
    /**
     *      @abstract   Return Widget Customs Parameters
     */
    public function getParameters()
    {
        return array();
    }
    
    //====================================================================//
    //  TRANSLATIONS MANAGEMENT
    //====================================================================//

    /**
     *      @abstract       Load translations from a specified INI file into Static array.
     *                      If data for file already loaded, do nothing.
     *                      All data in translation array are stored in UTF-8 format.
     *                      trans_loaded is completed with $file key.
     *
     *      @param	string	$FileName   File name to load (.ini file). Must be "file" or "file@local" for local language files:
     *                                      If $FileName is "file@local" instead of "file" then we look for local lang file
     *                                      in localpath/langs/code_CODE/file.lang
     *
     *      @return	bool
     *
     */
    public function Load($FileName)
    {
        return Splash::Translator()->Load($FileName);
    }
    
    /**
     *      @abstract   Return text translated of text received as parameter (and encode it into HTML)
     *
     *      @param  string	$key        Key to translate
     *      @param  string	$param1     chaine de param1
     *      @param  string	$param2     chaine de param2
     *      @param  string	$param3     chaine de param3
     *      @param  string	$param4     chaine de param4
     *      @param  string	$param5     chaine de param5
     *      @param  int		$maxsize    Max length of text
     *      @return string      		Translated string (encoded into HTML entities and UTF8)
     */
    public static function Trans($key, $param1='', $param2='', $param3='', $param4='', $param5='', $maxsize=0)
    {
        return Splash::Translator()->Translate($key, $param1, $param2, $param3, $param4, $param5, $maxsize);
    }

    //====================================================================//
    //  COMMON CLASS VALIDATION
    //====================================================================//

    /**
     *      @abstract   Run Validation procedure on this object Class & Return return
     *
     *      @return     bool
     */
    public function Validate()
    {
        return Splash::Validate()->isValidWidget(__CLASS__);
    }
  
    //====================================================================//
    //  COMMON CLASS SETTERS
    //====================================================================//

    /**
     *  @abstract   Set Widget Title
     *
     *  @param      string   $text
     *
     *  @return     $this
     */
    public function setTitle($text)
    {
        $this->Out["title"]     =   self::Trans($text);
        return $this;
    }
    
    /**
     *  @abstract   Set Widget SubTitle
     *
     *  @param      string   $text
     *
     *  @return     $this
     */
    public function setSubTitle($text)
    {
        $this->Out["subtitle"]     =   self::Trans($text);
        return $this;
    }
    
    /**
     *  @abstract   Set Widget Icon
     *
     *  @param      string   $text
     *
     *  @return     $this
     */
    public function setIcon($text)
    {
        $this->Out["icon"]     =   $text;
        return $this;
    }
    
    /**
     *  @abstract   Set Widget Blocks
     *
     *  @param      array   $blocks
     *
     *  @return     $this
     */
    public function setBlocks($blocks)
    {
        $this->Out["blocks"]     =   $blocks;
        return $this;
    }
    
    /**
     *  @abstract   Render / Return Widget Data Array
     *
     *  @return     array
     */
    public function Render()
    {
        return $this->Out;
    }
    
    //====================================================================//
    //  COMMON CLASS SERVER ACTIONS
    //====================================================================//

    /**
     *  @abstract   Get Definition Array for requested Widget Type
     *
     *  @return     array
     */
    public function Description()
    {
        //====================================================================//
        // Stack Trace
        Splash::Log()->Trace(__CLASS__, __FUNCTION__);
        
        //====================================================================//
        // Build & Return Widget Description Array
        return array(
            //====================================================================//
            // General Object definition
            "type"          =>  $this->getType(),                   // Widget Type Name
            "name"          =>  $this->getName(),                   // Widget Display Neme
            "description"   =>  $this->getDesc(),                   // Widget Descritioon
            "icon"          =>  $this->getIcon(),                   // Widget Icon
            "disabled"      =>  $this->getIsDisabled(),             // Is This Widget Enabled or Not?
            //====================================================================//
            // Widget Default Options
            "options"       =>  $this->getOptions(),                // Widget Default Options Array
            //====================================================================//
            // Widget Parameters
            "parameters"    =>  $this->getParameters(),             // Widget Default Options Array
        );
    }
}
