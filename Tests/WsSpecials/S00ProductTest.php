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

namespace Splash\Tests\WsSpecials;

use ArrayObject;
use Splash\Client\Splash;
use Splash\Tests\Tools\ObjectsCase;
use Splash\Tests\Tools\Traits\ObjectsSetTestsTrait;

/**
 * Product Special Test Suite - Products Fields Verifications
 *
 * @author SplashSync <contact@splashsync.com>
 */
class S00ProductTest extends ObjectsCase
{
    use ObjectsSetTestsTrait;

    use \Splash\Tests\Tools\Traits\Product\AssertionsTrait;
    use \Splash\Tests\Tools\Traits\Product\DefinitionsTrait;
    use \Splash\Tests\Tools\Traits\Product\VariantsTrait;
    use \Splash\Tests\Tools\Traits\Product\ImagesTrait;
    use \Splash\Models\Objects\ListsTrait;

    /** @var array */
    const ATTRIBUTES = array('VariantA','VariantB');

    /** @var array */
    protected $currentVariation = array();

    /** @var array */
    protected $currentImages = array();

    /**
     * @dataProvider objectTypesProvider
     *
     * @param string $testSequence
     * @param string $objectType
     *
     * @return void
     */
    public function testFieldsDefinition($testSequence, $objectType)
    {
        //====================================================================//
        //   TEST INIT
        //====================================================================//
        if (!$this->assertIsProductType($objectType)) {
            return;
        }
        $this->loadLocalTestSequence($testSequence);

        //====================================================================//
        //   Load Fields
        $this->fields = Splash::object($objectType)->fields();
        $this->assertNotEmpty($this->fields, 'Product Fields List is Empty!');
        $this->assertIsArray($this->fields, 'Product Fields List is not an Array!');

        //====================================================================//
        //   VALIDATE PRODUCTS FIELDS DEFINITIONS
        //====================================================================//

        $this->assertValidTitle();
        $this->assertValidShortDescription();
        $this->assertValidLongDescription();
        $this->assertValidProductFlags();
        $this->assertValidProductPrices();
        $this->assertValidProductShipping();
        $this->assertValidProductImages();
        $this->assertValidProductVariants();
    }

    /**
     * Test Creation of Products Variants from Module
     *
     * @dataProvider objectFieldsProvider
     *
     * @param string      $testSequence
     * @param string      $objectType
     * @param ArrayObject $field
     *
     * @return void
     */
    public function testVariantsFromModule($testSequence, $objectType, $field)
    {
        //====================================================================//
        //   TEST INIT
        //====================================================================//
        if (!$this->initVariantsTest($testSequence, $objectType, $field)) {
            return;
        }

        //====================================================================//
        //   CREATE A FIRST VARIANT PRODUCT
        //====================================================================//

        //====================================================================//
        //   Setup Current Tested Variant
        $this->currentVariation = array_merge(
            $this->getProductsVariant(),
            $this->getProductsAttributes(self::ATTRIBUTES)
        );

        //====================================================================//
        //   Execute Create First Product Variant
        $newData = $this->prepareForTesting($objectType, $field);
        $this->assertIsArray($newData);
        $variantProductId = $this->setObjectFromModule($objectType, $newData);

        //====================================================================//
        //   Create Multiple Variants for Same Product
        //====================================================================//

        for ($i = 0; $i < 2; $i++) {
            //====================================================================//
            //   Load Fields
            $this->fields = Splash::object($objectType)->fields();
            $this->assertNotEmpty($this->fields, 'Product Fields List is Empty!');
            //====================================================================//
            //   Setup Current Tested Variant
            $this->currentVariation = array_merge(
                $this->getProductsVariant($variantProductId),
                $this->getProductsAttributes(self::ATTRIBUTES)
            );
            //====================================================================//
            //   Execute Set Test
            $this->coreTestSetSingleFieldFromModule($objectType, $field);
        }

        //====================================================================//
        //   Execute Delete First Product Variant
        $this->deleteObjectFromModule($objectType, $variantProductId);
    }

