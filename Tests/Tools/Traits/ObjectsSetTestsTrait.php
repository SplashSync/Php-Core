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
use Splash\Components\CommitsManager;

/**
 * Splash Test Tools - Objects Fields Management
 */
trait ObjectsSetTestsTrait
{
    /**
     * @var string Md5 CheckSum of Current Field Data Block
     */
    protected string $fieldMd5;

    //==============================================================================
    //      COMPLETE TESTS EXECUTION FUNCTIONS
    //==============================================================================

    /**
     * Execute Single Field Test From Module
     *
     * @param string      $objectType    Splash Object Type Name
     * @param array       $field         Current Tested Field (ArrayObject)
     * @param null|string $forceObjectId Object Id (Update) or Null (Create)
     *
     * @throws Exception
     *
     * @return bool
     */
    protected function coreTestSetSingleFieldFromModule(
        string $objectType,
        array $field,
        string $forceObjectId = null
    ): bool {
        //====================================================================//
        //   OBJECT CREATE TEST
        //====================================================================//

        //====================================================================//
        //   Generate Dummy New Object Data (Required Fields Only)
        $this->originData = null;
        $newData = $this->prepareForTesting($objectType, $field);
        if (false == $newData) {
            return true;
        }
        $this->originData = $newData;

        //====================================================================//
        //   Execute Create Test
        $objectId = $this->setObjectFromModule($objectType, $this->originData, $forceObjectId);

        //====================================================================//
        //   OBJECT UPDATE TEST
        //====================================================================//

        //====================================================================//
        //   Update Data Focused Field Data
        $updateData = $this->prepareForTesting($objectType, $field);
        $this->assertNotEmpty($updateData);
        if (false == $updateData) {
            return true;
        }

        //====================================================================//
        //   Execute Update Test
        $this->setObjectFromModule($objectType, $updateData, $objectId);

        //====================================================================//
        //   OBJECT DELETE
        //====================================================================//

        //====================================================================//
        // If Test was Forced on a Specific Object (Local Sequences)
        if (!is_null($forceObjectId)) {
            return true;
        }

        //====================================================================//
        //   Delete Object From Module
        $this->deleteObjectFromModule($objectType, $objectId);

        return true;
    }

    /**
     * Execute Single Field Test From Service
     *
     * @param string      $objectType    Splash Object Type Name
     * @param array       $field         Current Tested Field (ArrayObject)
     * @param null|string $forceObjectId Object Id (Update) or Null (Create)
     *
     * @throws Exception
     *
     * @return bool
     */
    protected function coreTestSetSingleFieldFromService(
        string $objectType,
        array $field,
        string $forceObjectId = null
    ): bool {
        //====================================================================//
        //   OBJECT CREATE TEST
        //====================================================================//

        //====================================================================//
        //   Generate Dummy New Object Data (Required Fields Only)
        $this->originData = null;
        $newData = $this->prepareForTesting($objectType, $field);
        if (!$newData) {
            return true;
        }
        $this->originData = $newData;

        //====================================================================//
        //   Execute Create Test
        $objectId = $this->setObjectFromService($objectType, $this->originData, $forceObjectId);

        //====================================================================//
        // BOOT or REBOOT MODULE
        $this->setUp();

        //====================================================================//
        //   OBJECT UPDATE TEST
        //====================================================================//

        //====================================================================//
        //   Generate Dummy Object Data (Required Fields Only)
        $updateData = $this->prepareForTesting($objectType, $field);
        $this->assertNotEmpty($updateData);
        if (false == $updateData) {
            return true;
        }

        //====================================================================//
        //   Execute Update Test
        $this->setObjectFromService($objectType, $updateData, $objectId);

        //====================================================================//
        //   OBJECT DELETE
        //====================================================================//

        //====================================================================//
        // If Test was Forced on a Specific Object (Local Sequences)
        if (!is_null($forceObjectId)) {
            return true;
        }

        //====================================================================//
        //   Delete Object From Module
        $this->deleteObjectFromModule($objectType, $objectId);

        return true;
    }

    //==============================================================================
    //      UNIT TESTS EXECUTION FUNCTIONS
    //==============================================================================

