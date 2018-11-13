<?php

namespace Splash\Tests\Tools\Traits\Product;

/**
 * @abstract    Splash Test Tools - Products PhpUnit Specific Assertions
 *
 * @author SplashSync <contact@splashsync.com>
 */
trait AssertionsTrait
{
    
    //==============================================================================
    //      SPLASH PRODUCT SPECIFIC ASSERTIONS
    //==============================================================================
    
    /**
     * @abstract        Verify Object Type is Product
     * @param string    $objectType         Tested Object Type Name
     * @return bool
     */
    public function assertIsProductType($objectType)
    {
        if ($objectType != "Product") {
            $this->assertTrue(true);
            return false;
        }
        return true;
    }
    
    /**
     * @abstract        Verify Product Title Fields
     * @return void
     */
    public function assertValidTitle()
    {
        //====================================================================//
        //   Verify this Field
        $comment    =   "Product Name with Options";
        $this->assertFieldIsDefined("http://schema.org/Product", "name", $comment);
        $this->assertFieldIsRead("http://schema.org/Product", "name", $comment);
        $this->assertFieldHasFormat("http://schema.org/Product", "name", [SPL_T_VARCHAR, SPL_T_MVARCHAR], $comment);
        
        //====================================================================//
        //   Verify Product Base Name
        $baseName   =   self::findFieldByTag($this->fields, "http://schema.org/Product", "alternateName");
        if ($baseName) {
            $baseComment    =   "Product Name without Options";
            $this->assertFieldIsRead(
                "http://schema.org/Product",
                "alternateName",
                $baseComment
            );
            $this->assertFieldIsWrite(
                "http://schema.org/Product",
                "alternateName",
                $baseComment
            );
            $this->assertFieldHasFormat(
                "http://schema.org/Product",
                "alternateName",
                [SPL_T_VARCHAR, SPL_T_MVARCHAR],
                $baseComment
            );
            
            $comment    =   "When Product Name without Options is provided, Product Name";
            $this->assertFieldNotWrite("http://schema.org/Product", "name", $comment);
        }
    }
    
    /**
     * @abstract        Verify Product Short Description
     * @return void
     */
    public function assertValidShortDescription()
    {
        $comment    =   "Product Short Description";
        $this->assertFieldIsDefined(
            "http://schema.org/Product",
            "description",
            $comment
        );
        $this->assertFieldHasFormat(
            "http://schema.org/Product",
            "description",
            [SPL_T_VARCHAR, SPL_T_MVARCHAR],
            $comment
        );
        $this->assertFieldIsRead(
            "http://schema.org/Product",
            "description",
            $comment
        );
    }

    /**
     * @abstract        Verify Product Long Description
     * @return void
     */
    public function assertValidLongDescription()
    {
        $comment    =   "Product Long Description";
        $this->assertFieldHasFormat(
            "http://schema.org/Product",
            "articleBody",
            [SPL_T_TEXT, SPL_T_MTEXT],
            $comment
        );
    }

