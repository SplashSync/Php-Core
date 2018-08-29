<?php

namespace Splash\Tests\Tools\Traits;

use ArrayObject;

use Splash\Tests\Tools\Traits\ObjectsFieldsTrait;
 
/**
 * @abstract    Splash Test Tools - Objects PhpUnit Specific Assertions
 *
 * @author SplashSync <contact@splashsync.com>
 */
trait ObjectsAssertionsTrait
{
    /**
     * @var array
     */
    protected $Fields;
    
    //==============================================================================
    //      SPLASH ASSERTIONS FUNCTIONS
    //==============================================================================
    
    /**
     * @abstract        Verify if Data is present in Array and in right Internal Format
     *
     * @param mixed     $Data           Tested Array
     * @param mixed     $Key            Tested Array Key
     * @param mixed     $Type           Expected Data Type
     * @param string    $Comment
     */
    public function assertArrayInternalType($Data, $Key, $Type, $Comment)
    {
        $this->assertArrayHasKey($Key, $Data, $Comment . " => Key '" . $Key . "' not defined");
        $this->assertNotEmpty($Data[$Key], $Comment . " => Key '" . $Key . "' is Empty");
        $this->assertInternalType($Type, $Data[$Key], $Comment . " => Key '" . $Key . "' is of Expected Internal Type");
    }
    
    /**
     * @abstract        Verify if Data is present in Array and in right Internal Format
     *
     * @param mixed     $Data           Tested Array
     * @param mixed     $Key            Tested Array Key
     * @param mixed     $Type           Expected Data Type
     * @param string    $Comment
     */
    public function assertArrayInstanceOf($Data, $Key, $Type, $Comment)
    {
        $this->assertArrayHasKey($Key, $Data, $Comment . " => Key '" . $Key . "' not defined");
        $this->assertNotEmpty($Data[$Key], $Comment . " => Key '" . $Key . "' is Empty");
        $this->assertInstanceOf($Type, $Data[$Key], $Comment . " => Key '" . $Key . "' is of Expected Internal Type");
    }
    
    /**
     * @abstract        Verify if Data is a valid Splash Data Block Bool Value
     *
     * @param mixed     $Data
     * @param string    $Comment
     */
    public function assertIsSplashBool($Data, $Comment)
    {
        $Test = is_bool($Data) || ($Data === "0") || ($Data === "1");
        $this->assertTrue($Test, $Comment);
    }
    
    /**
     * @abstract        Verify if Data is present in Array and is Splash Bool
     *
     * @param mixed     $Data           Tested Array
     * @param mixed     $Key            Tested Array Key
     * @param string    $Comment
     */
    public function assertArraySplashBool($Data, $Key, $Comment)
    {
        $this->assertArrayHasKey($Key, $Data, $Comment . " => Key '" . $Key . "' not defined");
        $this->assertIsSplashBool($Data[$Key], $Comment . " => Key '" . $Key . "' is of Expected Internal Type");
    }

    /**
     * @abstract        Verify if Data is a valid Splash Field Data Value
     *
     * @param mixed     $Data
     * @param string    $Type
     * @param string    $Comment
     */
    public function assertIsValidSplashFieldData($Data, $Type, $Comment)
    {
        //====================================================================//
        // Verify Type is Valid
        $ClassName = self::isValidType($Type);
        $this->assertNotEmpty($ClassName, "Field Type '" . $Type . "' is not a Valid Splash Field Type." . $Comment);
    
        //====================================================================//
        // Verify Data is Valid
        $this->assertTrue(
            $ClassName::validate($Data),
            "Data is not a Valid Splash '" . $Type . "'. (" . print_r($Data, true) . ")" . $Comment
        );
    }
    
    /**
     * @abstract    Load Object Field from List
     * @param       string      $ItemType           Field Microdata Type Url
     * @param       string      $ItemProp           Field Microdata Property Name
     * @return      ArrayObject
     */
    private function loadObjectFieldByTag($ItemType, $ItemProp)
    {
        //====================================================================//
        //   Ensure Fields List is Loaded
        $this->assertNotEmpty($this->Fields, "Objects Fields List is Empty! Did you load it?");
        //====================================================================//
        //   Touch this Field
        $Field       =   ObjectsFieldsTrait::findFieldByTag($this->Fields, $ItemType, $ItemProp);
        return $Field;
    }
    
    /**
     * @abstract    Build Test Result Comment
     * @param       string      $ItemType           Field Microdata Type Url
     * @param       string      $ItemProp           Field Microdata Property Name
     * @param       string      $TestComment
     * @param       string      $Comment
     * @return      string
     */
    private static function buildResult($ItemType, $ItemProp, $TestComment, $Comment = null)
    {
        return $Comment . " (" . $ItemType . ":" . $ItemProp . ") " . $TestComment;
    }
    
    /**
     * @abstract    Verify Object Field is Defined
     * @param       string      $ItemType           Field Microdata Type Url
     * @param       string      $ItemProp           Field Microdata Property Name
     * @param       string      $Comment
     * @return void
     */
    public function assertFieldIsDefined($ItemType, $ItemProp, $Comment = null)
    {
        //====================================================================//
        //   Touch this Field
        $Field       =   $this->loadObjectFieldByTag($ItemType, $ItemProp);
        //====================================================================//
        //   Verify this Field
        $this->assertNotEmpty(
            $Field,
            self::buildResult($ItemType, $ItemProp, " must be defined", $Comment)
        );
    }
    
