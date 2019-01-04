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

namespace Splash\Tests\WsSpecials;

use ArrayObject;
use Splash\Client\Splash;
use Splash\Tests\Tools\ObjectsCase;
use Splash\Tests\Tools\Traits\ObjectsSetTestsTrait;

/**
 * @abstract    Product Special Test Suite - Products Fields Verifications
 *
 * @author SplashSync <contact@splashsync.com>
 */
class S00ProductTest extends ObjectsCase
{
    use ObjectsSetTestsTrait;

    use \Splash\Tests\Tools\Traits\Product\AssertionsTrait;
    use \Splash\Tests\Tools\Traits\Product\VariantsTrait;
    use \Splash\Tests\Tools\Traits\Product\ImagesTrait;
    use \Splash\Models\Objects\ListsTrait;

    /** @var array */
    protected $currentVariation = array();

    /** @var array */
    protected $currentImages = array();

    /**
     * @dataProvider objectTypesProvider
     *
     * @param mixed $testSequence
     * @param mixed $objectType
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
        $this->assertInternalType('array', $this->fields, 'Product Fields List is not an Array!');

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
     * @dataProvider objectFieldsProvider
     *
     * @param mixed      $testSequence
     * @param mixed      $objectType
     * @param mixed      $field
     * @param null|mixed $forceObjectId
     */
    public function testVariantsFromModule($testSequence, $objectType, $field, $forceObjectId = null)
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

        foreach ($this->objectVariantsProvider() as $variationData) {
            //====================================================================//
            //   Store Current Tested Variant
            $this->currentVariation = $variationData;
            //====================================================================//
            //   Execute Set Test
            $this->coreTestSetSingleFieldFromModule($objectType, $field, $forceObjectId);
        }
    }

    /**
     * @dataProvider objectFieldsProvider
     *
     * @param mixed      $testSequence
     * @param mixed      $objectType
     * @param mixed      $field
     * @param null|mixed $forceObjectId
     */
    public function testVariantsFieldFromService($testSequence, $objectType, $field, $forceObjectId = null)
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

        foreach ($this->objectVariantsProvider() as $variationData) {
            //====================================================================//
            //   Store Current Tested Variant
            $this->currentVariation = $variationData;
            //====================================================================//
            //   Execute Set Test
            $this->coreTestSetSingleFieldFromService($objectType, $field, $forceObjectId);
        }
    }

    /**
     * @dataProvider productImagesProvider
     *
     * @param mixed $testSequence
     * @param mixed $objectType
     * @param mixed $images
     */
    public function testImagesFromModule($testSequence, $objectType, $images)
    {
        $this->coreTestImagesFromModule($testSequence, $objectType, $images);
    }

    /**
     * @dataProvider productImagesProvider
     *
     * @param mixed $testSequence
     * @param mixed $objectType
     * @param mixed $images
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

        foreach ($this->objectVariantsProvider() as $variationData) {
            //====================================================================//
            //   Store Current Tested Variant
            $this->currentVariation = $variationData;
            //====================================================================//
            //   Execute Set Test
            $this->coreTestImagesFromModule($testSequence, $objectType, $images);
        }
    }

    /**
     * @abstract    Override Parent Function to Add Variants Attributes
     * @abstract    Ensure Set/Write Test is Possible & Generate Fake Object Data
     *              -> This Function uses Preloaded Fields
     *              -> If Md5 provided, check Current Field was Modified
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
        if (is_null($field)) {
            $field = self::findFieldByTag($fields, 'http://schema.org/Product', 'alternateName');
        }
        $this->assertNotEmpty($field);
        if (is_null($field)) {
            return false;
        }
        //====================================================================//
        //  Generated Object Data
        $fakeData = $this->generateObjectData($objectType, $field, $unik);
        $this->assertInternalType('array', $fakeData);

        //====================================================================//
        //   Add Attributes Fields To Fields List for Verifications
        if (!empty($this->currentVariation)) {
            $this->fields[] = self::findFieldByTag($fields, 'http://schema.org/Product', 'VariantAttributeCode');
            $this->fields[] = self::findFieldByTag($fields, 'http://schema.org/Product', 'VariantAttributeName');
            $this->fields[] = self::findFieldByTag($fields, 'http://schema.org/Product', 'VariantAttributeValue');
        }

        //====================================================================//
        // Return Generated Object Data
        return array_merge(is_array($fakeData) ? $fakeData : array(), $this->currentVariation, $this->currentImages);
    }
}
