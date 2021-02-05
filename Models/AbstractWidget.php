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

namespace   Splash\Models;

use Splash\Components\BlocksFactory;
use Splash\Components\FieldsFactory;
use Splash\Core\SplashCore      as Splash;
use Splash\Models\Widgets\DatesManagerTrait;
use Splash\Models\Widgets\WidgetInterface;

/**
 * This class is a base class for all Splash Widgets.
 *
 * @author      B. Paquier <contact@splashsync.com>
 */
abstract class AbstractWidget implements WidgetInterface
{
    use DatesManagerTrait;

    //====================================================================//
    // *******************************************************************//
    //  WIDGET GENERICS PARAMETERS
    // *******************************************************************//
    //====================================================================//

    /** @var string */
    const SIZE_XS = "col-sm-6 col-md-4 col-lg-3";
    /** @var string */
    const SIZE_SM = "col-sm-6 col-md-6 col-lg-4";
    /** @var string */
    const SIZE_DEFAULT = "col-sm-12 col-md-6 col-lg-6";
    /** @var string */
    const SIZE_M = "col-sm-12 col-md-6 col-lg-6";
    /** @var string */
    const SIZE_L = "col-sm-12 col-md-6 col-lg-8";
    /** @var string */
    const SIZE_XL = "col-sm-12 col-md-12 col-lg-12";

    //====================================================================//
    // Define Standard Options for this Widget
    // Override this array to change default options for your widget
    //====================================================================//
    /** @var array */
    public static $OPTIONS = array();

    /**
     * @var FieldsFactory
     */
    protected static $fields;

    /**
     * @var BlocksFactory
     */
    protected static $blocks;

    /**
     * Widget Disable Flag. Override this flag to disable Widget.
     *
     * @var bool
     */
    protected static $DISABLED = false;

    /**
     * Widget Name
     *
     * @var string
     */
    protected static $NAME = __CLASS__;

    /**
     * Widget Description
     *
     * @var string
     */
    protected static $DESCRIPTION = __CLASS__;

    /**
     * Widget Icon (FontAwesome or Glyph ico tag)
     *
     * @var string
     */
    protected static $ICO = "fa fa-info";

    //====================================================================//
    // General Class Variables
    //====================================================================//

    /**
     * Get Operations Output Buffer
     *
     * This variable is used to store Widget Array during Get Operations
     *
     * @var array
     */
    private $Out = array();

    //====================================================================//
    //  STATIC CLASS ACCESS
    //  Creation & Acces to all subclasses Instances
    //====================================================================//

    /**
     * Get a singleton FieldsFactory Class
     *
     * Acces to Object Fields Creation Functions
     *
     * @return FieldsFactory
     */
    public static function fieldsFactory()
    {
        //====================================================================//
        // Initialize Field Factory Class
        if (isset(self::$fields)) {
            return self::$fields;
        }

        //====================================================================//
        // Initialize Class
        self::$fields = new FieldsFactory();

        //====================================================================//
        //  Load Translation File
        Splash::translator()->load("objects");

        return self::$fields;
    }

    /**
     * Get a singleton BlocksFactory Class
     * Acces to Widgets Contents Blocks Functions
     *
     * @return BlocksFactory
     */
    public static function blocksFactory()
    {
        //====================================================================//
        // Initialize Field Factory Class
        if (isset(self::$blocks)) {
            return self::$blocks;
        }

        //====================================================================//
        // Initialize Class
        self::$blocks = new BlocksFactory();

        return self::$blocks;
    }

    //====================================================================//
    //  COMMON CLASS INFORMATIONS
    //====================================================================//

    /**
     * Return type of this Widget Class
     *
     * @return string
     */
    public static function getType()
    {
        return pathinfo(__FILE__, PATHINFO_FILENAME);
    }

    /**
     * Return name of this Widget Class
     *
     * @return string
     */
    public function getName()
    {
        return self::trans(static::$NAME);
    }

    /**
     * Return Description of this Widget Class
     *
     * @return string
     */
    public function getDesc()
    {
        return self::trans(static::$DESCRIPTION);
    }

