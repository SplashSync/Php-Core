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
use Splash\Models\Helpers\FullNameParser;

/**
 * Test suite for the Fullname Parser
 */
class C82FullnameParser extends TestCase
{
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
        $parser = new FullNameParser();
        if (!empty($source)) {
            $parser->setCompanyName($source['name'] ?? null)
                ->setFirstName($source['firstname'] ?? null)
                ->setLastName($source['lastname'] ?? null);
        }
        $this->assertSame($target, $parser->getFullName());
    }

    /**
     * Generate Test Values Sets for Mass Conversion Test
     *
     * @return array<string, array<int, null|array<string, string>|string>>
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

            "Empty" => array(array(), ''),

            "NoCompany" => array(array(
                "firstname" => "Giorno",
                "lastname" => "Giovanna",
            ), 'Giovanna, Giorno'),
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
    public function testFullnameDecoder(?string $source, ?array $target): void
    {
        $parser = new FullNameParser($source);

        $this->assertSame($parser->getCompanyName(), $target['name'] ?? null);

        if (!isset($target['firstname']) && isset($target['lastname'])) {
            $this->assertSame($parser->getFirstName(), $target['lastname']);
        } elseif (!isset($target['lastname']) && isset($target['firstname'])) {
            $this->assertSame($parser->getFirstName(), $target['firstname']);
        } else {
            $this->assertSame($parser->getFirstName(), $target['firstname'] ?? null);
            $this->assertSame($parser->getLastName(), $target['lastname'] ?? null);
        }
    }

    /**
     * Generate Test Values Sets for Mass Conversion Test
     *
     * @return array<string, array<int, array<string, string>>>.
     */
    public function fullnameDecoderProvider(): array
    {
        return array(
            "CompleteFullname" => array('Giovanna, Giorno - Passione',
                array(
                    "name" => "Passione",
                    "firstname" => "Giorno",
                    "lastname" => "Giovanna",
                )
            ),

            "NoFirstname" => array('Giovanna - Passione',
                array(
                    "name" => "Passione",
                    "lastname" => "Giovanna",
                )
            ),

            "NoLastname" => array('Giorno - Passione',
                array(
                    "name" => "Passione",
                    "firstname" => "Giorno",
                )
            ),
        );
    }
}
