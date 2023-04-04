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

use Exception;
use Splash\Client\Splash;
use Splash\Models\Helpers\ListsHelper;
use Splash\Models\Helpers\ObjectsHelper;
use Splash\Tests\Tools\Fields\OoVarchar;

/**
 * Splash Test Tools - Products Variants PhpUnit Specific Features
 *
 * @author SplashSync <contact@splashsync.com>
 */
trait VariantsTrait
{
    /**
     * Override Parent Function to Filter on Products Fields
     *
     * @throws Exception
     *
     * @return array
     */
    public function objectFieldsProvider(): array
    {
        $fields = array();
        foreach (parent::objectFieldsProvider() as $index => $field) {
            //====================================================================//
            // Filter Non Product Fields
            if ("Product" != $field[1]) {
                continue;
            }
            //            //====================================================================//
            //            // DEBUG => Focus on a Specific Fields
            //            if ($field[2]['id'] != "image@images") {
            //                continue;
            //            }
            $fields[$index] = $field;
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
     * @param null|string $variantProductId Existing Variant Product Id
     *
     * @return array
     */
    protected function getProductsVariant(string $variantProductId = null): array
    {
        //====================================================================//
        //   Verify Product Base Name
        $field = self::findFieldByTag($this->fields, static::$itemProp, "Variants");
        $this->assertNotEmpty($field);
        //====================================================================//
        //   Generate Product Splash Object Id
        if (is_null($variantProductId)) {
            return array(ListsHelper::listName($field['id']) => array());
        }
        //====================================================================//
        //   Return Field Value
        return array(ListsHelper::listName($field['id']) => array(array(
            ListsHelper::fieldName($field['id']) => ObjectsHelper::encode("Product", $variantProductId)
        )));
    }

    /**
     * Generate Variations Attributes
     *
     * @param array $attributesCodes
     *
     * @return array
     */
    protected function getProductsAttributes(array $attributesCodes): array
    {
        //====================================================================//
        //   Load Required Fields
        $code = self::findFieldByTag($this->fields, static::$itemProp, static::$attrCode);
        $this->assertNotEmpty($code);
        $result = array();
        //====================================================================//
        // IF TESTED SYSTEM DEFINE POSSIBLE VARIATIONS CODES
        if (!empty($code['choices'])) {
            $attributesCodes = array();
            foreach ($code['choices'] as $choice) {
                $attributesCodes[] = $choice["key"];
            }
        }
        $this->assertNotEmpty($attributesCodes);
        //====================================================================//
        // GENERATE VARIATIONS ATTRIBUTES
        foreach ($attributesCodes as $attributesCode) {
            $result[] = $this->getVariantCustomAttribute($attributesCode);
        }

        return array(
            self::lists()->listName($code['id']) => $result
        );
    }

    /**
     * Generate Variations CustomAttribute
     *
     * @param string $attributesCode
     *
     * @throws Exception
     *
     * @return array
     */
    protected function getVariantCustomAttribute(string $attributesCode): array
    {
        //====================================================================//
        //   Load Required Fields
        //====================================================================//

        /**
         * @var array $code
         */
        $code = self::findFieldByTag($this->fields, static::$itemProp, static::$attrCode);
        $this->assertNotEmpty($code);

        /**
         * @var array[] $names
         */
        $names = $this->findMultiFields(static::$attrName);
        $this->assertIsArray($names);
        $this->assertNotEmpty($names);

        /**
         * @var array[] $values
         */
        $values = $this->findMultiFields(static::$attrValue);
        $this->assertIsArray($values);
        $this->assertNotEmpty($values);

        //====================================================================//
        // IF TESTED SYSTEM DEFINE POSSIBLE VALUES
        //====================================================================//
        // Search for a Field with Same Id as Variation Code
        $attributeField = self::findField($this->fields, array($attributesCode));
        $attributeChoices = $attributeField ? array_values(self::toArray($attributeField['choices'])) : null;

        //====================================================================//
        //   Generate Random Attributes Set
        //====================================================================//

        $attributesSet = array();
        //====================================================================//
        // Setup Attribute Type Code
        OoVarchar::applyCaseConstrains($code['options'], $attributesCode);
        $attributesSet[self::lists()->fieldName($code['id'])] = $attributesCode;

        //====================================================================//
        // Setup Attribute Type Names
        foreach ($names as $name) {
            //====================================================================//
            // Do Not Write ReadOnly Attributes Names
            if (false == (bool) $name['write']) {
                continue;
            }
            //====================================================================//
            // Add Custom Attributes Names
            $attributesSet[self::lists()->fieldName($name['id'])] = self::fakeFieldData(
                $name['type'],
                null,
                (array) array_replace_recursive(
                    self::toArray($name['options']),
                    array("minLength" => 4, "maxLength" => 7)
                )
            );
        }
        //====================================================================//
        // Setup Attribute Value Names
        foreach ($values as $value) {
            $attributesSet[self::lists()->fieldName($value['id'])] = self::fakeFieldData(
                $value['type'],
                $attributeChoices,
                (array) array_replace_recursive(
                    self::toArray($value['options']),
                    array("minLength" => 5, "maxLength" => 10)
                )
            );
        }

        return $attributesSet;
    }

    /**
     * Prepare Test of Products Variants
     *
     * @param string $testSequence
     * @param string $objectType
     * @param array  $field
     *
     * @throws Exception
     *
     * @return bool
     */
    protected function initVariantsTest(string $testSequence, string $objectType, array $field): bool
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
        $variantCode = self::findFieldByTag($this->fields, static::$itemProp, static::$attrCode);
        if (!$variantCode) {
            return false;
        }

        return true;
    }

    /**
     * Identify All Multi-lang Fields for an Attribute
     *
     * @param string       $itemtype   Item Prop Type
     * @param null|array[] $fieldsList Object Fields List
     *
     * @return array[]
     */
    protected function findMultiFields(string $itemtype, array $fieldsList = null)
    {
        $response = array();
        $fields = is_null($fieldsList) ? $this->fields : $fieldsList;
        //====================================================================//
        //   Walk on Available Languages
        if (is_array($this->settings["Langs"])) {
            foreach ($this->settings["Langs"] as $isoCode) {
                //====================================================================//
                //   Search for This Field
                $field = ($this->settings["Default_Lang"] == $isoCode)
                        ? self::findFieldByTag($fields, self::$itemProp, $itemtype)
                        : self::findFieldByTag($fields, self::$itemProp."/".$isoCode, $itemtype);
                //====================================================================//
                //   Field Not Found
                if ($field) {
                    $response[$field['id']] = $field;
                }
            }
        }

        return $response;
    }
}