    /**
     * Execute Object Create or Update Test with Given Data (From Module)
     *
     * @param string      $objectType    Splash Object Type Name
     * @param array       $objectData    Splash Data Block
     * @param null|string $forceObjectId Object Id (Update) or Null (Create)
     *
     * @throws Exception
     *
     * @return string
     */
    protected function setObjectFromModule(string $objectType, array $objectData, string $forceObjectId = null): string
    {
        //====================================================================//
        // Lock New Objects To Avoid Action Commit
        Splash::object($objectType)->lock($forceObjectId);
        //====================================================================//
        // Clean Objects Committed Array
        CommitsManager::reset();
        //====================================================================//
        //   Update Object on Module
        $objectId = Splash::object($objectType)->set($forceObjectId, $objectData);
        //====================================================================//
        //   Verify Object Id Is Not Empty
        $this->assertNotEmpty($objectId, 'Returned New Object Id is Empty');
        $this->assertIsString($objectId, 'Returned New Object Id is Empty');
        //====================================================================//
        //   Verify Response
        $this->verifySetResponse($objectType, $objectId, ($forceObjectId ? SPL_A_UPDATE : SPL_A_CREATE), $objectData);
        //====================================================================//
        // UnLock New Objects To Avoid Action Commit
        Splash::object($objectType)->unLock();
        //====================================================================//
        // Lock This Object To Avoid Being Selected for Linking
        $this->setCurrentObject($objectType, $objectId);
        //====================================================================//
        // Return Object Id
        return $objectId;
    }

    /**
     * Execute Object Create or Update Test with Given Data (From Service)
     *
     * @param string      $objectType    Splash Object Type Name
     * @param array       $objectData    Splash Data Block
     * @param null|string $forceObjectId Object Id (Update) or Null (Create)
     *
     * @throws Exception
     *
     * @return string
     */
    protected function setObjectFromService(string $objectType, array $objectData, string $forceObjectId = null): string
    {
        //====================================================================//
        // Clean Objects Committed Array
        CommitsManager::reset();
        //====================================================================//
        //   Create a New Object via Service
        $objectId = $this->genericStringAction(
            SPL_S_OBJECTS,
            SPL_F_SET,
            __METHOD__,
            array('id' => $forceObjectId, 'type' => $objectType, 'fields' => $objectData)
        );
        //====================================================================//
        //   Verify Object Id Is Not Empty
        $this->assertNotEmpty($objectId, 'Returned New Object Id is Empty');
        $this->assertIsString($objectId, 'Returned New Object Id is Empty');
        //====================================================================//
        //   Verify Response
        $this->verifySetResponse($objectType, $objectId, ($forceObjectId ? SPL_A_UPDATE : SPL_A_CREATE), $objectData);
        //====================================================================//
        // UnLock New Objects To Avoid Action Commit
        Splash::object($objectType)->unLock();
        //====================================================================//
        // Lock This Object To Avoid Being Selected for Linking
        $this->setCurrentObject($objectType, $objectId);
        //====================================================================//
        // Return Object Id
        return $objectId;
    }

    /**
     * Execute Object Delete Test (From Module)
     *
     * @param string $objectType Splash Object Type Name
     * @param string $objectId   Object ID
     *
     * @throws Exception
     *
     * @return void
     */
    protected function deleteObjectFromModule(string $objectType, string $objectId): void
    {
        //====================================================================//
        // Lock New Objects To Avoid Action Commit
        Splash::object($objectType)->lock($objectId);
        //====================================================================//
        //   Delete Object on Module
        $data = Splash::object($objectType)->delete($objectId);
        //====================================================================//
        //   Verify Response
        $this->verifyDeleteResponse($objectType, $objectId, $data);
    }

    //==============================================================================
    //      DATA VERIFICATION FUNCTIONS
    //==============================================================================

    /**
     * Verify Client Object Set Response.
     *
     * @param string  $objectType
     * @param ?string $objectId
     * @param string  $action
     * @param array   $expectedData
     *
     * @throws Exception
     *
     * @return void
     */
    protected function verifySetResponse(
        string $objectType,
        ?string $objectId,
        string $action,
        array $expectedData
    ): void {
        //====================================================================//
        //   Verify Object Id Is Not Empty
        $this->assertNotEmpty($objectId, 'Returned New Object Id is Empty');

        //====================================================================//
        //   Add Object Id to Created List
        $this->addTestedObject($objectType, $objectId);

        //====================================================================//
        //   Verify Object Id Is in Right Format
        $this->assertIsString($objectId, 'New Object Id is not a Strings');

        //====================================================================//
        //   Verify Object Change Was Committed
        $this->assertIsFirstCommitted($action, $objectType, $objectId);

        //====================================================================//
        //   Read Object Data
        $currentData = Splash::object($objectType)
            ->get($objectId, $this->reduceFieldList($this->fields, true));
        $this->assertIsArray($currentData);

        //====================================================================//
        //   Verify Object Data are Ok
        $this->compareDataBlocks($this->fields, $expectedData, $currentData, $objectType);
    }

