<?php

namespace Splash\Tests\Tools\Traits;

/**
 * @abstract    Splash Test Tools - Objects PhpUnit Specific Assertions
 *
 * @author SplashSync <contact@splashsync.com>
 */
trait ObjectsAssertionsTrait
{
    
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
}
