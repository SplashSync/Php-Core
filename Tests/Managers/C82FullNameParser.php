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
 * Test suite for the FullName Parser
 */
class C82FullNameParser extends TestCase
{
    //==============================================================================
    // FullName Builder
    //==============================================================================

    /**
     * Test of Full Name Helper
     *
     * @dataProvider fullNameParserProvider
     */
    public function testFullNameParser(?array $source, ?string $target): void
    {
        //==============================================================================
        // Validate Encoding Full Names
        $parserEncoder = new FullNameParser();
        if (!empty($source)) {
            $parserEncoder
                ->setCompanyName($source['name'] ?? null)
                ->setFirstName($source['firstname'] ?? null)
                ->setLastName($source['lastname'] ?? null)
            ;
        }
        $this->assertSame($target, $parserEncoder->getFullName());

        //==============================================================================
        // Validate Decoding Full Names
        $parserDecoder = new FullNameParser($target);

        if (!isset($source['firstname'])) {
            $source['firstname'] = null;
        } elseif (!isset($source['lastname'])) {
            $source['lastname'] = null;
        }

        $this->assertSame($source['name'] ?? null, $parserDecoder->getCompanyName());
        if (!empty($source['firstname'] && !empty($source['lastname']))) {
            $this->assertSame($source['firstname'], $parserDecoder->getFirstName());
            $this->assertSame($source['lastname'], $parserDecoder->getLastName());
        } else {
            $this->assertNull($parserDecoder->getFirstName());
            $this->assertNull($parserDecoder->getLastName());
        }
    }

    /**
     * Generate Test Values Sets for Mass Conversion Test
     *
     * @return array<string, array<int, null|array<string, string>|string>>
     */
    public function fullNameParserProvider(): array
    {
        return array(
            "Complete FullName" => array(array(
                "name" => "Passione",
                "firstname" => "Giorno",
                "lastname" => "Giovanna",
            ), "Giovanna, Giorno - Passione"),

            "Empty FirstName" => array(array(
                "name" => "Passione",
                "firstname" => "",
                "lastname" => "Giovanna",
            ), "Passione"),

            "No FirstName" => array(array(
                "name" => "Passione",
                "lastname" => "Giovanna",
            ), "Passione"),

            "Empty LastName" => array(array(
                "name" => "Passione",
                "firstname" => "Giorno",
                "lastname" => "",
            ), "Passione"),

            "NoLastname" => array(array(
                "name" => "Passione",
                "firstname" => "Giorno",
            ), "Passione"),

            "Empty" => array(array(), null),
        );
    }
}
