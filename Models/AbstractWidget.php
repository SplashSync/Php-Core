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

namespace   Splash\Models;

use Splash\Core\SplashCore      as Splash;
use Splash\Models\Widgets\DatesManagerTrait;
use Splash\Models\Widgets\WidgetInterface;

/**
 * This class is a base class for all Splash Widgets.
 */
abstract class AbstractWidget implements WidgetInterface
{
    use DatesManagerTrait;
    use Objects\FieldsFactoryTrait;
    use Objects\TranslatorTrait;
    use Widgets\BlocksFactoryTrait;

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

    /**
     * Define Standard Options for this Widget
     * Override this array to change default options for your widget
     *
     * @var array
     */
    public static array $options = array();

    /**
     * Widget Disable Flag. Override this flag to disable Widget.
     *
     * @var bool
     */
    protected static bool $disabled = false;

    /**
     * Widget Name
     *
     * @var string
     */
    protected static string $name = __CLASS__;

    /**
     * Widget Description
     *
     * @var string
     */
    protected static string $description = __CLASS__;

    /**
     * Widget Icon (FontAwesome or Glyph ico tag)
     *
     * @var string
     */
    protected static string $ico = "fa fa-info";

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
    private array $out = array();

    //====================================================================//
    //  COMMON CLASS INFORMATIONS
    //====================================================================//

    /**
     * Return type of this Widget Class
     *
     * @return string
     */
    public static function getType(): string
    {
        return pathinfo(__FILE__, PATHINFO_FILENAME);
    }

    /**
     * Return name of this Widget Class
     *
     * @return string
     */
    public function getName(): string
    {
        return self::trans(static::$name);
    }

    /**
     * Return Description of this Widget Class
     *
     * @return string
     */
    public function getDesc(): string
    {
        return self::trans(static::$description);
    }

    /**
     * Return Widget Status
     *
     * @return bool
     */
    public static function isDisabled(): bool
    {
        return static::$disabled;
    }

    /**
     * Return Widget Icon
     *
     * @return string
     */
    public static function getIcon(): string
    {
        return static::$ico;
    }

    /**
     * Return Widget Defaults Options
     *
     * @return array
     */
    public static function getOptions(): array
    {
        return static::$options;
    }

    /**
     * Return Widget Customs Parameters
     * Used to Customize Widget on Splash Dashboard
     *
     * @return array Array of Field from Fields Factory
     */
    public function getParameters(): array
    {
        return array();
    }

    //====================================================================//
    //  COMMON CLASS VALIDATION
    //====================================================================//

    /**
     * Run Validation procedure on this widget Class
     *
     * @return bool
     */
    public function validate(): bool
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
    public function setTitle(string $text): self
    {
        $this->out["title"] = self::trans($text);

        return $this;
    }

    /**
     * Set Widget SubTitle
     *
     * @param string $text
     *
     * @return $this
     */
    public function setSubTitle(string $text): self
    {
        $this->out["subtitle"] = self::trans($text);

        return $this;
    }

    /**
     * Set Widget Icon
     *
     * @param string $text
     *
     * @return $this
     */
    public function setIcon(string $text): self
    {
        $this->out["icon"] = $text;

        return $this;
    }

    /**
     * Set Widget Blocks
     *
     * @param array $blocks
     *
     * @return $this
     */
    public function setBlocks(array $blocks): self
    {
        $this->out["blocks"] = $blocks;

        return $this;
    }

    /**
     * Render / Return Widget Data Array
     *
     * @return array
     */
    public function render(): array
    {
        return $this->out;
    }

    //====================================================================//
    //  COMMON CLASS SERVER ACTIONS
    //====================================================================//

    /**
     * Get Definition Array for requested Widget Type
     *
     * @return array
     */
    public function description(): array
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();

        //====================================================================//
        // Build & Return Widget Description Array
        return array(
            //====================================================================//
            // General Object definition
            "type" => $this->getType(),                     // Widget Type Name
            "name" => $this->getName(),                     // Widget Display Name
            "description" => $this->getDesc(),              // Widget Description
            "icon" => $this->getIcon(),                     // Widget Icon
            "disabled" => $this->isDisabled(),           // Is This Widget Enabled or Not?
            //====================================================================//
            // Widget Default Options
            "options" => $this->getOptions(),               // Widget Default Options Array
            //====================================================================//
            // Widget Parameters
            "parameters" => $this->getParameters(),         // Widget Default Options Array
        );
    }
}
