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
use Splash\Core\Helpers\System\ConfigFromEnv;

class T202ConfigFromEnvTest extends TestCase
{
    /**
     * Test with valid input
     */
    public function testCompleteConnexionWithValidInput(): void
    {
        //====================================================================//
        // Test data
        $params = array();
        $connexion = "https://username:password@host.com:8080/path";
        //====================================================================//
        // Call method
        $result = ConfigFromEnv::complete($params, $connexion);
        //====================================================================//
        // Assertions
        Assert::assertTrue($result);
        Assert::assertArrayHasKey('WsIdentifier', $params);
        Assert::assertArrayHasKey('WsEncryptionKey', $params);
        Assert::assertArrayHasKey('WsHost', $params);
        Assert::assertEquals('username', $params['WsIdentifier']);
        Assert::assertEquals('password', $params['WsEncryptionKey']);
        Assert::assertEquals('https://host.com:8080/path', $params['WsHost']);
    }

    /**
     * Test completeConnexion method with invalid input
     */
    public function testCompleteConnexionWithInvalidInput(): void
    {
        //====================================================================//
        // Test data
        $params = array();
        $connexion = "invalid_url";
        Splash::mockInputs(ConfigFromEnv::CONNEXION, null);
        Splash::mockInputs(ConfigFromEnv::ENDPOINT, null);
        //====================================================================//
        // Call method
        $result = ConfigFromEnv::complete($params, $connexion);
        //====================================================================//
        // Assertions
        Assert::assertFalse($result);
        Assert::assertArrayNotHasKey('WsIdentifier', $params);
        Assert::assertArrayNotHasKey('WsEncryptionKey', $params);
        Assert::assertArrayNotHasKey('WsHost', $params);
    }

    /**
     * Test completeConnexion method with missing parts of URL
     */
    public function testCompleteConnexionWithIncompleteUrl(): void
    {
        //====================================================================//
        // Test data
        $params = array();
        $connexion = "https://host.com";
        //====================================================================//
        // Call method
        $result = ConfigFromEnv::complete($params, $connexion);
        //====================================================================//
        // Assertions
        Assert::assertFalse($result);
        Assert::assertArrayNotHasKey('WsIdentifier', $params);
        Assert::assertArrayNotHasKey('WsEncryptionKey', $params);
        Assert::assertArrayNotHasKey('WsHost', $params);
    }

    /**
     * Test completeConnexion when parameters are already set
     */
    public function testCompleteConnexionWithExistingParams(): void
    {
        //====================================================================//
        // Test data
        $params = array(
            'WsIdentifier' => 'existingUser',
            'WsEncryptionKey' => 'existingKey',
            'WsHost' => 'https://existing-host.com'
        );
        $connexion = "https://username:password@host.com:8080/path";
        //====================================================================//
        // Call method
        $result = ConfigFromEnv::complete($params, $connexion);
        //====================================================================//
        // Assertions
        Assert::assertFalse($result);
        Assert::assertEquals('existingUser', $params['WsIdentifier']);
        Assert::assertEquals('existingKey', $params['WsEncryptionKey']);
        Assert::assertEquals('https://existing-host.com', $params['WsHost']);
    }

    /**
     * Test case: completeEndpoint when endpoint URL is valid and updates the configuration
     */
    public function testCompleteEndpointValidUrl(): void
    {
        //====================================================================//
        // Test data
        $params = array();
        $endpoint = "https://example.com/api/v1";
        //====================================================================//
        // Call method
        $result = ConfigFromEnv::complete($params, null, $endpoint);
        //====================================================================//
        // Assertions
        Assert::assertTrue($result);
        Assert::assertArrayHasKey('ServerHost', $params);
        Assert::assertEquals('https://example.com', $params['ServerHost']);
        Assert::assertArrayHasKey('ServerPath', $params);
        Assert::assertEquals('/api/v1', $params['ServerPath']);
    }

    /**
     * Test case: completeEndpoint when endpoint URL is invalid
     */
    public function testCompleteEndpointInvalidUrl(): void
    {
        //====================================================================//
        // Test data
        $params = array();
        $endpoint = "invalid-url";
        //====================================================================//
        // Call method
        $result = ConfigFromEnv::complete($params, null, $endpoint);
        //====================================================================//
        // Assertions
        Assert::assertFalse($result);
        Assert::assertArrayNotHasKey('ServerHost', $params);
        Assert::assertArrayNotHasKey('ServerPath', $params);
    }

    /**
     * Test case: completeEndpoint when endpoint URL is already set in params
     */
    public function testCompleteEndpointUrlAlreadySet(): void
    {
        //====================================================================//
        // Test data
        $params = array('ServerHost' => 'https://existing.com', 'ServerPath' => '/existing');
        $endpoint = "https://example.com/api/v1";
        //====================================================================//
        // Call method
        $result = ConfigFromEnv::complete($params, null, $endpoint);
        //====================================================================//
        // Assertions
        Assert::assertFalse($result);
        Assert::assertEquals('https://existing.com', $params['ServerHost']);
        Assert::assertEquals('/existing', $params['ServerPath']);
    }
}
