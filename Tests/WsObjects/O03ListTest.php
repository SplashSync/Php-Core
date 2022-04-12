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

namespace Splash\Tests\WsObjects;

use Exception;
use Splash\Client\Splash;
use Splash\Tests\Tools\ObjectsCase;

/**
 * Objects Test Suite - Objects List Reading Verifications
 */
class O03ListTest extends ObjectsCase
{
    /**
     * Verify Loading Objects List from Module
     *
     * @dataProvider objectTypesProvider
     *
     * @param string $testSequence
     * @param string $objectType
     *
     * @throws Exception
     *
     * @return void
     */
    public function testFromModule(string $testSequence, string $objectType): void
    {
        $this->loadLocalTestSequence($testSequence);
        //====================================================================//
        //   Execute Action Directly on Module
        $data = Splash::object($objectType)->objectsList();
        //====================================================================//
        //   Verify Response
        $this->verifyResponse($data, $objectType);
    }

    /**
     * Verify Loading Objects List from Service
     *
     * @dataProvider objectTypesProvider
     *
     * @param string $testSequence
     * @param string $objectType
     *
     * @throws Exception
     *
     * @return void
     */
    public function testFromObjectsService(string $testSequence, string $objectType): void
    {
        $this->loadLocalTestSequence($testSequence);
        //====================================================================//
        //   Execute Action From Splash Server to Module
        $data = $this->genericAction(
            SPL_S_OBJECTS,
            SPL_F_LIST,
            __METHOD__,
            array( "id" => null, "type" => $objectType)
        );
        //====================================================================//
        //   Verify Response
        $this->verifyResponse($data, $objectType);
    }

    /**
     * Verify Loading Objects List with Wrong Object Type
     *
     * @return void
     */
    public function testFromObjectsServiceErrors(): void
    {
        //====================================================================//
        //      Request definition without Sending ObjectType
        $this->genericErrorAction(SPL_S_OBJECTS, SPL_F_LIST, __METHOD__);
    }

    /**
     * Verify Objects List Response
     *
     * @param null|array $data
     * @param string     $objectType
     *
     * @throws Exception
     *
     * @return void
     */
    public function verifyResponse(?array $data, string $objectType): void
    {
        //====================================================================//
        //   Verify Response
        $this->assertNotEmpty($data, "Objects List is Empty");
        $this->assertIsArray($data, "Objects List is Not an Array");

        $this->verifyMetaInformations($data, $objectType);
        $this->verifyAvailableFields($data, $objectType);
    }

    /**
     * Verify Listed Fields are Available
     *
     * @param array  $data
     * @param string $objectType
     *
     * @throws Exception
     *
     * @return void
     */
    public function verifyAvailableFields(array $data, string $objectType): void
    {
        //====================================================================//
        // Verify Fields are Available
        $fields = Splash::object($objectType)->fields();
        //====================================================================//
        // Verify List Data Items
        foreach ($data as $item) {
            //====================================================================//
            // Verify Object Id field is available
            $this->assertArrayHasKey(
                "id",
                $item,
                $objectType." List => Object Identifier (id) is not defined in List."
            );
            $this->assertIsScalar(
                $item["id"],
                $objectType." List => Object Identifier (id) is not String convertible."
            );

            //====================================================================//
            // Verify all "inlist" fields are available
            foreach ($fields as $field) {
                if (isset($field['inlist']) && !empty($field['inlist'])) {
                    $this->assertIsString($field["id"]);
                    $this->assertIsString($field["name"]);
                    $this->assertArrayHasKey(
                        $field["id"],
                        $item,
                        $objectType." List => Field (".$field["name"].") is marked as 'inlist' but not found."
                    );
                    $this->assertIsScalar(
                        $item["id"],
                        $objectType." List => Field (".$field["name"].") is not String convertible."
                    );
                }
            }
        }
    }

    /**
     * Verify Metadata are Available
     *
     * @param array  $data
     * @param string $objectType
     *
     * @return void
     */
    public function verifyMetaInformations(array &$data, string $objectType): void
    {
        //====================================================================//
        // Verify List Meta Are Available
        $this->assertArrayHasKey("meta", $data, $objectType." List => Meta Informations are not defined");
        $meta = $data["meta"];
        $this->assertArrayHasKey("current", $meta, $objectType." List => Meta current value not defined");
        $this->assertArrayHasKey("total", $meta, $objectType." List => Meta total value are not defined");

        if (!empty($meta["current"]) && !empty($meta["total"])) {
            //====================================================================//
            // Verify List Meta Format
            $this->assertArrayInternalType(
                $meta,
                "current",
                "numeric",
                $objectType." List => Current Object Count not an Integer"
            );
            $this->assertArrayInternalType(
                $meta,
                "total",
                "numeric",
                $objectType." List => Total Object Count not an Integer"
            );
        }

        //====================================================================//
        // Verify List Meta Informations
        unset($data["meta"]);
        $this->assertEquals(
            $meta["current"],
            count($data),
            $objectType." List => Current Object Count is different from Given Meta['current'] count."
        );
    }
}
