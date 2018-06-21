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
     * @param string    $ObjectType         Tested Object Type Name
     * @return bool
     */
    public function assertIsProductType($ObjectType)
    {
        if ($ObjectType != "Product") {
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
        $Comment    =   "Product Name with Options";
        $this->assertFieldIsDefined("http://schema.org/Product", "name", $Comment);
        $this->assertFieldIsRead("http://schema.org/Product", "name", $Comment);
        $this->assertFieldHasFormat("http://schema.org/Product", "name", [SPL_T_VARCHAR, SPL_T_MVARCHAR], $Comment);
        
        //====================================================================//
        //   Verify Product Base Name
        $BaseName   =   self::findFieldByTag($this->Fields, "http://schema.org/Product", "alternateName");
        if ($BaseName) {
            $BaseComment    =   "Product Name without Options";
            $this->assertFieldIsRead(
                "http://schema.org/Product",
                "alternateName",
                $BaseComment
            );
            $this->assertFieldIsWrite(
                "http://schema.org/Product",
                "alternateName",
                $BaseComment
            );
            $this->assertFieldHasFormat(
                "http://schema.org/Product",
                "alternateName",
                [SPL_T_VARCHAR, SPL_T_MVARCHAR],
                $BaseComment
            );
            
            $Comment    =   "When Product Name without Options is provided, Product Name";
            $this->assertFieldNotWrite("http://schema.org/Product", "name", $Comment);
        }
    }
    
    /**
     * @abstract        Verify Product Short Description
     * @return void
     */
    public function assertValidShortDescription()
    {
        $Comment    =   "Product Short Description";
        $this->assertFieldIsDefined(
            "http://schema.org/Product",
            "description",
            $Comment
        );
        $this->assertFieldHasFormat(
            "http://schema.org/Product",
            "description",
            [SPL_T_VARCHAR, SPL_T_MVARCHAR],
            $Comment
        );
        $this->assertFieldIsRead(
            "http://schema.org/Product",
            "description",
            $Comment
        );
    }

    /**
     * @abstract        Verify Product Long Description
     * @return void
     */
    public function assertValidLongDescription()
    {
        $Comment    =   "Product Long Description";
        $this->assertFieldHasFormat(
            "http://schema.org/Product",
            "articleBody",
            [SPL_T_TEXT, SPL_T_MTEXT],
            $Comment
        );
    }

    /**
     * @abstract        Verify Product Main Flags
     * @return void
     */
    public function assertValidProductFlags()
    {
        $ItemProp   =  "http://schema.org/Product";
        $Formats    =   [SPL_T_BOOL];
        
        //====================================================================//
        //   Enabled Flag
        $this->assertFieldHasFormat($ItemProp, "active", $Formats, "Product Enabled Flag");
        $this->assertFieldIsRead($ItemProp, "offered", "Product Offered Flag");
        
        //====================================================================//
        //   Offered Flag
        $this->assertFieldIsDefined($ItemProp, "offered", "Product Offered Flag");
        $this->assertFieldHasFormat($ItemProp, "offered", $Formats, "Product Offered Flag");
        $this->assertFieldIsRead($ItemProp, "offered", "Product Offered Flag");
        $this->assertFieldIsWrite($ItemProp, "offered", "Product Offered Flag");
        $this->assertFieldNotRequired($ItemProp, "offered", "Product Offered Flag");

        //====================================================================//
        //   Buy Flag
        $this->assertFieldHasFormat($ItemProp, "ordered", $Formats, "Product Buy Flag");
        $this->assertFieldIsRead($ItemProp, "ordered", "Product Buy Flag");
        $this->assertFieldNotRequired($ItemProp, "ordered", "Product Buy Flag");
    }
    
    /**
     * @abstract        Verify Product Prices
     * @return void
     */
    public function assertValidProductPrices()
    {
        $ItemProp   =  "http://schema.org/Product";
        $Formats    =   [SPL_T_PRICE];
        
        //====================================================================//
        //   Main Customer Price
        $this->assertFieldHasFormat($ItemProp, "price", $Formats, "Product Customer Price");
        $this->assertFieldIsRead($ItemProp, "price", "Product Customer Price");
        $this->assertFieldNotRequired($ItemProp, "price", "Product Customer Price");

        //====================================================================//
        //   Wholesale Price
        $this->assertFieldHasFormat($ItemProp, "wholesalePrice", $Formats, "Product Wholesale Price");
        $this->assertFieldIsRead($ItemProp, "wholesalePrice", "Product Wholesale Price");
        $this->assertFieldNotRequired($ItemProp, "wholesalePrice", "Product Wholesale Price");
    }
    
    /**
     * @abstract        Verify Product Shipping Infos
     * @return void
     */
    public function assertValidProductShipping()
    {
        $ItemProp   =  "http://schema.org/Product";
        $Formats    =   [SPL_T_DOUBLE];
        
        //====================================================================//
        //   Verify All Dimensions Fields
        $Fields     =  ["width", "depth", "height", "surface", "volume", "weight"];
        foreach ($Fields as $Field) {
            $this->assertFieldHasFormat($ItemProp, $Field, $Formats, "Product " . ucwords($Field));
            $this->assertFieldIsRead($ItemProp, $Field, "Product " . ucwords($Field));
            $this->assertFieldNotRequired($ItemProp, $Field, "Product " . ucwords($Field));
        }
    }
    
    /**
     * @abstract        Verify Product Images Fields
     * @return void
     */
    public function assertValidProductImages()
    {
        $ItemProp   =  "http://schema.org/Product";
        //====================================================================//
        //   Verify Product Images Defined
        $Image   =   self::findFieldByTag($this->Fields, $ItemProp, "image");
        if (!$Image) {
            return;
        }
        //====================================================================//
        //   Verify Image Field
        $Comment    =   "Product Images List";
        $this->assertFieldIsDefined($ItemProp, "image", $Comment);
        $this->assertFieldIsRead($ItemProp, "image", $Comment);
        $this->assertFieldHasFormat($ItemProp, "image", [SPL_T_IMG . "@" . SPL_T_LIST], $Comment);
        
        //====================================================================//
        //   Verify Cover Flag
        $CoverComment   =   "Product Image is Cover Flag";
        $this->assertFieldIsDefined($ItemProp, "isCover", $CoverComment);
        $this->assertFieldIsRead($ItemProp, "isCover", $CoverComment);
        $this->assertFieldHasFormat($ItemProp, "isCover", [SPL_T_BOOL . "@" . SPL_T_LIST], $CoverComment);
        
        //====================================================================//
        //   Verify Visible Flag
        $EnComment      =   "Product Image is Visible Flag";
        $this->assertFieldIsDefined($ItemProp, "isVisibleImage", $EnComment);
        $this->assertFieldIsRead($ItemProp, "isVisibleImage", $EnComment);
        $this->assertFieldHasFormat($ItemProp, "isVisibleImage", [SPL_T_BOOL . "@" . SPL_T_LIST], $EnComment);
        
        //====================================================================//
        //   Verify Image Position
        $PosComment     =   "Product Image Position";
        $this->assertFieldIsDefined($ItemProp, "positionImage", $PosComment);
        $this->assertFieldIsRead($ItemProp, "positionImage", $PosComment);
        $this->assertFieldHasFormat($ItemProp, "positionImage", [SPL_T_INT . "@" . SPL_T_LIST], $PosComment);
        
        //====================================================================//
        //   Write Verifications
        if ($Image->write) {
            $this->assertFieldIsWrite($ItemProp, "image", $Comment);
            $this->assertFieldIsWrite($ItemProp, "isCover", $CoverComment);
            $this->assertFieldIsWrite($ItemProp, "isVisibleImage", $EnComment);
        }
    }

    /**
     * @abstract        Verify Product Variants Fields
     * @return void
     */
    public function assertValidProductVariants()
    {
        $ItemProp   =  "http://schema.org/Product";
        //====================================================================//
        //   Verify Product Variants Are Defined
        $VariantCode    =   self::findFieldByTag($this->Fields, $ItemProp, "VariantAttributeCode");
        if (!$VariantCode) {
            return;
        }
        $Formats    =   [
            SPL_T_VARCHAR . "@" . SPL_T_LIST,
            SPL_T_MVARCHAR . "@" . SPL_T_LIST
        ];

        //====================================================================//
        //   Verify Attributes Code is Not Multilang
        $this->assertFieldHasFormat(
            $ItemProp,
            "VariantAttributeCode",
            [SPL_T_VARCHAR . "@" . SPL_T_LIST],
            "Product Variant Attribute Code"
        );
        
        //====================================================================//
        //   Verify Attributes Fields
        $Fields     =  [
            "VariantAttributeCode"  => "Variant Attribute Code",
            "VariantAttributeName"  => "Variant Attribute Name",
            "VariantAttributeValue" => "Variant Attribute Value",
            ];
        foreach ($Fields as $Id => $Name) {
            //====================================================================//
            //   Common Read Verifications
            $this->assertFieldIsDefined($ItemProp, $Id, "Product " . $Name);
            $this->assertFieldHasFormat($ItemProp, $Id, $Formats, "Product " . $Name);
            $this->assertFieldIsRead($ItemProp, $Id, "Product " . $Name);
            $this->assertFieldNotRequired($ItemProp, $Id, "Product " . $Name);
            //====================================================================//
            //   Write Verifications
            if ($VariantCode->write) {
                $this->assertFieldIsWrite($ItemProp, $Id, "Product " . $Name);
            }
        }
    }
}