    /**
     * Verify Client Object Delete Response.
     *
     * @param string       $objectType
     * @param string       $objectId
     * @param array|scalar $data
     *
     * @throws Exception
     *
     * @return void
     */
    protected function verifyDeleteResponse(string $objectType, string $objectId, $data): void
    {
        //====================================================================//
        //   Verify Response
        $this->assertIsSplashBool($data, 'Object Delete Response Must be a Bool');
        $this->assertNotEmpty($data, 'Object Delete Response is Not True');

        //====================================================================//
        // Lock New Objects To Avoid Action Commit
        Splash::object($objectType)->lock($objectId);

        //====================================================================//
        //   Verify Repeating Delete as Same Result
        $repeatedResponse = Splash::object($objectType)->delete($objectId);
        $this->assertTrue(
            $repeatedResponse,
            'Object Repeated Delete, Must return True even if Object Already Deleted.'
        );

        //====================================================================//
        //   Verify Object not Present anymore
        $fields = $this->reduceFieldList(Splash::object($objectType)->fields(), true);
        $getResponse = Splash::object($objectType)->get($objectId, $fields);
        $this->assertNull($getResponse, 'Object Not Delete, I can still read it!!');
    }

    //==============================================================================
    //      TESTS PREPARATION FUNCTIONS
    //==============================================================================

    /**
     * Ensure Set/Write Test is Possible & Generate Fake Object Data
     * -> This Function uses Preloaded Fields
     * -> If Md5 provided, check Current Field was Modified
     *
     * @param string $objectType Current Object Type
     * @param array  $field      Current Tested Field (ArrayObject)
     * @param bool   $unique     Ask for Unique Field Data
     *
     * @throws Exception
     *
     * @return null|array Generated Data Block or False if not Allowed
     */
    protected function prepareForTesting(string $objectType, array $field, bool $unique = true): ?array
    {
        //====================================================================//
        //   Verify Test is Required
        if (!$this->verifyTestIsAllowed($objectType, $field)) {
            return null;
        }
        //====================================================================//
        // Return Generated Object Data
        return $this->generateObjectData($objectType, $field, $unique);
    }

    /**
     * Verify if Test is Allowed for This Field
     *
     * @param string     $objectType
     * @param null|array $field
     *
     * @throws Exception
     *
     * @return bool
     */
    protected function verifyTestIsAllowed(string $objectType, array $field = null): bool
    {
        $definition = Splash::object($objectType)->description();

        $this->assertNotEmpty($definition);
        //====================================================================//
        //   Verify Create is Allowed
        if (!$definition['allow_push_created']) {
            return false;
        }
        //====================================================================//
        //   Verify Update is Allowed
        if (!$definition['allow_push_updated']) {
            return false;
        }
        //====================================================================//
        //   Verify Delete is Allowed
        if (!$definition['allow_push_deleted']) {
            return false;
        }
        //====================================================================//
        //   Verify Field is To Be Tested
        if (!is_null($field) && $field['notest']) {
            return false;
        }

        return true;
    }

    /**
     * Generate Fake Object Data
     * -> This Function uses Preloaded Fields
     * -> If Md5 provided, check Current Field was Modified
     *
     * @param string $objectType Current Object Type
     * @param array  $field      Current Tested Field (ArrayObject)
     * @param bool   $unique     Ask for Unique Field Data
     *
     * @throws Exception
     *
     * @return null|array Generated Data Block or False if not Allowed
     */
    protected function generateObjectData(string $objectType, array $field, bool $unique = true): ?array
    {
        //====================================================================//
        // Generate Required Fields List
        $this->fields = $this->fakeFieldsList($objectType, array($field['id']), true);

        //====================================================================//
        // Prepare Fake Object Data
        //====================================================================//
        $try = 0;
        do {
            //====================================================================//
            // Generate Object Data
            $fakeData = $this->fakeObjectData($this->fields);
            if (false == $fakeData) {
                return null;
            }
            //====================================================================//
            // Check if Compare is Required
            if ((false == $unique) || (empty($this->fieldMd5))) {
                //====================================================================//
                // Store MD5 of New Generated Field Data
                $this->fieldMd5 = $this->getFakeDataMd5($fakeData, $field);

                return $fakeData;
            }

            $fakeDataMd5 = $this->getFakeDataMd5($fakeData, $field);

            //====================================================================//
            //   Ensure Field Data was modified
            ++$try;
        } while (($this->fieldMd5 === $fakeDataMd5) && ($try < 5));

        //====================================================================//
        // Store MD5 of New Generated Field Data
        $this->fieldMd5 = $this->getFakeDataMd5($fakeData, $field);

        //====================================================================//
        // Return Generated Object Data
        return $fakeData;
    }

    /**
     * Generate Object Data Md5 Checksum to Ensure Data are different
     *
     * @param array $fakeData Faker Object Data Set
     * @param array $field    Current Tested Field (ArrayObject)
     *
     * @return string Md5 CheckSum
     */
    protected function getFakeDataMd5(array $fakeData, array $field): string
    {
        //====================================================================//
        // Filter data to focus on Tested Field
        $filteredData = $this->filterData($fakeData, array($field['id']));
        //====================================================================//
        // Data Block is Empty(i.e: ReadOnly Field)
        if (empty($filteredData)) {
            return md5(serialize($fakeData));
        }

        return md5(serialize($filteredData));
    }
}
