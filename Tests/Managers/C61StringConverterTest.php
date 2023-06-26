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
use Splash\Components\StringConverter;

/**
 * Components Test Suite - String Converter Verifications
 */
class C61StringConverterTest extends TestCase
{
    //==============================================================================
    // Canonical String
    //==============================================================================

    /**
     * Test of Canonical String Converter
     *
     * @dataProvider canonicalStringProvider
     */
    public function testCanonicalString(string $source, string $target): void
    {
        $this->assertSame($target, StringConverter::canonicalString($source));
    }

    /**
     * Generate Test Values Sets for Mass Conversion Test
     *
     * @return array<string[]>
     */
    public function canonicalStringProvider(): array
    {
        return array(
            "StrToLower" => array("ABCDabcd", "abcdabcd"),
            "Numbers&Spaces" => array("123 123", "123_123"),
            "AccentChars" => array(
                "á|â|à|å|ä ð|é|ê|è|ë í|î|ì|ï ó|ô|ò|ø|õ|ö ú|û|ù|ü æ ç ß",
                "a_a_a_a_a_d_e_e_e_e_i_i_i_i_o_o_o_o_o_o_u_u_u_u_ae_c_ss"
            ),
            "SpecialChars" => array(
                "& / \\ < > & @ ^ $ , ; : ! .",
                "___________________________"
            ),
            "MoneyChars" => array(
                " £ € ",
                "_gbp_eur_"
            ),
        );
    }
}
