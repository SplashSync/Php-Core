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
     * @dataProvider fullnameParserProvider
     */
    public function testFullnameParser(?array $source, ?string $target): void
    {
        $parserEncoder = new FullNameParser();
        if (!empty($source)) {
            $parserEncoder
                ->setCompanyName($source['name'] ?? null)
                ->setFirstName($source['firstname'] ?? null)
                ->setLastName($source['lastname'] ?? null)
            ;
        }
        $this->assertSame($target, $parserEncoder->getFullName());

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
    public function fullnameParserProvider(): array
    {
        return array(
            "CompleteFullname" => array(array(
                "name" => "Passione",
                "firstname" => "Giorno",
                "lastname" => "Giovanna",
            ), "Giovanna, Giorno - Passione"),

            "EmptyFirstname" => array(array(
                "name" => "Passione",
                "firstname" => "",
                "lastname" => "Giovanna",
            ), "Passione"),

            "NoFirstname" => array(array(
                "name" => "Passione",
                "lastname" => "Giovanna",
            ), "Passione"),

            "EmptyLastname" => array(array(
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