    /**
     * Test Creation of Products Variants from Service
     *
     * @dataProvider objectFieldsProvider
     *
     * @param string      $testSequence
     * @param string      $objectType
     * @param ArrayObject $field
     *
     * @return void
     */
    public function testVariantsFieldFromService($testSequence, $objectType, $field)
    {
        //====================================================================//
        //   TEST INIT
        //====================================================================//
        if (!$this->initVariantsTest($testSequence, $objectType, $field)) {
            return;
        }

        //====================================================================//
        //   CREATE A FIRST VARIANT PRODUCT
        //====================================================================//

        //====================================================================//
        //   Setup Current Tested Variant
        $this->currentVariation = array_merge(
            $this->getProductsVariant(),
            $this->getProductsAttributes(self::ATTRIBUTES)
        );
        //====================================================================//
        //   Execute Create First Product Variant
        $newData = $this->prepareForTesting($objectType, $field);
        $this->assertIsArray($newData);
        $variantProductId = $this->setObjectFromService($objectType, $newData);

        //====================================================================//
        //   Create Multiple Variants for Same Product
        //====================================================================//

        for ($i = 0; $i < 2; $i++) {
            //====================================================================//
            //   Load Fields
            $this->fields = Splash::object($objectType)->fields();
            $this->assertNotEmpty($this->fields, 'Product Fields List is Empty!');
            //====================================================================//
            //   Setup Current Tested Variant
            $this->currentVariation = array_merge(
                $this->getProductsVariant($variantProductId),
                $this->getProductsAttributes(self::ATTRIBUTES)
            );
            //====================================================================//
            //   Execute Set Test
            $this->coreTestSetSingleFieldFromService($objectType, $field);
        }

        //====================================================================//
        //   Execute Delete First Product Variant
        $this->deleteObjectFromModule($objectType, $variantProductId);
    }

    /**
     * Test Product Images Writing from Module
     *
     * @dataProvider productImagesProvider
     *
     * @param string $testSequence
     * @param string $objectType
     * @param array  $images
     *
     * @return void
     */
    public function testImagesFromModule($testSequence, $objectType, $images)
    {
        $this->coreTestImagesFromModule($testSequence, $objectType, $images);
    }

    /**
     * Test Variant Products Images Writing from Module
     *
     * @dataProvider productImagesProvider
     *
     * @param string $testSequence
     * @param string $objectType
     * @param array  $images
     *
     * @return void
     */
    public function testVariantImagesFromModule($testSequence, $objectType, $images)
    {
        //====================================================================//
        //   TEST INIT
        //====================================================================//
        if (!$this->assertIsProductType($objectType)) {
            return;
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
            return;
        }

        for ($i = 0; $i < 2; $i++) {
            //====================================================================//
            //   Store Current Tested Variant
            $this->currentVariation = $this->getProductsAttributes(self::ATTRIBUTES);
            //====================================================================//
            //   Execute Set Test
            $this->coreTestImagesFromModule($testSequence, $objectType, $images);
        }
    }

    /**
     * Override Parent Function to Add Variants Attributes
     * Ensure Set/Write Test is Possible & Generate Fake Object Data
     *  -> This Function uses Preloaded Fields
     *  -> If Md5 provided, check Current Field was Modified
     *
     * @param string      $objectType Current Object Type
     * @param ArrayObject $field      Current Tested Field (ArrayObject)
     * @param bool        $unik       Ask for Unik Field Data
     *
     * @return array|false Generated Data Block or False if not Allowed
     */
    public function prepareForTesting($objectType, $field = null, $unik = true)
    {
        //====================================================================//
        //   Verify Test is Required
        if (!$this->verifyTestIsAllowed($objectType, $field)) {
            return false;
        }

        //====================================================================//
        //   Load Fields
        $fields = Splash::object($objectType)->fields();

        //====================================================================//
        //   Ensure a Field is Requested
        // Alternate Name if Exists (Variable Product)
        // Else Base Name
        if (is_null($field)) {
            $name = self::findFieldByTag($fields, static::$itemProp, 'name');
            $altName = self::findFieldByTag($fields, static::$itemProp, 'alternateName');
            $field = $altName ? $altName : $name;
        }
        $this->assertNotEmpty($field);
        if (is_null($field)) {
            return false;
        }
        //====================================================================//
        //  Generated Object Data
        $fakeData = $this->generateObjectData($objectType, $field, $unik);
        $this->assertIsArray($fakeData);

        //====================================================================//
        //   Add Attributes Fields To Fields List for Verifications
        if (!empty($this->currentVariation)) {
            // List of Product Variants
            $this->fields[] = self::findFieldByTag($fields, static::$itemProp, 'Variants');
            // Variant Attribute Codes
            $this->fields[] = self::findFieldByTag($fields, static::$itemProp, static::$attrCode);
            // Variant Attribute Name
            foreach ($this->findMultiFields(static::$attrName, $fields) as $field) {
                // Only Write Attributes Names
                if (isset($field->write) && !empty($field->write)) {
                    $this->fields[] = $field;
                }
            }

            // Variant Attribute Value
            $this->fields = array_merge($this->fields, $this->findMultiFields(static::$attrValue, $fields));
        }
        //====================================================================//
        // Return Generated Object Data
        return array_merge($fakeData, $this->currentVariation, $this->currentImages);
    }
}
