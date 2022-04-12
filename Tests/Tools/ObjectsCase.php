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

namespace Splash\Tests\Tools;

use Exception;
use Splash\Client\Splash;
use Splash\Models\Fields\FieldsManagerTrait;

/**
 * Splash Test Tools - Objects Test Case Base Class
 */
class ObjectsCase extends AbstractBaseCase
{
    use FieldsManagerTrait;
    use Traits\ObjectsDataTrait;
    use Traits\ObjectsFakerTrait;

    /**
     * List of Created & Tested Object used to delete if test failed.
     *
     * @var array
     */
    private $createdObjects = array();

    /**
     * @throws Exception
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        //====================================================================//
        // BOOT or REBOOT MODULE
        Splash::reboot();

        //====================================================================//
        // FAKE SPLASH SERVER HOST URL
        Splash::configuration()->WsHost = "No.Commit.allowed.not";

        //====================================================================//
        // Load Module Local Configuration (In Safe Mode)
        //====================================================================//
        $this->loadLocalTestParameters();
    }

    //====================================================================//
    //   Data Provider Functions
    //====================================================================//

    /**
     * Data Provider : Objects Types Tests Sequences
     *
     * @throws Exception
     *
     * @return array
     */
    public function objectTypesProvider(): array
    {
        $result = array();

        self::setUp();

        //====================================================================//
        // Check if Local Tests Sequences are defined
        if (method_exists(Splash::local(), "TestSequences")) {
            $testSequences = Splash::local()->testSequences("List");
        } else {
            $testSequences = array( 1 => "None");
        }

        //====================================================================//
        //   For Each Test Sequence
        foreach ($testSequences as $testSequence) {
            //====================================================================//
            //   Filter Tested Sequences  =>> Skip
            if (!self::isAllowedSequence($testSequence)) {
                continue;
            }
            $this->loadLocalTestSequence($testSequence);
            //====================================================================//
            //   For Each Object Type
            foreach (Splash::objects() as $objectType) {
                //====================================================================//
                //   Filter Tested Object Types  =>> Skip
                if (!self::isAllowedObjectType($objectType)) {
                    continue;
                }
                //====================================================================//
                //   Add Object Type to List
                $dataSetName = '['.$testSequence."] ".$objectType;
                $result[$dataSetName] = array($testSequence, $objectType);
            }
        }

        self::tearDown();

        return $result;
    }

    /**
     * Data Provider : Objects Types x Fields Tests Sequences
     *
     * @throws Exception
     *
     * @return array
     */
    public function objectFieldsProvider(): array
    {
        $result = array();

        self::setUp();

        //====================================================================//
        // Check if Local Tests Sequences are defined
        if (method_exists(Splash::local(), "TestSequences")) {
            $testSequences = Splash::local()->testSequences("List");
        } else {
            $testSequences = array( 1 => "None");
        }

        //====================================================================//
        //   For Each Test Sequence
        foreach ($testSequences as $testSequence) {
            //====================================================================//
            //   Filter Tested Sequences  =>> Skip
            if (!self::isAllowedSequence($testSequence)) {
                continue;
            }
            $this->loadLocalTestSequence($testSequence);
            //====================================================================//
            //   For Each Object Type
            foreach (Splash::objects() as $objectType) {
                //====================================================================//
                //   Filter Tested Object Types  =>> Skip
                if (!self::isAllowedObjectType($objectType)) {
                    continue;
                }
                //====================================================================//
                //   For Each Field Type
                foreach (Splash::object($objectType)->fields() as $field) {
                    //====================================================================//
                    //   Filter Tested Object Fields  =>> Skip
                    if (!self::isAllowedObjectField($field['id'])) {
                        continue;
                    }
                    $dataSetName = '['.$testSequence."] ".$objectType."->".$field['id'];
                    $result[$dataSetName] = array($testSequence, $objectType, $field);
                }
            }
        }

        self::tearDown();

        return $result;
    }

    /**
     * Set Current Tested Object to Filter Objects List upon Fake ObjectId Creation
     *
     * @param string $objectType Expected Object Type
     * @param string $objectId   Expected Object Id
     *
     * @return void
     */
    protected function setCurrentObject(string $objectType, string $objectId): void
    {
        $this->settings["CurrentType"] = $objectType;
        $this->settings["CurrentId"] = $objectId;
    }

    //==============================================================================
    //      OBJECTS DELETE AT THE END OF TESTS
    //==============================================================================

    /**
     * Add Object Id to List of Tested Objects (To delete at the End)
     *
     * @param string      $objectType
     * @param null|string $objectId
     *
     * @return void
     */
    protected function addTestedObject(string $objectType, string $objectId = null)
    {
        $this->createdObjects[] = array(
            "ObjectType" => $objectType,
            "ObjectId" => $objectId,
        );
    }

    /**
     * Delete all Objects Created for Testing
     *
     * @throws Exception
     *
     * @return void
     */
    protected function cleanTestedObjects(): void
    {
        foreach ($this->createdObjects as $object) {
            if (empty($object["ObjectId"])) {
                continue;
            }
            //====================================================================//
            //   Verify Delete is Allowed
            $definition = Splash::object($object["ObjectType"])->description();
            if ($definition["allow_push_deleted"]) {
                continue;
            }
            Splash::object($object["ObjectType"])->delete($object["ObjectId"]);
        }
    }
}
