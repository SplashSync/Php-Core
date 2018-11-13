<?php

namespace Splash\Tests\Tools\Traits\Product;

use Splash\Client\Splash;

/**
 * @abstract    Splash Test Tools - Products Variants PhpUnit Specific Features
 *
 * @author SplashSync <contact@splashsync.com>
 */
trait VariantsTrait
{

    //==============================================================================
    //      SPLASH PRODUCT VARIANTS SPECIFIC FUNCTIONS
    //==============================================================================

    /**
     * @abstract    Generate Fields Variations Attributes
     */
    public function objectVariantsProvider()
    {
        $result = array();
        
        $name   =  $this->getVariantName();
        for ($i=0; $i<2; $i++) {
            $result[]   =   array_merge($name, $this->getVariantAttributes(['VariantA','VariantB']));
        }

        return $result;
    }

    /**
     * @abstract    Generate Variations Base Name Fialds Data
     * @return      array
     */
    public function getVariantName()
    {
        //====================================================================//
        //   Verify Product Base Name
        $field   =   self::findFieldByTag($this->fields, "http://schema.org/Product", "alternateName");
        $this->assertNotEmpty($field);
        //====================================================================//
        //   Generate Random Value
        return array(
            $field->id  =>  self::fakeFieldData($field->type),
        );
    }

    /**
     * @abstract    Generate Variations Attributes
     */
    public function getVariantAttributes($attributesCodes)
    {
        //====================================================================//
        //   Load Required Fields
        $code   =   self::findFieldByTag($this->fields, "http://schema.org/Product", "VariantAttributeCode");
        $this->assertNotEmpty($code);

        $result = array();
        foreach ($attributesCodes as $attributesCode) {
            $result[] = $this->getVariantCustomAttribute($attributesCode);
        }
        return array(
            self::lists()->listName($code->id) => $result
        );
    }

    /**
     * @abstract    Generate Variations CustomAttribute
     */
    public function getVariantCustomAttribute($attributesCode)
    {
        //====================================================================//
        //   Load Required Fields
        $code   =   self::findFieldByTag($this->fields, "http://schema.org/Product", "VariantAttributeCode");
        $this->assertNotEmpty($code);
        $name   =   self::findFieldByTag($this->fields, "http://schema.org/Product", "VariantAttributeName");
        $this->assertNotEmpty($name);
        $value  =   self::findFieldByTag($this->fields, "http://schema.org/Product", "VariantAttributeValue");
        $this->assertNotEmpty($value);
        //====================================================================//
        //   Generate Random Attributes Set
        return array(
            self::lists()->fieldName($code->id)     =>      strtolower($attributesCode),
            self::lists()->fieldName($name->id)     =>      self::fakeFieldData(
                $name->type,
                null,
                array_merge_recursive($name->options, ["minLength" =>   3, "maxLength" =>   5])
            ),
            self::lists()->fieldName($value->id)     =>      self::fakeFieldData(
                $value->type,
                null,
                array_merge_recursive($value->options, ["minLength" =>   5, "maxLength" =>   10])
            ),
        );
    }

    /**
     * @abstract    Override Parent Function to Filter on Products Fields
     */
    public function objectFieldsProvider()
    {
        $fields = array();
        foreach (parent::objectFieldsProvider() as $field) {
            //====================================================================//
            // Filter Non Product Fields
            if ($field[1] != "Product") {
                continue;
            }
//            //====================================================================//
//            // DEBUG => Focus on a Specific Fields
//            if ($Field[2]->id == "image@images") {
//                continue;
//            }
            $fields[] = $field;
        }
        if (empty($fields)) {
            $this->markTestSkipped('This Server has no Product Object Type.');
        }
        return $fields;
    }
}
