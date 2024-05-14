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
use Splash\Models\Objects\ThirdParty\Civility;

/**
 * Models Test Suite - ThirdParty Gender Converter Verifications
 */
class C80GenderConverterTest extends TestCase
{
    //==============================================================================
    // Gender Converters
    //==============================================================================

    /**
     * Test of toSplash Gender Type Converter
     *
     * @dataProvider toSplashGenderTypesProvider
     */
    public function testToSplashMethod(string $source, ?string $target): void
    {
        $this->assertSame($target, Civility::toSplash($source));
    }

    /**
     * Test of toSplash Gender Type Converter
     *
     * @dataProvider toAppGenderTypesProvider
     */
    public function testToAppMethod(string $source, ?string $target): void
    {
        $this->assertSame(
            $target,
            Civility::toApp($source)
        );
    }

    /**
     * Generate Test Values Sets for Gender Type Converter Test
     *
     * @return array<string[]>
     */
    public function toSplashGenderTypesProvider(): array
    {
        return array(
            "WrongValue" => array("Wrong-Value", null),
            "CorrectValueMale" => array("m", Civility::MALE),
            "CorrectValueFemale" => array("f", Civility::FEMALE),
            "CorrectValueNeutral" => array("n", Civility::NEUTRAL),
        );
    }

    /**
     * Generate Test Values Sets for Gender Type Converter Test
     *
     * @return array<string[]>
     */
    public function toAppGenderTypesProvider(): array
    {
        return array(
            "WrongValue" => array("Wrong-Value", null),
            "CorrectValueMale" => array(Civility::MALE, "m"),
            "CorrectValueFemale" => array(Civility::FEMALE, "f"),
            "CorrectValueNeutral" => array(Civility::NEUTRAL, "n"),
        );
    }
}
