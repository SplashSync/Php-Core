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

namespace Splash\Tests\Managers;

use PHPUnit\Framework\TestCase;
use Splash\Components\UnitConverter as Unit;

/**
 * Components Test Suite - Unit Converter Verifications
 */
class C60UnitsConverterTest extends TestCase
{
    use \Splash\Models\Objects\UnitsHelperTrait;

    //==============================================================================
    // MASS UNIT CONVERTER FUNCTIONS
    //==============================================================================

    /**
     * Test of Mass Unit Converter
     *
     * @param float $source
     * @param float $factor
     * @param float $target
     *
     * @dataProvider massValuesProvider
     *
     * @return void
     */
    public function testMassConverter($source, $factor, $target)
    {
        //====================================================================//
        // Convert Source to Base Unit
        $normalized = self::units()->normalizeWeight($source, $factor);
        $this->assertSame(round($target, 6), round($normalized, 6));

        //====================================================================//
        // Revert Normalized to Original Unit
        $reverse = self::units()->convertWeight($normalized, $factor);
        $this->assertSame(round($source, 9), round($reverse, 9));
    }

    /**
     * Generate Test Values Sets for Mass Convertion Test
     *
     * @return array
     */
    public function massValuesProvider()
    {
        return array(
            array(123456, Unit::MASS_MICROGRAM, 0.000123456),
            array(123456, Unit::MASS_MILLIGRAM, 0.123456),
            array(1, Unit::MASS_GRAM, 0.001),
            array(123456, Unit::MASS_GRAM, 123.456),
            array(1,        Unit::MASS_OUNCE, 0.028349523),
            array(123456, Unit::MASS_OUNCE, 3499.918721917),
            array(123456, Unit::MASS_KG, 123456),
            array(123456, Unit::MASS_TONNE, 123456000),
            array(123456, Unit::MASS_LIVRE, 55998.699631),
        );
    }

    //==============================================================================
    // LENGTH UNIT CONVERTER FUNCTIONS
    //==============================================================================

    /**
     * Test of Length Unit Converter
     *
     * @param float $source
     * @param float $factor
     * @param float $target
     *
     * @dataProvider lengthValuesProvider
     *
     * @return void
     */
    public function testLengthConverter($source, $factor, $target)
    {
        //====================================================================//
        // Convert Source to Base Unit
        $normalized = self::units()->normalizeLength($source, $factor);
        $this->assertSame(round($target, 6), round($normalized, 6));

        //====================================================================//
        // Revert Normalized to Original Unit
        $reverse = self::units()->convertLength($normalized, $factor);
        $this->assertSame(round($source, 9), round($reverse, 9));
    }

    /**
     * Generate Test Values Sets for Lenght Convertion Test
     *
     * @return array
     */
    public function lengthValuesProvider()
    {
        return array(
            array(1,        Unit::LENGTH_MILIMETER, 0.001),
            array(1,        Unit::LENGTH_CENTIMETER, 0.01),
            array(1,        Unit::LENGTH_DECIMETER, 0.1),
            array(1,        Unit::LENGTH_M, 1),
            array(1,        Unit::LENGTH_KM, 1000),
            array(1,        Unit::LENGTH_FOOT, 0.304800),
            array(1,        Unit::LENGTH_INCH, 0.025400),
            array(1,        Unit::LENGTH_YARD, 0.914400),
            array(123456,   Unit::LENGTH_MILIMETER, 123.456),
            array(123456,   Unit::LENGTH_CENTIMETER, 1234.56),
            array(123456,   Unit::LENGTH_DECIMETER, 12345.6),
            array(123456,   Unit::LENGTH_KM, 123456000),
            array(123456,   Unit::LENGTH_FOOT, 37629.388800),
            array(123456,   Unit::LENGTH_INCH, 3135.782400),
            array(123456,   Unit::LENGTH_YARD, 112888.166400),
        );
    }

