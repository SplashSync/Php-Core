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
 * Splash Test Tools - Products PhpUnit Specific Assertions
 *
 * @author SplashSync <contact@splashsync.com>
 */
trait AssertionsTrait
{
    //==============================================================================
    //      SPLASH PRODUCT SPECIFIC ASSERTIONS
    //==============================================================================

    /**
     * Verify Object Type is Product
     *
     * @param string $objectType Tested Object Type Name
     *
     * @return bool
     */
    public function assertIsProductType(string $objectType): bool
    {
        if ("Product" != $objectType) {
            $this->assertTrue(true);

            return false;
        }

        return true;
    }

    /**
     * Verify Product Title Fields
     *
     * @return void
     */
    public function assertValidTitle()
    {
        //====================================================================//
        //   Verify this Field
        $comment = "Product Name with Options";
        $this->assertFieldIsDefined(static::$itemProp, static::$fullTitle, $comment);
        $this->assertFieldIsRead(static::$itemProp, static::$fullTitle, $comment);
        $this->assertFieldHasFormat(
            static::$itemProp,
            "name",
            array(SPL_T_VARCHAR, SPL_T_MVARCHAR),
            $comment
        );

        //====================================================================//
        //   Verify Product Base Name
        $baseName = self::findFieldByTag($this->fields, static::$itemProp, static::$baseTitle);
        if ($baseName) {
            $baseComment = "Product Name without Options";
            $this->assertFieldIsRead(static::$itemProp, static::$baseTitle, $baseComment);
            $this->assertFieldIsWrite(static::$itemProp, static::$baseTitle, $baseComment);
            $this->assertFieldHasFormat(
                static::$itemProp,
                static::$baseTitle,
                array(SPL_T_VARCHAR, SPL_T_MVARCHAR),
                $baseComment
            );

            $comment = "When Product Name without Options is provided, Product Name";
            $this->assertFieldNotWrite(static::$itemProp, static::$fullTitle, $comment);
        }
    }

    /**
     * Verify Product Short Description
     *
     * @return void
     */
    public function assertValidShortDescription()
    {
        $comment = "Product Short Description";
        $this->assertFieldIsDefined(
            static::$itemProp,
            "description",
            $comment
        );
        $this->assertFieldHasFormat(
            static::$itemProp,
            "description",
            array(SPL_T_VARCHAR, SPL_T_MVARCHAR),
            $comment
        );
        $this->assertFieldIsRead(
            static::$itemProp,
            "description",
            $comment
        );
    }

    /**
     * Verify Product Long Description
     *
     * @return void
     */
    public function assertValidLongDescription()
    {
        $comment = "Product Long Description";
        $this->assertFieldHasFormat(
            static::$itemProp,
            "articleBody",
            array(SPL_T_TEXT, SPL_T_MTEXT),
            $comment
        );
    }

    /**
     * Verify Product Main Flags
     *
     * @return void
     */
    public function assertValidProductFlags()
    {
        $itemProp = static::$itemProp;
        $formats = array(SPL_T_BOOL);

        //====================================================================//
        //   Enabled Flag
        $this->assertFieldHasFormat($itemProp, "active", $formats, "Product Enabled Flag");
        $this->assertFieldIsRead($itemProp, "offered", "Product Offered Flag");

        //====================================================================//
        //   Offered Flag
        $this->assertFieldIsDefined($itemProp, "offered", "Product Offered Flag");
        $this->assertFieldHasFormat($itemProp, "offered", $formats, "Product Offered Flag");
        $this->assertFieldIsRead($itemProp, "offered", "Product Offered Flag");
        $this->assertFieldIsWrite($itemProp, "offered", "Product Offered Flag");
        $this->assertFieldNotRequired($itemProp, "offered", "Product Offered Flag");

        //====================================================================//
        //   Buy Flag
        $this->assertFieldHasFormat($itemProp, "ordered", $formats, "Product Buy Flag");
        $this->assertFieldIsRead($itemProp, "ordered", "Product Buy Flag");
        $this->assertFieldNotRequired($itemProp, "ordered", "Product Buy Flag");
    }

    /**
     * Verify Product Prices
     *
     * @return void
     */
    public function assertValidProductPrices()
    {
        $itemProp = static::$itemProp;
        $formats = array(SPL_T_PRICE);

        //====================================================================//
        //   Main Customer Price
        $this->assertFieldHasFormat($itemProp, "price", $formats, "Product Customer Price");
        $this->assertFieldNotRequired($itemProp, "price", "Product Customer Price");

        //====================================================================//
        //   Wholesale Price
        $this->assertFieldHasFormat($itemProp, "wholesalePrice", $formats, "Product Wholesale Price");
        $this->assertFieldNotRequired($itemProp, "wholesalePrice", "Product Wholesale Price");
    }