    /**
     * @abstract    Verify Object Field is in Allowed Formats
     * @param       string      $ItemType           Field Microdata Type Url
     * @param       string      $ItemProp           Field Microdata Property Name
     * @param       array       $Formats            Array of Allowed Splash Field Formats
     * @param       string      $Comment
     * @return void
     */
    public function assertFieldHasFormat($ItemType, $ItemProp, $Formats, $Comment = null)
    {
        //====================================================================//
        //   Touch this Field
        $Field       =   $this->loadObjectFieldByTag($ItemType, $ItemProp);
        if (!$Field) {
            return;
        }
        //====================================================================//
        //   Verify this Field
        $this->assertTrue(
            in_array($Field->type, $Formats),
            self::buildResult($ItemType, $ItemProp, " must be a " . implode("|", $Formats), $Comment)
        );
        $this->assertTrue(
            $Field->read,
            "Product Short Description must be readable"
        );
    }
    
    /**
     * @abstract    Verify Object Field is Readable
     * @param       string      $ItemType           Field Microdata Type Url
     * @param       string      $ItemProp           Field Microdata Property Name
     * @param       string      $Comment
     * @return void
     */
    public function assertFieldIsRead($ItemType, $ItemProp, $Comment = null)
    {
        //====================================================================//
        //   Touch this Field
        $Field       =   $this->loadObjectFieldByTag($ItemType, $ItemProp);
        if (!$Field) {
            return;
        }
        //====================================================================//
        //   Verify this Field
        $this->assertTrue(
            $Field->read,
            self::buildResult($ItemType, $ItemProp, " must be readable.", $Comment)
        );
    }

    /**
     * @abstract    Verify Object Field is Writeable
     * @param       string      $ItemType           Field Microdata Type Url
     * @param       string      $ItemProp           Field Microdata Property Name
     * @param       string      $Comment
     * @return void
     */
    public function assertFieldIsWrite($ItemType, $ItemProp, $Comment = null)
    {
        //====================================================================//
        //   Touch this Field
        $Field       =   $this->loadObjectFieldByTag($ItemType, $ItemProp);
        if (!$Field) {
            return;
        }
        //====================================================================//
        //   Verify this Field
        $this->assertTrue(
            $Field->write,
            self::buildResult($ItemType, $ItemProp, " must be writeable.", $Comment)
        );
    }
    
    /**
     * @abstract    Verify Object Field is NOT Writeable
     * @param       string      $ItemType           Field Microdata Type Url
     * @param       string      $ItemProp           Field Microdata Property Name
     * @param       string      $Comment
     * @return void
     */
    public function assertFieldNotWrite($ItemType, $ItemProp, $Comment = null)
    {
        //====================================================================//
        //   Touch this Field
        $Field       =   $this->loadObjectFieldByTag($ItemType, $ItemProp);
        if (!$Field) {
            return;
        }
        //====================================================================//
        //   Verify this Field
        $this->assertFalse(
            $Field->write,
            self::buildResult($ItemType, $ItemProp, " must be read-only.", $Comment)
        );
    }
    
    /**
     * @abstract    Verify Object Field is Required
     * @param       string      $ItemType           Field Microdata Type Url
     * @param       string      $ItemProp           Field Microdata Property Name
     * @param       string      $Comment
     * @return void
     */
    public function assertFieldIsRequired($ItemType, $ItemProp, $Comment = null)
    {
        //====================================================================//
        //   Touch this Field
        $Field       =   $this->loadObjectFieldByTag($ItemType, $ItemProp);
        if (!$Field) {
            return;
        }
        //====================================================================//
        //   Verify this Field
        $this->assertTrue(
            $Field->required,
            self::buildResult($ItemType, $ItemProp, " must be required.", $Comment)
        );
    }
    
    /**
     * @abstract    Verify Object Field is NOT Required
     * @param       string      $ItemType           Field Microdata Type Url
     * @param       string      $ItemProp           Field Microdata Property Name
     * @param       string      $Comment
     * @return void
     */
    public function assertFieldNotRequired($ItemType, $ItemProp, $Comment = null)
    {
        //====================================================================//
        //   Touch this Field
        $Field       =   $this->loadObjectFieldByTag($ItemType, $ItemProp);
        if (!$Field) {
            return;
        }
        //====================================================================//
        //   Verify this Field
        $this->assertFalse(
            $Field->required,
            self::buildResult($ItemType, $ItemProp, " must not be Required.", $Comment)
        );
    }

    /**
     * @abstract    Verify Object Field is In List
     * @param       string      $ItemType           Field Microdata Type Url
     * @param       string      $ItemProp           Field Microdata Property Name
     * @param       string      $Comment
     * @return void
     */
    public function assertFieldIsInList($ItemType, $ItemProp, $Comment = null)
    {
        //====================================================================//
        //   Touch this Field
        $Field       =   $this->loadObjectFieldByTag($ItemType, $ItemProp);
        if (!$Field) {
            return;
        }
        //====================================================================//
        //   Verify this Field
        $this->assertTrue(
            $Field->inlist,
            $Comment . " " . $ItemType . ":" . $ItemProp . " must be readable."
        );
    }
}
