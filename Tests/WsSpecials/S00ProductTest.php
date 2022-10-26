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

namespace Splash\Tests\WsSpecials;

use Exception;
use Splash\Client\Splash;
use Splash\Models\Objects\ListsTrait;
use Splash\Tests\Tools\ObjectsCase;
use Splash\Tests\Tools\Traits\ObjectsSetTestsTrait;
use Splash\Tests\Tools\Traits\Product as Traits;

/**
 * Product Special Test Suite - Products Fields Verifications
 *
 * @author SplashSync <contact@splashsync.com>
 */
class S00ProductTest extends ObjectsCase
{
    use ObjectsSetTestsTrait;

    use Traits\AssertionsTrait;
    use Traits\DefinitionsTrait;
    use Traits\VariantsTrait;
    use Traits\ImagesTrait;
    use ListsTrait;

    /** @var array */
    const ATTRIBUTES = array('VariantA','VariantB');

    /**
     * @var array
     */
    protected array $currentVariation = array();

    /**
     * @var array
     */
    protected array $currentImages = array();

    /**
     * @dataProvider objectTypesProvider
     *
     * @param string $testSequence
     * @param string $objectType
     *
     * @throws Exception
     *
     * @return void
     */
    public function testFieldsDefinition(string $testSequence, string $objectType): void
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
     * @param string $testSequence
     * @param string $objectType
     * @param array  $field
     *
     * @throws Exception
     *
     * @return void
     */
    public function testVariantsFromModule(string $testSequence, string $objectType, array $field): void
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
     * @param string $testSequence
     * @param string $objectType
     * @param array  $field
     *
     * @throws Exception
     *
     * @return void
     */
    public function testVariantsFieldFromService(string $testSequence, string $objectType, array $field): void
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
     * @throws Exception
     *
     * @return void
     */
    public function testImagesFromModule(string $testSequence, string $objectType, array $images)
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
     * @throws Exception
     *
     * @return void
     */
    public function testVariantImagesFromModule(string $testSequence, string $objectType, array $images): void
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
     * @param string     $objectType Current Object Type
     * @param null|array $field      Current Tested Field
     * @param bool       $unique     Ask for Unique Field Data
     *
     * @throws Exception
     *
     * @return null|array Generated Data Block or False if not Allowed
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function prepareForTesting(string $objectType, array $field = null, bool $unique = true): ?array
    {
        //====================================================================//
        //   Verify Test is Required
        if (!$this->verifyTestIsAllowed($objectType, $field)) {
            return null;
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
            $field = $altName ?: $name;
        }
        $this->assertNotEmpty($field);
        //====================================================================//
        //  Generated Object Data
        $fakeData = $this->generateObjectData($objectType, $field, $unique);
        $this->assertIsArray($fakeData);

        //====================================================================//
        //   Add Attributes Fields To Fields List for Verifications
        if (!empty($this->currentVariation)) {
            // List of Product Variants
            $variants = self::findFieldByTag($fields, static::$itemProp, 'Variants');
            if ($variants) {
                $this->fields[] = $variants;
            }
            // Variant Attribute Codes
            $attrField = self::findFieldByTag($fields, static::$itemProp, static::$attrCode);
            if ($attrField) {
                $this->fields[] = $attrField;
            }
            // Variant Attribute Name
            foreach ($this->findMultiFields(static::$attrName, $fields) as $field) {
                // Only Write Attributes Names
                if (isset($field['write']) && !empty($field['write'])) {
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
