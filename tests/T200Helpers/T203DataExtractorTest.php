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

namespace Splash\Core\Tests\T200Helpers;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Splash\Core\Helpers\DataExtractor;

/**
 * Helpers Test Suite - Data Extractor Verifications
 */
class T203DataExtractorTest extends TestCase
{
    const SAMPLE_DATA = array(
        "SimpleField1" => "SimpleValue1",
        "SimpleField2" => "SimpleValue2",
        "SimpleField3" => "SimpleValue3",
        "list1" => array(
            "item-1" => array("fieldA" => "1A", "fieldB" => "1B"),
            "item-2" => array("fieldA" => "2A", "fieldB" => "2B"),
            "item-3" => array("fieldA" => "3A", "fieldB" => "3B"),
        ),
        "list2" => array(
            "item-1" => array("fieldA" => "1A", "fieldB" => "1B"),
            "item-2" => array("fieldA" => "2A", "fieldB" => "2B"),
            "item-3" => array("fieldA" => "3A", "fieldB" => "3B"),
        ),
        "list3" => array(
            "item-1" => array("fieldB" => "1B"),
            "item-2" => array("fieldA" => "2A"),
            "item-3" => array("fieldA" => "3A", "fieldB" => "3B"),
        ),
    );

    //==============================================================================
    // Canonical String
    //==============================================================================

    /**
     * Test of Data Filtering Features
     *
     * @dataProvider filteredObjectDataProvider
     */
    public function testDataFiltering(array $fieldIds, array $response): void
    {
        //==============================================================================
        // Extract Field Data
        $filteredData = DataExtractor::filterData(self::SAMPLE_DATA, $fieldIds);
        Assert::assertIsArray($filteredData);
        //==============================================================================
        // Validate Field Data
        if (!empty($response)) {
            Assert::assertNotEmpty($filteredData);
        }
        Assert::assertEquals($response, $filteredData);
    }

    /**
     * Test of Data Extraction Features
     */
    public function testDataExtractor(): void
    {
        //==============================================================================
        // Extract Simple Field Data
        Assert::assertEquals(
            "SimpleValue1",
            DataExtractor::extractField(self::SAMPLE_DATA, "SimpleField1")
        );
        Assert::assertEquals(
            "SimpleValue2",
            DataExtractor::extractField(self::SAMPLE_DATA, "SimpleField2")
        );
        //==============================================================================
        // Extract List Field Data
        Assert::assertEquals(
            array(
                0 => '1A',
                1 => '2A',
                2 => '3A'
            ),
            DataExtractor::extractField(self::SAMPLE_DATA, "fieldA@list1")
        );
        Assert::assertEquals(
            array(
                0 => '1B',
                1 => null,
                2 => '3B'
            ),
            DataExtractor::extractField(self::SAMPLE_DATA, "fieldB@list3")
        );
    }

    /**
     * Generate Exemples of Objects Data to Filter
     *
     * @return array<string, array>
     */
    public function filteredObjectDataProvider(): array
    {
        return array(
            "Empty" => array(
                "fieldIds" => array(),
                "response" => array()
            ),
            "Invalid" => array(
                "fieldIds" => array("WrongFieldId"),
                "response" => array()
            ),
            "Invalid List" => array(
                "fieldIds" => array("WrongFieldId@list"),
                "response" => array(
                    "list" => array()
                )
            ),
            "Simple1" => array(
                "fieldIds" => array("SimpleField1"),
                "response" => array("SimpleField1" => "SimpleValue1")
            ),
            "Simple3" => array(
                "fieldIds" => array("SimpleField3"),
                "response" => array("SimpleField3" => "SimpleValue3")
            ),
            "Multiple" => array(
                "fieldIds" => array("SimpleField2", "SimpleField3"),
                "response" => array(
                    "SimpleField2" => "SimpleValue2",
                    "SimpleField3" => "SimpleValue3"
                )
            ),
            "Simple List" => array(
                "fieldIds" => array("fieldA@list1"),
                "response" => array(
                    "list1" => array(
                        0 => array("fieldA" => "1A"),
                        1 => array("fieldA" => "2A"),
                        2 => array("fieldA" => "3A"),
                    ),
                )
            ),
            "Multi List" => array(
                "fieldIds" => array("fieldA@list1", "fieldB@list1"),
                "response" => array(
                    "list1" => array(
                        0 => array("fieldA" => "1A", "fieldB" => "1B"),
                        1 => array("fieldA" => "2A", "fieldB" => "2B"),
                        2 => array("fieldA" => "3A", "fieldB" => "3B"),
                    ),
                )
            ),
            "Partial List" => array(
                "fieldIds" => array("fieldA@list3"),
                "response" => array(
                    "list3" => array(
                        0 => array(),
                        1 => array("fieldA" => "2A"),
                        2 => array("fieldA" => "3A"),
                    ),
                )
            ),
            "Complex" => array(
                "fieldIds" => array("SimpleField1", "fieldA@list2", "SimpleField3",  "fieldB@list2"),
                "response" => array(
                    "SimpleField1" => "SimpleValue1",
                    "SimpleField3" => "SimpleValue3",
                    "list2" => array(
                        0 => array("fieldA" => "1A", "fieldB" => "1B"),
                        1 => array("fieldA" => "2A", "fieldB" => "2B"),
                        2 => array("fieldA" => "3A", "fieldB" => "3B"),
                    ),
                )
            ),
        );
    }
}
