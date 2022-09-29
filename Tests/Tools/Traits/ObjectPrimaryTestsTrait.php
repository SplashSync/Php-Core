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

namespace Splash\Tests\Tools\Traits;

use Exception;
use Splash\Client\Splash;
use Splash\Models\Objects\PrimaryKeysAwareInterface;

trait ObjectPrimaryTestsTrait
{
    //==============================================================================
    //      UNIT TESTS EXECUTION FUNCTIONS
    //==============================================================================

    /**
     * Execute Object Identify by Primary Keys Test with Given Data (From Module)
     *
     * @param string                $objectType  Splash Object Type Name
     * @param array<string, string> $primaryKeys Primary Keys List
     * @param null|string           $objectId    Expected Splash Object ID
     *
     * @throws Exception
     *
     * @return string
     */
    protected function identifyObjectFromModule(string $objectType, array $primaryKeys, string $objectId = null): string
    {
        //====================================================================//
        //   Check object Class
        $objectClass = Splash::object($objectType);
        $this->assertInstanceOf(
            PrimaryKeysAwareInterface::class,
            $objectClass,
            sprintf(
                "Object Class %s uses Primary keys, therefore it must Implement %s",
                get_class($objectClass),
                PrimaryKeysAwareInterface::class
            )
        );
        //====================================================================//
        // Identify Object by Primary Keys
        $identifiedId = $objectClass->getByPrimary($primaryKeys);
        //====================================================================//
        // Verify Identification Worked
        $verifiedId = $this->verifyIdentifiedObjectId($objectType, $primaryKeys, $identifiedId, $objectId);
        //====================================================================//
        // Identify Object with Randomized Primary Keys
        $randomizedId = $objectClass->getByPrimary(self::randomizePrimaryKeys($primaryKeys));
        $this->assertNull($randomizedId);

        return $verifiedId;
    }

    /**
     * Execute Object Identify by Primary Keys Test with Given Data (From Module)
     *
     * @param string                $objectType  Splash Object Type Name
     * @param array<string, string> $primaryKeys Primary Keys List
     * @param null|string           $objectId    Expected Splash Object ID
     *
     * @throws Exception
     *
     * @return string
     */
    protected function identifyObjectFromService(
        string $objectType,
        array $primaryKeys,
        string $objectId = null
    ): string {
        //====================================================================//
        //   Check object Class
        $objectClass = Splash::object($objectType);
        $this->assertInstanceOf(
            PrimaryKeysAwareInterface::class,
            $objectClass,
            sprintf(
                "Object Class %s uses Primary keys, therefore it must Implement %s",
                get_class($objectClass),
                PrimaryKeysAwareInterface::class
            )
        );
        //====================================================================//
        // Identify Object by Primary Keys via Service
        $identifiedId = $this->genericStringAction(
            SPL_S_OBJECTS,
            SPL_F_IDENTIFY,
            __METHOD__,
            array('type' => $objectType, 'keys' => $primaryKeys)
        );
        //====================================================================//
        // Verify Identification Worked
        $verifiedId = $this->verifyIdentifiedObjectId($objectType, $primaryKeys, $identifiedId, $objectId);
        //====================================================================//
        // Identify Object with Randomized Primary Keys
        $randomizedId = $this->genericStringAction(
            SPL_S_OBJECTS,
            SPL_F_IDENTIFY,
            __METHOD__,
            array('type' => $objectType, 'keys' => self::randomizePrimaryKeys($primaryKeys))
        );
        $this->assertEmpty($randomizedId);

        return $verifiedId;
    }

    //==============================================================================
    //      PRIMARY KEYS MANAGEMENT FUNCTIONS
    //==============================================================================

    /**
     * Extract All Primary Key Data from Object Data
     *
     * @param array   $fakeData   Faker Object Data Set
     * @param array[] $fieldsList Object Field List
     *
     * @return array<string, string> Primary Keys Array
     */
    protected function extractPrimaryKeys(array $fakeData, array $fieldsList): array
    {
        //====================================================================//
        // Extract Data focused on Tested Field
        $filteredData = $this->filterData(
            $fakeData,
            self::reduceFieldList(self::findPrimaryFields($fieldsList))
        ) ?? array();
        //====================================================================//
        // Verify Data
        foreach ($filteredData as $key => $value) {
            $this->assertIsString($key);
            $this->assertIsString($value);
        }

        return $filteredData;
    }

    /**
     * Randomize a set of Primary Key to make it useless
     *
     * @param array<string, string> $primaryKeys Primary Keys List
     *
     * @return array<string, string> Randomized Primary Keys Array
     */
    protected static function randomizePrimaryKeys(array $primaryKeys): array
    {
        //====================================================================//
        // Take a Random Index
        $index = array_rand($primaryKeys);
        //====================================================================//
        // Randomize this Index
        return array_replace_recursive(
            $primaryKeys,
            array($index => uniqid($primaryKeys[$index]."--"))
        );
    }

    //==============================================================================
    //      DATA VERIFICATION FUNCTIONS
    //==============================================================================

    /**
     * Verify Object ID Returned by Primary Keys Identification
     *
     * @param string                $objectType   Splash Object Type Name
     * @param array<string, string> $primaryKeys  Primary Keys List
     * @param null|string           $identifiedId Returned Splash Object ID
     * @param null|string           $objectId     Expected Splash Object ID
     *
     * @throws Exception
     *
     * @return string
     */
    private function verifyIdentifiedObjectId(
        string $objectType,
        array $primaryKeys,
        ?string $identifiedId,
        ?string $objectId
    ): string {
        //====================================================================//
        // Verify Identification Worked
        $this->assertNotNull($identifiedId, sprintf(
            "Identification of a '%s' by Primary Keys Failed: %s",
            $objectType,
            print_r($primaryKeys, true)
        ));
        if ($objectId) {
            $this->assertEquals($identifiedId, $objectId, sprintf(
                "Primary Keys returned wrong Object Id: expected %s, get %s",
                $objectId,
                $identifiedId
            ));
        }

        return $identifiedId;
    }
}
