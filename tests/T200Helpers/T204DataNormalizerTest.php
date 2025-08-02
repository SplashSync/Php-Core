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

use ArrayObject;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Splash\Core\Helpers\DataNormalizer;

/**
 * Helpers Test Suite - Data Normalizer Verifications
 */
class T204DataNormalizerTest extends TestCase
{
    /**
     * Test Normalizing Blocks of Data / Fields.
     *
     * @dataProvider normalizeDataProvider
     *
     * @group ServiceLevel
     */
    public function testNormalizeFunction(array|ArrayObject $inFields, array $outFields): void
    {
        Assert::assertNotEquals(serialize($outFields), serialize($inFields));
        //====================================================================//
        // Perform Test
        $result = DataNormalizer::normalize($inFields);
        //====================================================================//
        // Check result
        Assert::assertEquals(serialize($outFields), serialize($result));
    }

    /**
     * Formater Normalizer Data Provider.
     *
     * @return array
     */
    public static function normalizeDataProvider(): array
    {
        return array(
            array(
                new ArrayObject(array(
                    "Item1" => array("_Item1" => "XXX", "_Item2" => true, "_Item3" => false),
                    "Item2" => new ArrayObject(array("_Item1" => "XXX", "_Item2" => "XXX", "_Item3" => 422)),
                    "Item3" => array("_Item1" => "XXX", "_Item2" => new ArrayObject(array("XXX")), "_Item3" => "XXX"),
                )),
                array(
                    "Item1" => array("_Item1" => "XXX", "_Item2" => "1", "_Item3" => "0"),
                    "Item2" => array("_Item1" => "XXX", "_Item2" => "XXX", "_Item3" => "422"),
                    "Item3" => array("_Item1" => "XXX", "_Item2" => array("XXX"), "_Item3" => "XXX"),
                ),
            ),
            array(
                array(
                    "A" => array("_Item1" => true, "_Item2" => new ArrayObject(array(true, false)), "_Item3" => "XXX"),
                    "B" => array("_Item1" => "XXX", "_Item2" => "XXX", "_Item3" => "XXX"),
                    "C" => array("_Item1" => 2.33, "_Item2" => "XXX", "_Item3" => "XXX"),
                    "Z" => array("_Item1" => "XXX", "_Item2" => "XXX", "_Item3" => "XXX"),
                ),
                array(
                    "A" => array("_Item1" => "1", "_Item2" => array("1", "0"), "_Item3" => "XXX"),
                    "B" => array("_Item1" => "XXX", "_Item2" => "XXX", "_Item3" => "XXX"),
                    "C" => array("_Item1" => "2.33", "_Item2" => "XXX", "_Item3" => "XXX"),
                    "Z" => array("_Item1" => "XXX", "_Item2" => "XXX", "_Item3" => "XXX"),
                ),
            ),
        );
    }

    /**
     * Test Sorting Arrays of Data / Fields.
     *
     * @dataProvider sortArrayProvider
     *
     * @group ServiceLevel
     */
    public function testSortFunction(array $inFields, array $outFields): void
    {
        Assert::assertNotEquals(serialize($outFields), serialize($inFields));
        //====================================================================//
        // Perform Test
        Assert::assertEquals($inFields, DataNormalizer::sort($inFields));
        //====================================================================//
        // Check result
        Assert::assertEquals(serialize($outFields), serialize($inFields));
    }

    /**
     * Formater Sort Function Data Provider.
     *
     * @return array
     */
    public static function sortArrayProvider(): array
    {
        return array(
            array(
                array(
                    "Item1" => array("_Item3" => "XXX", "_Item2" => "XXX", "_Item1" => "XXX"),
                    "Item3" => array("_Item1" => "XXX", "_Item2" => "XXX", "_Item3" => "XXX"),
                    "Item2" => array("_Item2" => "XXX", "_Item3" => "XXX", "_Item1" => "XXX"),
                ),
                array(
                    "Item1" => array("_Item1" => "XXX", "_Item2" => "XXX", "_Item3" => "XXX"),
                    "Item2" => array("_Item1" => "XXX", "_Item2" => "XXX", "_Item3" => "XXX"),
                    "Item3" => array("_Item1" => "XXX", "_Item2" => "XXX", "_Item3" => "XXX"),
                ),
            ),
            array(
                array(
                    "Z" => array("_Item2" => "XXX", "_Item3" => "XXX", "_Item1" => "XXX"),
                    "A" => array("_Item3" => "XXX", "_Item2" => "XXX", "_Item1" => "XXX"),
                    "C" => array("_Item1" => "XXX", "_Item2" => "XXX", "_Item3" => "XXX"),
                    "B" => array("_Item2" => "XXX", "_Item3" => "XXX", "_Item1" => "XXX"),
                ),
                array(
                    "A" => array("_Item1" => "XXX", "_Item2" => "XXX", "_Item3" => "XXX"),
                    "B" => array("_Item1" => "XXX", "_Item2" => "XXX", "_Item3" => "XXX"),
                    "C" => array("_Item1" => "XXX", "_Item2" => "XXX", "_Item3" => "XXX"),
                    "Z" => array("_Item1" => "XXX", "_Item2" => "XXX", "_Item3" => "XXX"),
                ),
            ),
        );
    }
}
