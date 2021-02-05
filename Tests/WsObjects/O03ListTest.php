<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2021 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Tests\WsObjects;

use ArrayObject;
use Splash\Client\Splash;
use Splash\Tests\Tools\ObjectsCase;

/**
 * Objects Test Suite - Objects List Reading Verifications
 *
 * @author SplashSync <contact@splashsync.com>
 */
class O03ListTest extends ObjectsCase
{
    /**
     * @dataProvider objectTypesProvider
     *
     * @param string $testSequence
     * @param string $objectType
     *
     * @return void
     */
    public function testFromModule($testSequence, $objectType)
    {
        $this->loadLocalTestSequence($testSequence);

        //====================================================================//
        //   Execute Action Directly on Module
        $data = Splash::object($objectType)->objectsList();
        //====================================================================//
        //   Module May Return an Array (ArrayObject created by WebService)
        if (is_array($data)) {
            $data = new ArrayObject($data);
        }
        //====================================================================//
        //   Verify Response
        $this->verifyResponse($data, $objectType);
    }

    /**
     * @dataProvider objectTypesProvider
     *
     * @param string $testSequence
     * @param string $objectType
     *
     * @return void
     */
    public function testFromObjectsService($testSequence, $objectType)
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
     * @return void
     */
    public function testFromObjectsServiceErrors()
    {
        //====================================================================//
        //      Request definition without Sending ObjectType
        $this->genericErrorAction(SPL_S_OBJECTS, SPL_F_LIST, __METHOD__);
    }

    /**
     * @param ArrayObject|bool|string $data
     * @param string                  $objectType
     *
     * @return void
     */
    public function verifyResponse($data, $objectType)
    {
        //====================================================================//
        //   Verify Response
        $this->assertNotEmpty($data, "Objects List is Empty");
        $this->assertInstanceOf("ArrayObject", $data, "Objects List is Not an ArrayObject");

        $this->verifyMetaInformations($data, $objectType);
        $this->verifyAvailableFields($data, $objectType);
    }

    /**
     * @param ArrayObject $data
     * @param string      $objectType
     *
     * @return void
     */
    public function verifyAvailableFields($data, $objectType)
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
     * @param ArrayObject $data
     * @param string      $objectType
     *
     * @return void
     */
    public function verifyMetaInformations($data, $objectType)
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