    /**
     * Return Widget Status
     *
     * @return null|bool
     */
    public static function getIsDisabled()
    {
        return static::$DISABLED;
    }

    /**
     * Return Widget Icon
     *
     * @return string
     */
    public static function getIcon()
    {
        return static::$ICO;
    }

    /**
     * Return Widget Defaults Options
     *
     * @return array
     */
    public static function getOptions()
    {
        return static::$OPTIONS;
    }

    /**
     * Return Widget Customs Parameters
     * Used to Customize Widget on Splash Dashboard
     *
     * @return array|false Array of Field from Fields Factory
     */
    public function getParameters()
    {
        return array();
    }

    //====================================================================//
    //  TRANSLATIONS MANAGEMENT
    //====================================================================//

    /**
     * Load translations from a specified INI file into Static array.
     * If data for file already loaded, do nothing.
     * All data in translation array are stored in UTF-8 format.
     * trans_loaded is completed with $file key.
     *
     * @param string $fileName File name to load (.ini file).
     *                         Must be "file" or "file@local" for local language files:
     *                         If $FileName is "file@local" instead of "file" then we look for local lang file
     *                         in localpath/langs/code_CODE/file.lang
     *
     * @return bool
     */
    public function load($fileName)
    {
        return Splash::translator()->load($fileName);
    }

    /**
     * Return text translated of text received as parameter (and encode it into HTML)
     *
     * @param string $key     Key to translate
     * @param string $param1  chaine de param1
     * @param string $param2  chaine de param2
     * @param string $param3  chaine de param3
     * @param string $param4  chaine de param4
     * @param int    $maxsize Max length of text
     *
     * @return string Translated string (encoded into HTML entities and UTF8)
     */
    public static function trans(
        $key,
        $param1 = '',
        $param2 = '',
        $param3 = '',
        $param4 = '',
        $maxsize = 0
    ) {
        return Splash::translator()
            ->translate($key, $param1, $param2, $param3, $param4, $maxsize);
    }

    //====================================================================//
    //  COMMON CLASS VALIDATION
    //====================================================================//

    /**
     * Run Validation procedure on this object Class & Return return
     *
     * @return bool
     */
    public function validate()
    {
        return Splash::validate()->isValidWidget(__CLASS__);
    }

    //====================================================================//
    //  COMMON CLASS SETTERS
    //====================================================================//

    /**
     * Set Widget Title
     *
     * @param string $text
     *
     * @return $this
     */
    public function setTitle($text)
    {
        $this->Out["title"] = self::trans($text);

        return $this;
    }

    /**
     * Set Widget SubTitle
     *
     * @param string $text
     *
     * @return $this
     */
    public function setSubTitle($text)
    {
        $this->Out["subtitle"] = self::trans($text);

        return $this;
    }

    /**
     * Set Widget Icon
     *
     * @param string $text
     *
     * @return $this
     */
    public function setIcon($text)
    {
        $this->Out["icon"] = $text;

        return $this;
    }

    /**
     * Set Widget Blocks
     *
     * @param array $blocks
     *
     * @return $this
     */
    public function setBlocks($blocks)
    {
        $this->Out["blocks"] = $blocks;

        return $this;
    }

    /**
     * Render / Return Widget Data Array
     *
     * @return array
     */
    public function render()
    {
        return $this->Out;
    }

    //====================================================================//
    //  COMMON CLASS SERVER ACTIONS
    //====================================================================//

    /**
     * Get Definition Array for requested Widget Type
     *
     * @return array
     */
    public function description()
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();

        //====================================================================//
        // Build & Return Widget Description Array
        return array(
            //====================================================================//
            // General Object definition
            "type" => $this->getType(),                   // Widget Type Name
            "name" => $this->getName(),                   // Widget Display Neme
            "description" => $this->getDesc(),                   // Widget Descritioon
            "icon" => $this->getIcon(),                   // Widget Icon
            "disabled" => $this->getIsDisabled(),             // Is This Widget Enabled or Not?
            //====================================================================//
            // Widget Default Options
            "options" => $this->getOptions(),                // Widget Default Options Array
            //====================================================================//
            // Widget Parameters
            "parameters" => $this->getParameters(),             // Widget Default Options Array
        );
    }
}
