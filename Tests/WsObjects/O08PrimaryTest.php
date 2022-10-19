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
use Splash\Tests\Tools\Traits\ObjectPrimaryTestsTrait;
use Splash\Tests\Tools\Traits\ObjectsSetTestsTrait;

/**
 * Objects Test Suite - Object Primary Fields Verifications
 *
 * @author SplashSync <contact@splashsync.com>
 */
class O08PrimaryTest extends ObjectsCase
{
    use ObjectsSetTestsTrait;
    use ObjectPrimaryTestsTrait;

    /**
     * Test Identify Object ID by Primary Field from Module
     *
     * @dataProvider objectFieldsProvider
     *
     * @param string $testSequence
     * @param string $objectType
     * @param array  $field
     *
     * @throws Exception
     *
     * @return void
     */
    public function testCheckPrimaryFieldFromModule(string $testSequence, string $objectType, array $field): void
    {
        $this->loadLocalTestSequence($testSequence);
        //====================================================================//
        // Check if Object is Primary Field
        if (!$field['primary']) {
            $this->assertTrue(true);

            return;
        }
        //====================================================================//
        // Load All Fields List
        $fields = Splash::object($objectType)->fields();

        //====================================================================//
        //   OBJECT CREATE TEST
        //====================================================================//

        //====================================================================//
        //   Generate Dummy New Object Data (Required Fields Only)
        $newData = $this->prepareForTesting($objectType, $field);
        if (!$newData) {
            return;
        }
        //====================================================================//
        //   Execute Create Test
        $objectId = $this->setObjectFromModule($objectType, $newData);

        //====================================================================//
        //   DETECT BY PRIMARY TEST
        //====================================================================//

        //====================================================================//
        //   Extract All Primary Key
        $primaryKeys = $this->extractPrimaryKeys($newData, $fields);
        //====================================================================//
        //   Test Object Identification by Primary Keys
        $identifiedId = $this->identifyObjectFromModule($objectType, $primaryKeys, $objectId);

        //====================================================================//
        //   OBJECT DELETE
        //====================================================================//

        //====================================================================//
        //   Delete Object From Module
        $this->deleteObjectFromModule($objectType, $identifiedId);
    }

    /**
     * Test Identify Object ID by Primary Field from Module
     *
     * @dataProvider objectFieldsProvider
     *
     * @param string $testSequence
     * @param string $objectType
     * @param array  $field
     *
     * @throws Exception
     *
     * @return void
     */
    public function testCheckPrimaryFieldFromService(string $testSequence, string $objectType, array $field): void
    {
        $this->loadLocalTestSequence($testSequence);
        //====================================================================//
        // Check if Object is Primary Field
        if (!$field['primary']) {
            $this->assertTrue(true);

            return;
        }
        //====================================================================//
        // Load All Fields List
        $fields = Splash::object($objectType)->fields();

        //====================================================================//
        //   OBJECT CREATE TEST
        //====================================================================//

        //====================================================================//
        //   Generate Dummy New Object Data (Required Fields Only)
        $newData = $this->prepareForTesting($objectType, $field);
        if (!$newData) {
            return;
        }
        //====================================================================//
        //   Execute Create Test
        $objectId = $this->setObjectFromService($objectType, $newData);

        //====================================================================//
        //   DETECT BY PRIMARY TEST
        //====================================================================//

        //====================================================================//
        //   Extract All Primary Key
        $primaryKeys = $this->extractPrimaryKeys($newData, $fields);
        //====================================================================//
        //   Test Object Identification by Primary Keys
        $identifiedId = $this->identifyObjectFromService($objectType, $primaryKeys, $objectId);

        //====================================================================//
        //   OBJECT DELETE
        //====================================================================//

        //====================================================================//
        //   Delete Object From Module
        $this->deleteObjectFromModule($objectType, $identifiedId);
    }
}
