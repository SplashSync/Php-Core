<?php
namespace Splash\Tests\WsSpecials;

use Splash\Tests\Tools\ObjectsCase;
use Splash\Client\Splash;
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
    public function testVariantsFromModule($Sequence, $ObjectType, $Field, $ForceObjectId = null)
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
            //====================================================================//
            //   Store Current Tested Variant
            $this->CurrentVariation =   $VariationData;
            //====================================================================//
            //   Execute Set Test
            $this->coreTestSetSingleFieldFromModule($ObjectType, $Field, $ForceObjectId);
        }
    }

    /**
     * @dataProvider objectFieldsProvider
     */
    public function testVariantsFieldFromService($Sequence, $ObjectType, $Field, $ForceObjectId = null)
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
            //====================================================================//
            //   Store Current Tested Variant
            $this->CurrentVariation =   $VariationData;
            //====================================================================//
            //   Execute Set Test
            $this->coreTestSetSingleFieldFromService($ObjectType, $Field, $ForceObjectId);
        }
    }
    
    /**
     * @abstract    Override Parent Function to Add Variants Attributes
     * @abstract    Ensure Set/Write Test is Possible & Generate Fake Object Data
     *              -> This Function uses Preloaded Fields
     *              -> If Md5 provided, check Current Field was Modified
     *
     * @param       string      $ObjectType     Current Object Type
     * @param       ArrayObject $Field          Current Tested Field (ArrayObject)
     * @param       bool        $Unik           Ask for Unik Field Data
     *
     * @return      array|bool      Generated Data Block or False if not Allowed
     */
    public function prepareForTesting($ObjectType, $Field, $Unik = false)
    {
        //====================================================================//
        //   Verify Test is Required
        if (!$this->verifyTestIsAllowed($ObjectType, $Field)) {
            return false;
        }
        
        //====================================================================//
        // Generated Object Data
        $FakeData = $this->generateObjectData($ObjectType, $Field, $Unik);

        //====================================================================//
        //   Add Attributes Fields To Fields List for Verifications
        $Fields   =   Splash::object($ObjectType)->fields();
        $this->Fields[]   =   self::findFieldByTag($Fields, "http://schema.org/Product", "VariantAttributeCode");
        $this->Fields[]   =   self::findFieldByTag($Fields, "http://schema.org/Product", "VariantAttributeName");
        $this->Fields[]   =   self::findFieldByTag($Fields, "http://schema.org/Product", "VariantAttributeValue");
      
//var_dump($Field->id);
//var_dump(array_merge($FakeData, $this->CurrentVariation));

        //====================================================================//
        // Return Generated Object Data
        return array_merge($FakeData, $this->CurrentVariation);
    }
}