    /**
     * @abstract        Verify Product Main Flags
     * @return void
     */
    public function assertValidProductFlags()
    {
        $itemProp   =  "http://schema.org/Product";
        $formats    =   [SPL_T_BOOL];
        
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
     * @abstract        Verify Product Prices
     * @return void
     */
    public function assertValidProductPrices()
    {
        $itemProp   =  "http://schema.org/Product";
        $formats    =   [SPL_T_PRICE];
        
        //====================================================================//
        //   Main Customer Price
        $this->assertFieldHasFormat($itemProp, "price", $formats, "Product Customer Price");
        $this->assertFieldIsRead($itemProp, "price", "Product Customer Price");
        $this->assertFieldNotRequired($itemProp, "price", "Product Customer Price");

        //====================================================================//
        //   Wholesale Price
        $this->assertFieldHasFormat($itemProp, "wholesalePrice", $formats, "Product Wholesale Price");
        $this->assertFieldIsRead($itemProp, "wholesalePrice", "Product Wholesale Price");
        $this->assertFieldNotRequired($itemProp, "wholesalePrice", "Product Wholesale Price");
    }
    
    /**
     * @abstract        Verify Product Shipping Infos
     * @return void
     */
    public function assertValidProductShipping()
    {
        $itemProp   =  "http://schema.org/Product";
        $formats    =   [SPL_T_DOUBLE];
        
        //====================================================================//
        //   Verify All Dimensions Fields
        $fields     =  ["width", "depth", "height", "surface", "volume", "weight"];
        foreach ($fields as $field) {
            $this->assertFieldHasFormat($itemProp, $field, $formats, "Product " . ucwords($field));
            $this->assertFieldIsRead($itemProp, $field, "Product " . ucwords($field));
            $this->assertFieldNotRequired($itemProp, $field, "Product " . ucwords($field));
        }
    }
    
    /**
     * @abstract        Verify Product Images Fields
     * @return void
     */
    public function assertValidProductImages()
    {
        $itemProp   =  "http://schema.org/Product";
        //====================================================================//
        //   Verify Product Images Defined
        $image   =   self::findFieldByTag($this->fields, $itemProp, "image");
        if (!$image) {
            return;
        }
        //====================================================================//
        //   Verify Image Field
        $comment    =   "Product Images List";
        $this->assertFieldIsDefined($itemProp, "image", $comment);
        $this->assertFieldIsRead($itemProp, "image", $comment);
        $this->assertFieldHasFormat($itemProp, "image", [SPL_T_IMG . "@" . SPL_T_LIST], $comment);
        
        //====================================================================//
        //   Verify Cover Flag
        $coverComment   =   "Product Image is Cover Flag";
        $this->assertFieldIsDefined($itemProp, "isCover", $coverComment);
        $this->assertFieldIsRead($itemProp, "isCover", $coverComment);
        $this->assertFieldHasFormat($itemProp, "isCover", [SPL_T_BOOL . "@" . SPL_T_LIST], $coverComment);
        
        //====================================================================//
        //   Verify Visible Flag
        $enComment      =   "Product Image is Visible Flag";
        $this->assertFieldIsDefined($itemProp, "isVisibleImage", $enComment);
        $this->assertFieldIsRead($itemProp, "isVisibleImage", $enComment);
        $this->assertFieldHasFormat($itemProp, "isVisibleImage", [SPL_T_BOOL . "@" . SPL_T_LIST], $enComment);
        
        //====================================================================//
        //   Verify Image Position
        $posComment     =   "Product Image Position";
        $this->assertFieldIsDefined($itemProp, "positionImage", $posComment);
        $this->assertFieldIsRead($itemProp, "positionImage", $posComment);
        $this->assertFieldHasFormat($itemProp, "positionImage", [SPL_T_INT . "@" . SPL_T_LIST], $posComment);
        
        //====================================================================//
        //   Write Verifications
        if ($image->write) {
            $this->assertFieldIsWrite($itemProp, "image", $comment);
            $this->assertFieldIsWrite($itemProp, "isCover", $coverComment);
            $this->assertFieldIsWrite($itemProp, "isVisibleImage", $enComment);
        }
    }

    /**
     * @abstract        Verify Product Variants Fields
     * @return void
     */
    public function assertValidProductVariants()
    {
        $itemProp   =  "http://schema.org/Product";
        //====================================================================//
        //   Verify Product Variants Are Defined
        $variantCode    =   self::findFieldByTag($this->fields, $itemProp, "VariantAttributeCode");
        if (!$variantCode) {
            return;
        }
        $formats    =   [
            SPL_T_VARCHAR . "@" . SPL_T_LIST,
            SPL_T_MVARCHAR . "@" . SPL_T_LIST
        ];

        //====================================================================//
        //   Verify Attributes Code is Not Multilang
        $this->assertFieldHasFormat(
            $itemProp,
            "VariantAttributeCode",
            [SPL_T_VARCHAR . "@" . SPL_T_LIST],
            "Product Variant Attribute Code"
        );
        
        //====================================================================//
        //   Verify Attributes Fields
        $fields     =  [
            "VariantAttributeCode"  => "Variant Attribute Code",
            "VariantAttributeName"  => "Variant Attribute Name",
            "VariantAttributeValue" => "Variant Attribute Value",
            ];
        foreach ($fields as $fieldId => $fieldName) {
            //====================================================================//
            //   Common Read Verifications
            $this->assertFieldIsDefined($itemProp, $fieldId, "Product " . $fieldName);
            $this->assertFieldHasFormat($itemProp, $fieldId, $formats, "Product " . $fieldName);
            $this->assertFieldIsRead($itemProp, $fieldId, "Product " . $fieldName);
            $this->assertFieldNotRequired($itemProp, $fieldId, "Product " . $fieldName);
            //====================================================================//
            //   Write Verifications
            if ($variantCode->write) {
                $this->assertFieldIsWrite($itemProp, $fieldId, "Product " . $fieldName);
            }
        }
    }
}
