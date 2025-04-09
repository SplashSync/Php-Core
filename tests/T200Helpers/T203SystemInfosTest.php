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
use Splash\Core\Client\Splash;
use Splash\Core\Helpers\System\ServerInfos;

/**
 * Helpers Test Suite - Test of System Information Collector
 */
class T203SystemInfosTest extends TestCase
{
    /**
     * Test getServerName method when Splash configuration provides ServerHost
     */
    public function testGetServerNameFromConfiguration(): void
    {
        //====================================================================//
        // Mock Splash configuration
        Splash::mockInputs('SERVER_NAME', null);
        Splash::mockInputs('SERVER_ADDR', null);
        Splash::configuration()->ServerHost = 'configured-server-name';

        //====================================================================//
        // Call getServerName
        $result = ServerInfos::getServerName();

        Assert::assertSame('configured-server-name', $result);

        //====================================================================//
        // Clean configuration mock
        unset(Splash::configuration()->ServerHost);
    }

    /**
     * Test getServerName method when SERVER_NAME is set via secured reading
     */
    public function testGetServerNameFromInput(): void
    {
        //====================================================================//
        // Mock SERVER_NAME using Splash input
        Splash::mockInputs('SERVER_NAME', 'input-server-name');
        Splash::mockInputs('SERVER_ADDR', null);
        //====================================================================//
        // Call getServerName
        $result = ServerInfos::getServerName();

        Assert::assertSame('input-server-name', $result);

        //====================================================================//
        // Clean mock
        Splash::mockInputs('SERVER_NAME', null);
    }

    /**
     * Test getServerName method when SERVER_NAME is set in $_SERVER
     */
    public function testGetServerNameFromServerGlobal(): void
    {
        //====================================================================//
        // Mock $_SERVER global variable
        $_SERVER['SERVER_NAME'] = 'global-server-name';
        //====================================================================//
        // Call getServerName
        $result = ServerInfos::getServerName();

        Assert::assertSame('global-server-name', $result);

        //====================================================================//
        // Clean mock
        unset($_SERVER['SERVER_NAME']);
    }

    /**
     * Test getServerName method when no data is available
     */
    public function testGetServerNameEmpty(): void
    {
        //====================================================================//
        // Ensure no configurations or globals are set
        Splash::mockInputs('SERVER_NAME', null);
        Splash::mockInputs('SERVER_ADDR', null);
        unset($_SERVER['SERVER_NAME']);
        //====================================================================//
        // Call getServerName
        $result = ServerInfos::getServerName();

        Assert::assertSame('', $result);
    }

    /**
     * Test getServerPath method when configuration value is provided
     */
    public function testGetServerPathFromConfiguration(): void
    {
        //====================================================================//
        // Mock configuration with a specific ServerPath
        Splash::mockInputs("DOCUMENT_ROOT", "/var/www/html");
        Splash::configuration()->ServerPath = "/api/soap.php";
        //====================================================================//
        // Call getServerPath
        $result = ServerInfos::getServerPath();
        //====================================================================//
        // Assert the path from the configuration is returned
        Assert::assertEquals("/api/soap.php", $result);
    }

    /**
     * Test getServerPath method when configuration value is not provided
     */
    public function testGetServerPathFromGlobals(): void
    {
        //====================================================================//
        // Mock inputs for DOCUMENT_ROOT and simulate module path detection
        Splash::mockInputs("DOCUMENT_ROOT", dirname(ServerInfos::getModulePath()));
        //====================================================================//
        // Override Splash configuration to clear ServerPath
        Splash::configuration()->offsetUnset("ServerPath");
        //====================================================================//
        // Call getServerPath
        $result = ServerInfos::getServerPath();
        //====================================================================//
        // Assert the computed path is returned correctly
        Assert::assertNotEmpty($result);
        Assert::assertStringEndsWith("/soap.php", $result);
    }

    /**
     * Test getServerPath method when DOCUMENT_ROOT is not available
     */
    public function testGetServerPathWithoutDocumentRoot(): void
    {
        //====================================================================//
        // Clear mock inputs for DOCUMENT_ROOT
        Splash::mockInputs("DOCUMENT_ROOT", "/this/path/does/not/exists");
        //====================================================================//
        // Override Splash configuration to clear ServerPath
        if (isset(Splash::configuration()->ServerPath)) {
            Splash::configuration()->offsetUnset("ServerPath");
        }
        //====================================================================//
        // Assert the method returns null when DOCUMENT_ROOT is missing
        $result = ServerInfos::getServerPath();
        //====================================================================//
        // Assert the computed path is empty
        Assert::assertNull($result);
    }

    /**
     * Test ServerInfos::getScheme for HTTP scenario.
     */
    public function testGetSchemeForHttp(): void
    {
        //====================================================================//
        // Mock input to simulate HTTP protocol
        Splash::mockInputs('REQUEST_SCHEME', 'http');
        Splash::mockInputs('HTTPS', null);
        Splash::mockInputs('SERVER_PORT', '80');
        //====================================================================//
        // Assert the scheme is HTTP
        Assert::assertEquals('http', ServerInfos::getScheme());
    }

    /**
     * Test ServerInfos::getScheme for HTTPS scenario using REQUEST_SCHEME.
     */
    public function testGetSchemeForHttpsRequestScheme(): void
    {
        //====================================================================//
        // Mock input to simulate HTTPS protocol via REQUEST_SCHEME
        Splash::mockInputs('REQUEST_SCHEME', 'https');
        Splash::mockInputs('HTTPS', null);
        Splash::mockInputs('SERVER_PORT', '80');
        //====================================================================//
        // Assert the scheme is HTTPS
        Assert::assertEquals('https', ServerInfos::getScheme());
    }

    /**
     * Test ServerInfos::getScheme for HTTPS scenario using HTTPS.
     */
    public function testGetSchemeForHttpsUsingHttps(): void
    {
        //====================================================================//
        // Mock input to simulate HTTPS protocol via HTTPS header
        Splash::mockInputs('REQUEST_SCHEME', 'http');
        Splash::mockInputs('HTTPS', 'on');
        Splash::mockInputs('SERVER_PORT', '80');
        //====================================================================//
        // Assert the scheme is HTTPS
        Assert::assertEquals('https', ServerInfos::getScheme());
    }

    /**
     * Test ServerInfos::getScheme for HTTPS scenario using SERVER_PORT.
     */
    public function testGetSchemeForHttpsUsingServerPort(): void
    {
        //====================================================================//
        // Mock input to simulate HTTPS protocol via SERVER_PORT
        Splash::mockInputs('REQUEST_SCHEME', 'http');
        Splash::mockInputs('HTTPS', null);
        Splash::mockInputs('SERVER_PORT', '443');
        //====================================================================//
        // Assert the scheme is HTTPS
        Assert::assertEquals('https', ServerInfos::getScheme());
    }

    /**
     * Test ServerInfos::getScheme with no secure parameters set.
     */
    public function testGetSchemeDefaultToHttp(): void
    {
        //====================================================================//
        // Mock input to simulate no secure parameters
        Splash::mockInputs('REQUEST_SCHEME', null);
        Splash::mockInputs('HTTPS', null);
        Splash::mockInputs('SERVER_PORT', null);
        //====================================================================//
        // Assert the scheme is HTTP
        Assert::assertEquals('http', ServerInfos::getScheme());
    }
}
