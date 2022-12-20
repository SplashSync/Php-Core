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

namespace Splash\Tests\Core;

use ArrayObject;
use Splash\Core\SplashCore     as Splash;
use Splash\Models\Helpers\SplashUrlHelper;
use Splash\Tests\Tools\TestCase;

/**
 * Core Test Suite - Module's Local Class Basics Verifications
 *
 * @author SplashSync <contact@splashsync.com>
 */
class C11LocalCoreClassTest extends TestCase
{
    /**
     * @return void
     */
    public function testParameterFunction()
    {
        //====================================================================//
        //   VERIFY LOCAL PARAMETERS READING
        //====================================================================//

        $parameters = Splash::local()->parameters();

        //====================================================================//
        // Complete Local Configuration with ENV Variables
        SplashUrlHelper::completeParameters($parameters);

        //====================================================================//
        //   Verify Parameters
        $this->assertIsArray($parameters, "Returned Local Parameters are Not inside an Array");
        $this->assertNotEmpty($parameters, "Returned Empty Parameters");
        $this->assertArrayHasKey("WsIdentifier", $parameters, "Local Parameter is Missing");
        $this->assertArrayHasKey("WsEncryptionKey", $parameters, "Local Parameter is Missing");
        $this->assertNotEmpty($parameters["WsIdentifier"], "Local Parameter WsIdentifier is Empty");
        $this->assertNotEmpty($parameters["WsEncryptionKey"], "Local Parameter WsEncryptionKey is Empty");

        //====================================================================//
        //   Verify Module Parsing
        $this->assertTrue(
            Splash::validate()->isValidLocalParameterArray($parameters),
            "Local Parameter Module's Verifictaion failled."
        );
    }

    /**
     * @return void
     */
    public function testIncludesFunction()
    {
        //====================================================================//
        //   VERIFY LOCAL INCLUDES PASS & REPEATABLE
        //====================================================================//
        for ($i = 0; $i < 5; $i++) {
            $this->assertTrue(Splash::local()->includes(), "Local Include Must Return True & be repeatable.");
        }
    }

    /**
     * @return void
     */
    public function testSelfTestFunction()
    {
        //====================================================================//
        //   VERIFY LOCAL SELFTEST PASS & REPEATABLE
        //====================================================================//
        for ($i = 0; $i < 5; $i++) {
            $this->assertTrue(Splash::local()->selfTest(), "Local SelfTest Must Return True (Passed) & be repeatable.");
        }
    }

    /**
     * @return void
     */
    public function testInformationsFunction()
    {
        //====================================================================//
        //   VERIFY LOCAL INFOS READING
        //====================================================================//

        //====================================================================//
        // Init Response Object
        $input = new ArrayObject(array("Dummy" => true), ArrayObject::ARRAY_AS_PROPS);

        //====================================================================//
        // Fetch Informations
        $output = Splash::local()->informations($input);

        //====================================================================//
        //   Verify Informations
        $this->assertInstanceOf("ArrayObject", $output, "Returned Local Informations are Not inside an ArrayObject");
        $output = $output->getArrayCopy();
        $this->assertArrayHasKey("company", $output, "Local Informations is Missing");
        $this->assertArrayHasKey("address", $output, "Local Informations is Missing");
        $this->assertArrayHasKey("zip", $output, "Local Informations is Missing");
        $this->assertArrayHasKey("town", $output, "Local Informations is Missing");
        $this->assertArrayHasKey("www", $output, "Local Informations is Missing");
        $this->assertArrayHasKey("email", $output, "Local Informations is Missing");
        $this->assertArrayHasKey("phone", $output, "Local Informations is Missing");

        //====================================================================//
        //   Verify Module Informations are Still Present
        $this->assertArrayHasKey("Dummy", $output, "Splash Original Informations are Missing");

        //====================================================================//
        //   Verify Module Parsing
        $this->assertNotEmpty(Splash::informations(), "Module Informations Reading failled.");
    }
}
