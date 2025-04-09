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

namespace Splash\Core\Tests\T400Components;

use ArrayObject;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Splash\Core\Client\Splash;

/**
 * Components Test Suite - Xml Manager Verifications
 */
class T401XmlManagerTest extends TestCase
{
    /**
     * Initialize Framework before testing
     */
    protected function setUp(): void
    {
        parent::setUp();
        //====================================================================//
        // BOOT MODULE
        Splash::core();
    }

    //==============================================================================
    // XML <> ARRAYOBJECT ENCODING TESTS
    //==============================================================================

    /**
     * Test Xml Encoding from ArrayObject
     *
     * @dataProvider xmlSamplesProvider
     */
    public function testArrayObjectEncoding(array $input): void
    {
        Assert::assertNotEmpty($input);
        $input = new ArrayObject($input, ArrayObject::STD_PROP_LIST);
        //==============================================================================
        // Encode to Xml
        $encoded = Splash::xml()->objectToXml($input);
        Assert::assertNotEmpty($encoded);
        Assert::assertIsString($encoded);
        //==============================================================================
        // Decode from Xml
        $decoded = Splash::xml()->xmlToArrayObject($encoded);
        Assert::assertNotEmpty($decoded);
        Assert::assertInstanceOf(ArrayObject::class, $decoded);
        //==============================================================================
        // Verify
        $this->compareValues($input->getArrayCopy(), $decoded->getArrayCopy());
    }

    //==============================================================================
    // XML <> ARRAY ENCODING TESTS
    //==============================================================================

    /**
     * Test Xml Encoding from Array
     *
     * @dataProvider xmlSamplesProvider
     */
    public function testArrayEncoding(array $input): void
    {
        $this->assertNotEmpty($input);
        //==============================================================================
        // Encode to Xml
        $encoded = Splash::xml()->arrayToXml($input);
        Assert::assertNotEmpty($encoded);
        Assert::assertIsString($encoded);
        //==============================================================================
        // Decode from Xml
        $decoded = Splash::xml()->xmlToArray($encoded);
        Assert::assertNotEmpty($decoded);
        Assert::assertIsArray($decoded);
        //==============================================================================
        // Verify
        $this->compareValues($input, $decoded);
    }

    /**
     * Sample Xml Data to Encode / Decode
     *
     * @return array[]
     */
    public function xmlSamplesProvider(): array
    {
        return array(
            "Basic" => array(array(
                "key" => "value"
            )),
            "Complex Keys" => array(array(
                01 => "Integer 01",
                10 => "Integer 10",
                "0" => "String 0",
                "2" => "String 2",
                "With Space" => "String with Space",
                "With_Underscore" => "String with Underscore",
            )),
            "Values Types" => array(array(
                "null" => null,
                "bTrue" => true,
                "bFalse" => false,
                "iZero" => 0,
                "iOne" => 1,
                "iTwo" => 2,
                "sEmpty" => "",
                "sSimple" => "AbCdEfGh 123456789",
                "sSpecial" => "AbC @ /: \\ , ; ! § $ ~ & %",
                "sLong" => "U6C648JnVQ1Fc0bPPol52KfvbJG7vB7gzTTaEJvJ6wVx5v1WRvMVKNi
                            qc2E5UCXdVNC1gS0Ntc3l68EUKKAarYuXAuPrbnqUJAVaiOQjfGMNOl
                            hI3slHwIGbXoCOSdyZ5VT5Ty0eOqJ7DXLDjD7HUhEcuneaWlG97AFN0
                            Rgw9N42OH7M2oi7jKUsFjXOz9BPoPm3IDXooNzjuZJcAfBmxiLBmsZt
                            BjdFI3SHaCagUXibWuQSpdCjbGCfPGI5oy6A2sS3FiVVw2xjGSNMpaQ
                            NmV9HGMyYje7aytb2x182TBuHj04hBkmGwqkp2ebvCQbliAds2SxBnF
                            2xnVpiyolU0dr2zspuz9QXTCB6NbhUBIIEGzPJVWv0SbaJGGgyiEwyb
                            XuHe6LHTjoSXvpBLrMk1z3DZtl5CwoB4ZwmuNCH1V2DN5fl3Oe4fzic
                            wvieo2VgkuJkpo6AN75ZqC5QCmQg0569zRKxfxgMqwNftMBV2LpTTQE
                            CJxW3AcBvV0JZ4UDCfnGrxgrI5Rz9l0nRaCbpFAh6qM4Oajugi7j2Hp",
            )),
            "Arrays" => array(array(
                "list" => array(
                    0 => "Value 0",
                    "key 1" => "Value 1",
                    "key 2" => "Value 2",
                    "sublist" => array(
                        1 => "Value 0",
                        "key 1" => "Value 1",
                        "key 2" => "Value 2",
                    )
                )
            )),
        );
    }

    /**
     * Compare Encoded / Decoded Values with Original
     */
    private function compareValues(array $input, array $output): void
    {
        //==============================================================================
        // Only Compare Values
        $inputValues = array_values($input);
        $outputValues = array_values($output);
        //==============================================================================
        // Walk on Values
        foreach ($inputValues as $index => $value) {
            //==============================================================================
            // If Array or ArrayObject
            if (is_iterable($value)) {
                Assert::assertIsIterable($outputValues[$index]);
                $this->compareValues((array)$value, (array) $outputValues[$index]);

                continue;
            }
            //==============================================================================
            // If Simple Value
            if ($value != $outputValues[$index]) {
                Assert::assertEquals($value, $outputValues[$index]);
            }
        }
    }
}
