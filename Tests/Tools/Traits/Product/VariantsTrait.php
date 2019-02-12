<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2019 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Tests\Tools\Traits\Product;

use ArrayObject;
use Splash\Client\Splash;
use Splash\Models\Helpers\ListsHelper;
use Splash\Models\Helpers\ObjectsHelper;

/**
 * Splash Test Tools - Products Variants PhpUnit Specific Features
 *
 * @author SplashSync <contact@splashsync.com>
 */
trait VariantsTrait
{
    /**
     * Override Parent Function to Filter on Products Fields
     */
    public function objectFieldsProvider()
    {
        $fields = array();
        foreach (parent::objectFieldsProvider() as $field) {
            //====================================================================//
            // Filter Non Product Fields
            if ("Product" != $field[1]) {
                continue;
            }
//            //====================================================================//
//            // DEBUG => Focus on a Specific Fields
//            if ($field[2]->id != "image@images") {
//                continue;
//            }
            $fields[] = $field;
        }
        if (empty($fields)) {
            $this->markTestSkipped('This Server has no Product Object Type.');
        }

        return $fields;
    }
    
    //==============================================================================
    //      SPLASH PRODUCT VARIANTS SPECIFIC FUNCTIONS
    //==============================================================================

    /**
     * Generate Product Variants Fields Data
     *
     * @param string $variantProductId Existing Variant Product Id
     *
     * @return array
     */
    protected function getProductsVariant($variantProductId = null)
    {
        //====================================================================//
        //   Verify Product Base Name
        $field   =   self::findFieldByTag($this->fields, "http://schema.org/Product", "Variants");
        $this->assertNotEmpty($field);
        if (is_null($field)) {
            return array();
        }
        //====================================================================//
        //   Generate Product Splash Object Id
        if (is_null($variantProductId)) {
            return array(ListsHelper::listName($field->id)  =>  array());
        }
        //====================================================================//
        //   Return Field Value
        return array(ListsHelper::listName($field->id)  =>  array(array(
            ListsHelper::fieldName($field->id) => ObjectsHelper::encode("Product", $variantProductId)
        )));
    }

    /**
     * Generate Variations Attributes
     *
     * @param array $attributesCodes
     */
    protected function getProductsAttributes($attributesCodes)
    {
        //====================================================================//
        //   Load Required Fields
        $code   =   self::findFieldByTag($this->fields, "http://schema.org/Product", "VariantAttributeCode");
        $this->assertNotEmpty($code);
        if (is_null($code)) {
            return array();
        }
        $result = array();
        foreach ($attributesCodes as $attributesCode) {
            $result[] = $this->getVariantCustomAttribute($attributesCode);
        }

        return array(
            self::lists()->listName($code->id) => $result
        );
    }

    /**
     * Generate Variations CustomAttribute
     *
     * @param mixed $attributesCode
     */
    protected function getVariantCustomAttribute($attributesCode)
    {
        //====================================================================//
        //   Load Required Fields
        $code   =   self::findFieldByTag($this->fields, "http://schema.org/Product", "VariantAttributeCode");
        $this->assertNotEmpty($code);
        $name   =   self::findFieldByTag($this->fields, "http://schema.org/Product", "VariantAttributeName");
        $this->assertNotEmpty($name);
        $value  =   self::findFieldByTag($this->fields, "http://schema.org/Product", "VariantAttributeValue");
        $this->assertNotEmpty($value);
        if (is_null($code) || is_null($name) || is_null($value)) {
            return array();
        }
        
        //====================================================================//
        //   Generate Random Attributes Set
        return array(
            self::lists()->fieldName($code->id)     =>      strtolower($attributesCode),
            self::lists()->fieldName($name->id)     =>      self::fakeFieldData(
                $name->type,
                null,
                array_replace_recursive($name->options, array("minLength" =>   3, "maxLength" =>   5))
            ),
            self::lists()->fieldName($value->id)     =>      self::fakeFieldData(
                $value->type,
                null,
                array_replace_recursive($value->options, array("minLength" =>   5, "maxLength" =>   10))
            ),
        );
    }
    
    /**
     * Prepare Test of Products Variants
     *
     * @param string      $testSequence
     * @param string      $objectType
     * @param ArrayObject $field
     *
     * @return bool
     */
    protected function initVariantsTest($testSequence, $objectType, $field)
    {
        //====================================================================//
        //   TEST INIT
        //====================================================================//
        if (!$this->assertIsProductType($objectType)) {
            return false;
        }
        //====================================================================//
        //   Verify Test is Required
        if (!$this->verifyTestIsAllowed($objectType, $field)) {
            return false;
        }
        $this->loadLocalTestSequence($testSequence);

        //====================================================================//
        //   Load Fields
        $this->fields = Splash::object($objectType)->fields();
        $this->assertNotEmpty($this->fields, 'Product Fields List is Empty!');

        //====================================================================//
        //   Verify Product Variants Are Defined
        $variantCode = self::findFieldByTag($this->fields, 'http://schema.org/Product', 'VariantAttributeCode');
        if (!$variantCode) {
            return false;
        }
        
        return true;
    }
}
