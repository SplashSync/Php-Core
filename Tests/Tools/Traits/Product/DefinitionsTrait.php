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

namespace Splash\Tests\Tools\Traits\Product;

/**
 * Splash Test Tools - Products Fields Definitions
 * Provide Descriptions for Product Standards Fields
 *
 * @author SplashSync <contact@splashsync.com>
 */
trait DefinitionsTrait
{
    //==============================================================================
    //      SPLASH PRODUCT SPECIFIC ITEM PROP & TYPES
    //==============================================================================

    /**
     * Generic Product ItemProp for Schemas
     * Field Type: VARCHAR
     *
     * @var string
     */
    protected static $itemProp = "http://schema.org/Product";

    /**
     * Multilangual Product Complete Title. Base Title + Options
     * Field Type: VARCHAR
     *
     * @example My Perfect Product - Size XL, Color Blue
     *
     * @var string
     */
    protected static $fullTitle = "name";

    /**
     * Multilangual Product Base Title. Base Title without Options
     * Field Type: VARCHAR
     *
     * @example My Perfect Product
     *
     * @var string
     */
    protected static $baseTitle = "alternateName";

    /**
     * Product Variant Attribute Code
     * Field Type: VARCHAR
     *
     * @var string
     */
    protected static $attrCode = "VariantAttributeCode";

    /**
     * Multilangual Product Variant Attribute Name
     * Field Type: VARCHAR
     *
     * @var string
     */
    protected static $attrName = "VariantAttributeName";

    /**
     * Multilangual Product Variant Attribute Value
     * Field Type: VARCHAR
     *
     * @var string
     */
    protected static $attrValue = "VariantAttributeValue";
}
