<?php
namespace Splash\Tests\WsSpecials;

use Splash\Tests\WsObjects\O06SetTest;
use Splash\Client\Splash;

/**
 * @abstract    Product Special Test Suite - Products Fields Verifications
 *
 * @author SplashSync <contact@splashsync.com>
 */
class S00ProductTest extends O06SetTest
{
    use \Splash\Tests\Tools\Traits\Product\AssertionsTrait;
    use \Splash\Tests\Tools\Traits\Product\VariantsTrait;
    use \Splash\Models\Objects\ListsTrait;

    /**
     * @var array
     */
    protected $Fields   = array();

    /**
     * @dataProvider objectTypesProvider
     */
    public function testFieldsDefinition($Sequence, $ObjectType)
    {
        //====================================================================//
        //   TEST INIT
        //====================================================================//
        if (!$this->assertIsProductType($ObjectType)) {
            return;
        }
        $this->loadLocalTestSequence($Sequence);

        //====================================================================//
        //   Load Fields
        $this->Fields   =   Splash::object($ObjectType)->fields();
        $this->assertNotEmpty($this->Fields, "Product Fields List is Empty!");
        $this->assertInternalType("array", $this->Fields, "Product Fields List is not an Array!");

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
     */
    public function testSetSingleFieldFromModule($Sequence, $ObjectType, $Field, $ForceObjectId = null)
    {
        //====================================================================//
        //   TEST INIT
        //====================================================================//
        if (!$this->assertIsProductType($ObjectType)) {
            return;
        }
        $this->loadLocalTestSequence($Sequence);

        //====================================================================//
        //   Load Fields
        $this->Fields   =   Splash::object($ObjectType)->fields();
        $this->assertNotEmpty($this->Fields, "Product Fields List is Empty!");

        //====================================================================//
        //   Verify Product Variants Are Defined
        $VariantCode    =   self::findFieldByTag($this->Fields, "http://schema.org/Product", "VariantAttributeCode");
        if (!$VariantCode) {
            return;
        }

        foreach ($this->objectVariantsProvider() as $VariationData) {
            $this->CurrentVariation =   $VariationData;
            parent::testSetSingleFieldFromModule($Sequence, $ObjectType, $Field, $ForceObjectId);
        }
    }

    /**
     * @dataProvider objectFieldsProvider
     */
    public function testSetSingleFieldFromService($Sequence, $ObjectType, $Field, $ForceObjectId = null)
    {
        //====================================================================//
        //   TEST INIT
        //====================================================================//
        if (!$this->assertIsProductType($ObjectType)) {
            return;
        }
        $this->loadLocalTestSequence($Sequence);

        //====================================================================//
        //   Load Fields
        $this->Fields   =   Splash::object($ObjectType)->fields();
        $this->assertNotEmpty($this->Fields, "Product Fields List is Empty!");

        //====================================================================//
        //   Verify Product Variants Are Defined
        $VariantCode    =   self::findFieldByTag($this->Fields, "http://schema.org/Product", "VariantAttributeCode");
        if (!$VariantCode) {
            return;
        }

        foreach ($this->objectVariantsProvider() as $VariationData) {
            $this->CurrentVariation =   $VariationData;
            parent::testSetSingleFieldFromService($Sequence, $ObjectType, $Field, $ForceObjectId);
        }
    }
}