    //==============================================================================
    // SURFACE UNIT CONVERTER FUNCTIONS
    //==============================================================================

    /**
     * Test of Surface Unit Converter
     *
     * @param float $source
     * @param float $factor
     * @param float $target
     *
     * @dataProvider surfaceValuesProvider
     *
     * @return void
     */
    public function testSurfaceConverter($source, $factor, $target)
    {
        //====================================================================//
        // Convert Source to Base Unit
        $normalized = self::units()->normalizeSurface($source, $factor);
        $this->assertSame(round($target, 6), round($normalized, 6));

        //====================================================================//
        // Revert Normalized to Original Unit
        $reverse = self::units()->convertSurface($normalized, $factor);
        $this->assertSame(round($source, 9), round($reverse, 9));
    }

    /**
     * Generate Test Values Sets for Surface Convertion Test
     *
     * @return array
     */
    public function surfaceValuesProvider()
    {
        return array(
            array(1,        Unit::AREA_MM2, 0.000001),
            array(1,        Unit::AREA_CM2, 0.0001),
            array(1,        Unit::AREA_M2, 1),
            array(1,        Unit::AREA_KM2, 1000000),
            array(1,        Unit::AREA_FOOT2, 0.092903),
            array(1,        Unit::AREA_INCH2, 0.000645),
            array(123456,   Unit::AREA_MM2, 0.123456),
            array(123456,   Unit::AREA_CM2, 12.3456),
            array(123456,   Unit::AREA_M2, 123456),
            array(123456,   Unit::AREA_KM2, 123456000000),
            array(123456,   Unit::AREA_FOOT2, 11469.437706),
            array(123456,   Unit::AREA_INCH2, 79.648873),
        );
    }

    //==============================================================================
    // VOLUME UNIT CONVERTER FUNCTIONS
    //==============================================================================

    /**
     * Test of Volume Unit Converter
     *
     * @param float $source
     * @param float $factor
     * @param float $target
     *
     * @dataProvider volumeValuesProvider
     *
     * @return void
     */
    public function testVolumeConverter($source, $factor, $target)
    {
        //====================================================================//
        // Convert Source to Base Unit
        $normalized = self::units()->normalizeSurface($source, $factor);
        $this->assertSame(round($target, 6), round($normalized, 6));

        //====================================================================//
        // Revert Normalized to Original Unit
        $reverse = self::units()->convertSurface($normalized, $factor);
        $this->assertSame(round($source, 9), round($reverse, 9));
    }

    /**
     * Generate Test Values Sets for Volume Convertion Test
     *
     * @return array
     */
    public function volumeValuesProvider()
    {
        return array(
            array(1,        Unit::VOLUME_MM3, 0.000000001),
            array(1,        Unit::VOLUME_CM3, 0.000001),
            array(1,        Unit::VOLUME_M3, 1),
            array(1,        Unit::VOLUME_KM3, 1000000000),
            array(1,        Unit::VOLUME_FOOT3, 0.028317),
            array(1,        Unit::VOLUME_INCH3, 0.000016),
            array(1,        Unit::VOLUME_LITER, 0.001),
            array(1,        Unit::VOLUME_OUNCE3, 2.9574e-5),
            array(1,        Unit::VOLUME_GALON, 0.00378541),
            array(123456,   Unit::VOLUME_MM3, 0.000123456),
            array(123456,   Unit::VOLUME_CM3, 0.123456),
            array(123456,   Unit::VOLUME_M3, 123456),
            array(12.3,     Unit::VOLUME_KM3, 12300000000),
            array(123456,   Unit::VOLUME_FOOT3, 3495.884613),
            array(123456,   Unit::VOLUME_INCH3, 2.023081),
            array(123456,   Unit::VOLUME_LITER, 123.456),
            array(123456,   Unit::VOLUME_OUNCE3, 3.651028),
            array(123456,   Unit::VOLUME_GALON, 467.331577),
        );
    }
}
