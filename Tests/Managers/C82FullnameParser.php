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
use Splash\Models\Objects\FullnameTrait;

/**
 * Test suite for the Fullname Parser
 */
class C82FullnameParser extends TestCase
{
    use FullnameTrait;

    //==============================================================================
    // Fullname Builder
    //==============================================================================

    /**
     * Test of Fullname Builder
     *
     * @dataProvider fullnameBuilderProvider
     */
    public function testFullnameBuilder(?array $source, ?string $target): void
    {
        $this->buildFullName($source);
        $this->assertSame($target, $this->getFullName());
    }

    /**
     * Generate Test Values Sets for Mass Conversion Test
     *
     * @return array<string[]>
     */
    public function fullnameBuilderProvider(): array
    {
        return array(
            "CompleteFullname" => array(array(
                "name" => "Passione",
                "firstname" => "Giorno",
                "lastname" => "Giovanna",
            ), "Giovanna, Giorno - Passione"),

            "NoFirstname" => array(array(
                "name" => "Passione",
                "lastname" => "Giovanna",
            ), "Giovanna - Passione"),

            "NoLastname" => array(array(
                "name" => "Passione",
                "firstname" => "Giorno",
            ), "Giorno - Passione"),

            "Empty" => array(array(), null),

            "NoCompany" => array(array(
                "firstname" => "Giorno",
                "lastname" => "Giovanna",
            ), null),
        );
    }

    //==============================================================================
    // Fullname Decoder
    //==============================================================================

    /**
     * Test of Fullname Decoder
     *
     * @dataProvider fullnameDecoderProvider
     */
    public function testFullnameDecoder(?array $source): void
    {
        $this->buildFullName($source);
        $this->assertSame($source, $this->decodeFullName());
    }

    /**
     * Generate Test Values Sets for Mass Conversion Test
     *
     * @return array<string[]>
     */
    public function fullnameDecoderProvider(): array
    {
        return array(
            "CompleteFullname" => array(array(
                "name" => "Passione",
                "firstname" => "Giorno",
                "lastname" => "Giovanna",
            )),

            "NoFirstname" => array(array(
                "name" => "Passione",
                "firstname" => "",
                "lastname" => "Giovanna",
            )),

            "NoLastname" => array(array(
                "name" => "Passione",
                "firstname" => "Giorno",
                "lastname" => "",
            ),
        ));
    }
}