    /**
     * Verify Product Shipping Infos
     *
     * @return void
     */
    public function assertValidProductShipping()
    {
        $itemProp = static::$itemProp;
        $formats = array(SPL_T_DOUBLE);

        //====================================================================//
        //   Verify All Dimensions Fields
        $fields = array("width", "depth", "height", "surface", "volume", "weight");
        foreach ($fields as $field) {
            $this->assertFieldHasFormat($itemProp, $field, $formats, "Product ".ucwords($field));
            $this->assertFieldNotRequired($itemProp, $field, "Product ".ucwords($field));
        }
    }

    /**
     * Verify Product Images Fields
     *
     * @return void
     */
    public function assertValidProductImages()
    {
        $itemProp = static::$itemProp;
        //====================================================================//
        //   Verify Product Images Defined
        $image = self::findFieldByTag($this->fields, $itemProp, "image");
        if (!$image) {
            return;
        }
        //====================================================================//
        //   Verify Image Field
        $comment = "Product Images List";
        $this->assertFieldIsDefined($itemProp, "image", $comment);
        $this->assertFieldIsRead($itemProp, "image", $comment);
        $this->assertFieldHasFormat($itemProp, "image", array(SPL_T_IMG."@".SPL_T_LIST), $comment);

        //====================================================================//
        //   Verify Cover Flag
        $coverComment = "Product Image is Cover Flag";
        $this->assertFieldIsDefined($itemProp, "isCover", $coverComment);
        $this->assertFieldIsRead($itemProp, "isCover", $coverComment);
        $this->assertFieldHasFormat($itemProp, "isCover", array(SPL_T_BOOL."@".SPL_T_LIST), $coverComment);

        //====================================================================//
        //   Verify Visible Flag
        $enComment = "Product Image is Visible Flag";
        $this->assertFieldIsDefined($itemProp, "isVisibleImage", $enComment);
        $this->assertFieldIsRead($itemProp, "isVisibleImage", $enComment);
        $this->assertFieldHasFormat($itemProp, "isVisibleImage", array(SPL_T_BOOL."@".SPL_T_LIST), $enComment);

        //====================================================================//
        //   Verify Image Position
        $posComment = "Product Image Position";
        $this->assertFieldIsDefined($itemProp, "positionImage", $posComment);
        $this->assertFieldIsRead($itemProp, "positionImage", $posComment);
        $this->assertFieldHasFormat($itemProp, "positionImage", array(SPL_T_INT."@".SPL_T_LIST), $posComment);

        //====================================================================//
        //   Write Verifications
        if ($image['write']) {
            $this->assertFieldIsWrite($itemProp, "image", $comment);
            $this->assertFieldIsWrite($itemProp, "isCover", $coverComment);
            $this->assertFieldIsWrite($itemProp, "isVisibleImage", $enComment);
        }
    }

    /**
     * Verify Product Variants Fields
     *
     * @return void
     */
    public function assertValidProductVariants()
    {
        $itemProp = static::$itemProp;
        //====================================================================//
        //   Verify Product Variants Are Defined
        $variantCode = self::findFieldByTag($this->fields, $itemProp, "VariantAttributeCode");
        if (!$variantCode) {
            return;
        }
        $formats = array(
            SPL_T_VARCHAR."@".SPL_T_LIST,
            "objectid::Product"."@".SPL_T_LIST
        );

        //====================================================================//
        //   Verify Parent Product Id is Defined
        $parentComment = "Variant Product Parent";
        $this->assertFieldIsDefined($itemProp, "isVariationOf", $parentComment);
        $this->assertFieldIsRead($itemProp, "isVariationOf", $parentComment);
        $this->assertFieldNotWrite($itemProp, "isVariationOf", $parentComment);
        $this->assertFieldHasFormat($itemProp, "isVariationOf", array(SPL_T_VARCHAR), $parentComment);

        //====================================================================//
        //   Verify Attributes Code is Not Multilang
        $this->assertFieldHasFormat(
            $itemProp,
            "VariantAttributeCode",
            array(SPL_T_VARCHAR."@".SPL_T_LIST),
            "Product Variant Attribute Code"
        );

        //====================================================================//
        //   Verify Attributes Fields
        $fields = array(
            "Variants" => "Product Variants List",
            "VariantAttributeCode" => "Variant Attribute Code",
            "VariantAttributeName" => "Variant Attribute Name",
            "VariantAttributeValue" => "Variant Attribute Value",
        );
        foreach ($fields as $fieldId => $fieldName) {
            //====================================================================//
            //   Common Read Verifications
            $this->assertFieldIsDefined($itemProp, $fieldId, "Product ".$fieldName);
            $this->assertFieldHasFormat($itemProp, $fieldId, $formats, "Product ".$fieldName);
            $this->assertFieldIsRead($itemProp, $fieldId, "Product ".$fieldName);
            $this->assertFieldNotRequired($itemProp, $fieldId, "Product ".$fieldName);
            //====================================================================//
            //   Write Verifications
            if ($variantCode['write'] && ("VariantAttributeName" != $fieldId)) {
                $this->assertFieldIsWrite($itemProp, $fieldId, "Product ".$fieldName);
            }
        }
    }
}
