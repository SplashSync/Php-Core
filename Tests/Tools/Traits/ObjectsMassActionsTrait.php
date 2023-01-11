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

/**
 * Splash Test Tools - Objects Mass Actions Tests
 */
trait ObjectsMassActionsTrait
{
    /**
     * Select From Module Actions Instead of Service
     *
     * @var bool
     */
    protected bool $fromModule = false;

    /**
     * Number of Tested Objects Actions
     *
     * @var int
     */
    protected int $maxTested = 10;

    /**
     * Number of Objects in a Batch Request
     *
     * @var int
     */
    protected int $batchSize = 5;

    /**
     * Test Objects After Action?
     *
     * @var bool
     */
    protected bool $verify = true;

    /**
     * Storage for Tested Objects Ids
     *
     * @var array
     */
    protected array $objectsIds = array();

    /**
     * Storage for Tested Objects Set Data
     *
     * @var array
     */
    protected $inputData;

    /**
     * Storage for Tested Objects Get Data
     *
     * @var array
     */
    protected $outputData;

    /**
     * Add/Override Custom Objects Fields Data to Faker
     *
     * @var array
     */
    protected array $customFieldsData = array();

    /**
     * Number of Objects Before Actions
     *
     * @var int
     */
    protected int $countBefore = 0;

    /**
     * Number of Objects After Actions
     *
     * @var int
     */
    protected int $countAfter = 0;

    //==============================================================================
    //      COMPLETE TESTS EXECUTION FUNCTIONS
    //==============================================================================

    /**
     * Execute a Complete Mass Create/Update/Delete Test From Service
     *
     * @param string $sequence
     * @param string $objectType Splash Object Type Name
     * @param int    $max        Number of Objects to Test
     * @param bool   $verify     Shall we Verify Objects after Writing?
     * @param bool   $delete     Shall we Delete Objects after Writing?
     *
     * @throws Exception
     *
     * @return void
     */
    public function coreTestMassCreateUpdateDelete(
        string $sequence,
        string $objectType,
        int $max = 10,
        bool $verify = true,
        bool $delete = true
    ): void {
        //====================================================================//
        // Load Test Sequence
        $this->loadLocalTestSequence($sequence);
        //====================================================================//
        // Execute Mass Create Test with Verifications
        $this->coreTestMassCreate($objectType, $max, $verify);
        //====================================================================//
        // Execute Mass Update Test with Verifications
        $this->coreTestMassUpdate($objectType, $verify);
        if ($delete) {
            //====================================================================//
            // Execute Mass Delete Test with Verifications
            $this->coreTestMassDelete($objectType, $verify);
        }
    }

    /**
     * Execute a Complete Mass Create/Delete Test From Service
     *
     * @param string $sequence
     * @param string $objectType Splash Object Type Name
     * @param int    $maxTested  Number of Objects to Test
     * @param bool   $verify     Shall we Verify Objects after Writing?
     * @param bool   $delete     Shall we Delete Objects after Writing?
     *
     * @throws Exception
     *
     * @return void
     */
    public function coreTestMassCreateDelete(
        string $sequence,
        string $objectType,
        int $maxTested = 10,
        bool $verify = true,
        bool $delete = true
    ): void {
        //====================================================================//
        // Load Test Sequence
        $this->loadLocalTestSequence($sequence);
        //====================================================================//
        // Execute Mass Create Test with Verifications
        $this->coreTestMassCreate($objectType, $maxTested, $verify);
        if ($delete) {
            //====================================================================//
            // Execute Mass Delete Test with Verifications
            $this->coreTestMassDelete($objectType, $verify);
        }
    }

    /**
     * Execute a Complete Mass Create/Delete Test From Service
     *
     * @param string $sequence   Test Sequence Name
     * @param string $objectType Splash Object Type Name
     * @param int    $max        Number of Objects to Test
     * @param int    $batch      Number of Objects to send in Same Batch Request
     * @param bool   $verify     Shall we Verify Objects after Writing?
     * @param bool   $delete     Shall we Delete Objects after Writing?
     *
     * @throws Exception
     *
     * @return void
     */
    public function coreTestBatchCreateDelete(
        string $sequence,
        string $objectType,
        int $max = 10,
        int $batch = 5,
        bool $verify = true,
        bool $delete = true
    ): void {
        //====================================================================//
        // Load Test Sequence
        $this->loadLocalTestSequence($sequence);
        //====================================================================//
        // Execute Mass Create Test with Verifications
        $this->coreTestBatchCreate($objectType, $max, $batch, $verify);
        if ($delete) {
            //====================================================================//
            // Execute Mass Delete Test with Verifications
            $this->coreTestMassDelete($objectType, $verify);
        }
    }

    /**
     * Execute a Complete Mass Create/Delete Test From Service
     *
     * @param string $sequence   Test Sequence Name
     * @param string $objectType Splash Object Type Name
     * @param int    $max        Number of Objects to Test
     * @param int    $batch      Number of Objects to send in Same Batch Request
     * @param bool   $verify     Shall we Verify Objects after Writing?
     * @param bool   $delete     Shall we Delete Objects after Writing?
     *
     * @throws Exception
     *
     * @return void
     */
    public function coreTestBatchCreateUpdateDelete(
        string $sequence,
        string $objectType,
        int $max = 10,
        int $batch = 5,
        bool $verify = true,
        bool $delete = true
    ): void {
        //====================================================================//
        // Load Test Sequence
        $this->loadLocalTestSequence($sequence);
        //====================================================================//
        // Execute Mass Create Test with Verifications
        $this->coreTestBatchCreate($objectType, $max, $batch, $verify);
        //====================================================================//
        // Execute Mass Update Test with Verifications
        $this->coreTestBatchUpdate($objectType, $max, $batch, $verify);
        if ($delete) {
            //====================================================================//
            // Execute Mass Delete Test with Verifications
            $this->coreTestMassDelete($objectType, $verify);
        }
    }

    //==============================================================================
    //      MASS UNIT TESTS EXECUTION FUNCTIONS
    //      WE SEND MULTIPLE OBJECTS WITH SINGLE REQUESTS
    //==============================================================================

    /**
     * Execute Mass Create Test From Service
     *
     * @param string $objectType Splash Object Type Name
     * @param int    $maxTested  Number of Objects to Test
     * @param bool   $verify     Shall we Verify Objects after Writing?
     *
     * @throws Exception
     *
     * @return bool
     */
    protected function coreTestMassCreate(string $objectType, int $maxTested = 10, bool $verify = true): bool
    {
        $this->maxTested = $maxTested;
        $this->verify = $verify;

        //====================================================================//
        //   INIT & GENERATE DATA FOR OBJECTS
        //====================================================================//

        //====================================================================//
        //   Generate Dummy New Object Data (All RW & Tested Fields Only)
        $newData = $this->prepareForTesting($objectType);
        if (!$newData) {
            return true;
        }

        //====================================================================//
        //   MASS OBJECT CREATE TEST
        //====================================================================//
        for ($i = 1; $i <= $this->maxTested; $i++) {
            //====================================================================//
            // Setup Empty Object Id
            $this->objectsIds[$i] = false;
            //====================================================================//
            // Lock New Objects To Avoid Action Commit
            Splash::object($objectType)->lock();
            //====================================================================//
            // Select Test Mode
            if ($this->fromModule) {
                //====================================================================//
                //   Create a New Object From Module
                $this->objectsIds[$i] = Splash::object($objectType)->set(null, $this->inputData[$i]);
            } else {
                //====================================================================//
                //   Create a New Object From Service
                $this->objectsIds[$i] = $this->genericFastAction(
                    SPL_S_OBJECTS,
                    SPL_F_SET,
                    __METHOD__,
                    array('id' => null, 'type' => $objectType, 'fields' => $this->inputData[$i])
                );
            }
            //====================================================================//
            //   Verify Object Id Is Not Empty
            $this->assertNotEmpty($this->objectsIds[$i], 'Mass Create '.$i.': New Object Id is Empty');
            $this->assertIsString($this->objectsIds[$i], 'Mass Create '.$i.': New Object Id is Not a String');
            $this->assertEmpty(Splash::log()->err, 'Mass Create '.$i.': Errors Returned');
        }
        //====================================================================//
        // UnLock New Objects To Avoid Action Commit
        Splash::object($objectType)->unLock();

        //====================================================================//
        //   VERIFY OBJECTS DATA
        //====================================================================//

        $this->verifySetResponse($objectType, $this->objectsIds, $this->inputData, $this->maxTested);

        return true;
    }

    /**
     * Execute Mass Update Test From Service
     *
     * @param string $objectType Splash Object Type Name
     * @param bool   $verify     Shall we Verify Objects after Writing?
     *
     * @throws Exception
     *
     * @return bool
     */
    protected function coreTestMassUpdate(string $objectType, bool $verify = true): bool
    {
        //====================================================================//
        //   INIT & GENERATE DATA FOR OBJECTS
        //====================================================================//

        $this->verify = $verify;
        $this->assertNotEmpty($this->objectsIds, 'Objects Ids List is Empty, Please run Mass Create Test Before');

        //====================================================================//
        //   Generate Dummy New Object Data (All RW & Tested Fields Only)
        $this->originData = null;
        $newData = $this->prepareForTesting($objectType);
        if (false == $newData) {
            return true;
        }

        //====================================================================//
        //   MASS OBJECT UPDATE TEST
        //====================================================================//

        for ($i = 1; $i <= $this->maxTested; $i++) {
            //====================================================================//
            //   Verify Object Id Is Not Empty
            $this->assertNotEmpty($this->objectsIds[$i], 'Mass Update '.$i.': Input Object Id is Empty!!');
            //====================================================================//
            // Lock New Objects To Avoid Action Commit
            Splash::object($objectType)->lock($this->objectsIds[$i]);
            //====================================================================//
            // Select Test Mode
            if ($this->fromModule) {
                //====================================================================//
                //   Update a New Object From Module
                $response = Splash::object($objectType)->set($this->objectsIds[$i], $this->inputData[$i]);
            } else {
                //====================================================================//
                //   Update a New Object From Service
                $response = $this->genericFastAction(
                    SPL_S_OBJECTS,
                    SPL_F_SET,
                    __METHOD__,
                    array('id' => $this->objectsIds[$i], 'type' => $objectType, 'fields' => $this->inputData[$i])
                );
            }
            //====================================================================//
            //   Verify Object Id Is Not Empty
            $this->assertNotEmpty($response, 'Mass Update '.$i.': Response Object Id is Empty');
            $this->assertIsString($this->objectsIds[$i], 'Mass Update '.$i.': New Object Id is Not a String');
            $this->assertEquals($this->objectsIds[$i], $response, 'Mass Update '.$i.': Object Id is Different!!??');
            $this->assertEmpty(Splash::log()->err, 'Mass Create '.$i.': Errors Returned');
            //====================================================================//
            // UnLock New Objects To Avoid Action Commit
            Splash::object($objectType)->unLock($this->objectsIds[$i]);
        }

        //====================================================================//
        // Store Number of Objects After Test
        $this->countAfter = $this->countAvailableObjects($objectType);
        $this->assertEquals(
            $this->countBefore,
            $this->countAfter,
            "Number of Objects After tests is Different, did you created Duplicates??"
        );

        //====================================================================//
        //   VERIFY OBJECTS DATA
        //====================================================================//

        $this->verifySetResponse($objectType, $this->objectsIds, $this->inputData, 0);

        return true;
    }

    /**
     * Execute Mass Delete Test From Service
     *
     * @param string $objectType Splash Object Type Name
     * @param bool   $verify     Shall we Verify Objects after Writing?
     *
     * @throws Exception
     *
     * @return bool
     */
    protected function coreTestMassDelete(string $objectType, bool $verify = true): bool
    {
        //====================================================================//
        //   INIT & GENERATE DATA FOR OBJECTS
        //====================================================================//

        $this->verify = $verify;
        $this->assertNotEmpty($this->objectsIds, 'Objects Ids List is Empty, Please run Mass Create Test Before');

        //====================================================================//
        //   Generate Dummy New Object Data (All RW & Tested Fields Only)
        $newData = $this->prepareForTesting($objectType);
        if (false == $newData) {
            return true;
        }

        //====================================================================//
        //   MASS OBJECT DELETE TEST
        //====================================================================//

        for ($i = 1; $i <= $this->maxTested; $i++) {
            //====================================================================//
            // Lock New Objects To Avoid Action Commit
            Splash::object($objectType)->lock($this->objectsIds[$i]);
            //====================================================================//
            //   Verify Object Id Is Not Empty
            $this->assertNotEmpty($this->objectsIds[$i], 'Mass Update '.$i.': Input Object Id is Empty!!');
            //====================================================================//
            // Select Test Mode
            if ($this->fromModule) {
                //====================================================================//
                //   Delete Object From Module
                $response = Splash::object($objectType)->delete($this->objectsIds[$i]);
            } else {
                //====================================================================//
                //   Delete Object From Service
                $response = $this->genericFastAction(
                    SPL_S_OBJECTS,
                    SPL_F_DEL,
                    __METHOD__,
                    array('id' => $this->objectsIds[$i], 'type' => $objectType)
                );
            }
            //====================================================================//
            //   Verify Response Is True
            $this->assertNotEmpty($response, 'Mass Delete '.$i.': Did not Respond True');
            //====================================================================//
            // UnLock New Objects To Avoid Action Commit
            Splash::object($objectType)->unLock($this->objectsIds[$i]);
        }

        //====================================================================//
        //   VERIFY OBJECTS DATA
        //====================================================================//

        $this->verifyDeleteResponse($objectType, $this->objectsIds, -1 * $this->maxTested);

        return true;
    }

    //==============================================================================
    //      BATCH UNIT TESTS EXECUTION FUNCTIONS
    //      WE SEND OBJECTS WITH BATCH TASKS REQUESTS
    //==============================================================================

    /**
     * Execute Batch Create Test From Service
     *
     * @param string $objectType Splash Object Type Name
     * @param int    $maxTested  Number of Objects to Test
     * @param int    $batch      Number of Objects to send in Same Batch Request
     * @param bool   $verify     Shall we Verify Objects after Writing?
     *
     * @throws Exception
     *
     * @return bool
     */
    protected function coreTestBatchCreate(
        string $objectType,
        int $maxTested = 10,
        int $batch = 5,
        bool $verify = true
    ): bool {
        return $this->coreTestBatchAction($objectType, $maxTested, $batch, true, $verify);
    }

    /**
     * Execute Batch Update Test From Service
     *
     * @param string $objectType Splash Object Type Name
     * @param int    $maxTested  Number of Objects to Test
     * @param int    $batch      Number of Objects to send in Same Batch Request
     * @param bool   $verify     Shall we Verify Objects after Writing?
     *
     * @throws Exception
     *
     * @return bool
     */
    protected function coreTestBatchUpdate(
        string $objectType,
        int $maxTested = 10,
        int $batch = 5,
        bool $verify = true
    ): bool {
        return $this->coreTestBatchAction($objectType, $maxTested, $batch, false, $verify);
    }

    /**
     * BAse Execute Batch Action Test From Service
     *
     * @param string $objectType Splash Object Type Name
     * @param int    $max        Number of Objects to Test
     * @param int    $batch      Number of Objects to send in Same Batch Request
     * @param bool   $create     Shall we Create or Update Objects?
     * @param bool   $verify     Shall we Verify Objects after Writing?
     *
     * @throws Exception
     *
     * @return bool
     */
    protected function coreTestBatchAction(
        string $objectType,
        int $max = 10,
        int $batch = 5,
        bool $create = true,
        bool $verify = true
    ): bool {
        $this->maxTested = $max;
        $this->batchSize = $batch;
        $this->verify = $verify;

        //====================================================================//
        //   INIT & GENERATE DATA FOR OBJECTS
        //====================================================================//

        if (!$create) {
            $this->assertNotEmpty($this->objectsIds, 'Objects Ids List is Empty, Please run Create Test Before');
        }

        //====================================================================//
        //   Generate Dummy New Object Data (All RW & Tested Fields Only)
        $newData = $this->prepareForTesting($objectType);
        if (false == $newData) {
            return true;
        }

        //====================================================================//
        //   MASS OBJECT CREATE TEST
        //====================================================================//
        $buffer = array();
        for ($i = 1; $i <= $this->maxTested; $i++) {
            //====================================================================//
            // Setup Empty Object Id
            if ($create) {
                $this->objectsIds[$i] = false;
            }
            //====================================================================//
            // Add Task Parameters to Buffer
            $buffer[$i] = array(
                'id' => $create ? null : $this->objectsIds[$i],
                'type' => $objectType,
                'fields' => $this->inputData[$i]
            );
            //====================================================================//
            // Add Task Parameters to Buffer
            if ((0 != $i % $this->batchSize) && ($i != $this->maxTested)) {
                continue;
            }
            //====================================================================//
            //   Execute Create Batch Action From Service
            $this->doBatchAction($objectType, $buffer);
            $buffer = array();
        }

        //====================================================================//
        //   VERIFY OBJECTS DATA
        //====================================================================//

        $this->verifySetResponse(
            $objectType,
            $this->objectsIds,
            $this->inputData,
            $create ? $this->maxTested :0
        );

        return true;
    }

    /**
     * Do Batch Action
     *
     * @param string $objectType Splash Object Type Name
     * @param array  $buffer     Splash Tasks Data Buffer
     *
     * @throws Exception
     *
     * @return void
     */
    private function doBatchAction(string $objectType, array $buffer): void
    {
        //====================================================================//
        // Lock New Objects To Avoid Action Commit
        Splash::object($objectType)->lock();
        //====================================================================//
        //   Execute Create Batch Action From Service
        $response = $this->multipleAction(SPL_S_OBJECTS, SPL_F_SET, __METHOD__, $buffer);
        //====================================================================//
        //   Verify Batch was Successful
        $this->assertNotEmpty($response, 'Batch Action: Batch response is Empty');
        $this->assertEmpty(Splash::log()->err, 'Batch: Errors Returned');
        //====================================================================//
        //   Parse Batch Response
        foreach (array_keys($buffer) as $key => $index) {
            $this->objectsIds[$index] = $response[$key];
            //   Verify Object Id Is Not Empty
            $this->assertNotEmpty($this->objectsIds[$index], 'Batch : Returned Object Id is Empty');
        }
        //====================================================================//
        // UnLock New Objects To Avoid Action Commit
        Splash::object($objectType)->unLock();
    }

    //==============================================================================
    //      DATA VERIFICATION FUNCTIONS
    //==============================================================================

    /**
     * Get Total Count of Objects.
     *
     * @param string $objectType
     *
     * @throws Exception
     *
     * @return int
     */
    private function countAvailableObjects(string $objectType): int
    {
        //====================================================================//
        //   Read Objects List
        $objectsList = Splash::object($objectType)->objectsList();
        //====================================================================//
        //   Extract Objects Count
        if (!isset($objectsList["meta"]["total"])) {
            return 0;
        }

        return $objectsList["meta"]["total"];
    }

    /**
     * Verify Total Count of Objects.
     *
     * @param string $objectType    Object Type Name
     * @param int    $expectedDelta Expected Diff between Before & After Tests
     *
     * @throws Exception
     *
     * @return int
     */
    private function verifyAvailableObjects(string $objectType, int $expectedDelta = 0): int
    {
        //====================================================================//
        // Store Number of Objects After Test
        $this->countAfter = $this->countAvailableObjects($objectType);
        $this->assertEquals(
            $this->countBefore + $expectedDelta,
            $this->countAfter,
            "Objects count After|Before tests is wrong. Did you missed something?? Created Duplicates??"
        );

        //====================================================================//
        //   Read Objects List
        $objectsList = Splash::object($objectType)->objectsList();
        //====================================================================//
        //   Extract Objects Count
        if (!isset($objectsList["meta"]["total"])) {
            return 0;
        }

        return $objectsList["meta"]["total"];
    }

    /**
     * Verify Client Object Set Response.
     *
     * @param string $objectType    Object Type Name
     * @param array  $objectIds     Array Of Tested Ids
     * @param array  $expectedData  Array of Written Data
     * @param int    $expectedDelta Expected Diff between Before & After Tests
     *
     * @throws Exception
     *
     * @return void
     */
    private function verifySetResponse(
        string $objectType,
        array $objectIds,
        iterable $expectedData,
        int $expectedDelta = 0
    ): void {
        $this->verifyAvailableObjects($objectType, $expectedDelta);
        if (!$this->verify) {
            return;
        }

        for ($i = 1; $i <= $this->maxTested; $i++) {
            //====================================================================//
            //   Verify Object Id Is Not Empty
            $this->assertNotEmpty($objectIds[$i], 'Returned New Object Id is Empty');
            //====================================================================//
            //   Add Object Id to Created List
            $this->addTestedObject($objectType, $objectIds[$i]);
            //====================================================================//
            //   Verify Object Id Is in Right Format
            $this->assertTrue(
                is_numeric($objectIds[$i]) || is_string($objectIds[$i]),
                'New Object Id is not an Integer or a Strings'
            );
            //====================================================================//
            //   Read Object Data
            $currentData = Splash::object($objectType)
                ->get($objectIds[$i], $this->reduceFieldList($this->fields));
            $this->assertIsArray($currentData);
            //====================================================================//
            //   Verify Object Data are Ok
            $this->compareDataBlocks($this->fields, $expectedData[$i], $currentData, $objectType);
        }
    }

    /**
     * Verify Client Object Delete Response.
     *
     * @param string $objectType    Object Type Name
     * @param array  $objectIds     Array Of Tested Ids
     * @param int    $expectedDelta Expected Diff between Before & After Tests
     *
     * @throws Exception
     *
     * @return void
     */
    private function verifyDeleteResponse(string $objectType, array $objectIds, int $expectedDelta = 0): void
    {
        $this->verifyAvailableObjects($objectType, $expectedDelta);
        if (!$this->verify) {
            return;
        }

        for ($i = 1; $i <= $this->maxTested; $i++) {
            //====================================================================//
            // Lock New Objects To Avoid Action Commit
            Splash::object($objectType)->lock($objectIds[$i]);
            //====================================================================//
            //   Verify Repeating Delete as Same Result
            $repeatedResponse = Splash::object($objectType)->delete($objectIds[$i]);
            $this->assertTrue(
                $repeatedResponse,
                'Object Repeated Delete, Must return True even if Object Already Deleted.'
            );
            //====================================================================//
            //   Verify Object not Present anymore
            $fields = $this->reduceFieldList(Splash::object($objectType)->fields(), true, false);
            $getResponse = Splash::object($objectType)->get($objectIds[$i], $fields);
            $this->assertNull($getResponse, 'Object Not Delete, I can still read it!!');
        }
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
     *
     * @throws Exception
     *
     * @return null|array Generated Data Block or False if not Allowed
     */
    private function prepareForTesting(string $objectType): ?array
    {
        //====================================================================//
        //   Verify Test is Required
        if (!$this->verifyTestIsAllowed($objectType)) {
            return null;
        }
        //====================================================================//
        // Generate Objects Data Sets
        $this->inputData = array();
        for ($i = 1; $i <= $this->maxTested; $i++) {
            $this->originData = null;
            $this->inputData[$i] = array_replace(
                $this->generateObjectData($objectType),
                $this->customFieldsData
            );
        }
        //====================================================================//
        // Store Number of Objects Before Test
        $this->countBefore = $this->countAvailableObjects($objectType);
        //====================================================================//
        // Return Generated Object Data
        return $this->inputData;
    }

    /**
     * Verify if Test is Allowed for This Field
     *
     * @param string $objectType
     *
     * @throws Exception
     *
     * @return bool
     */
    private function verifyTestIsAllowed(string $objectType): bool
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

        return true;
    }

    /**
     * Generate Fake Object Data
     * -> This Function uses Preloaded Fields
     * -> If Md5 provided, check Current Field was Modified
     *
     * @param string $objectType Current Object Type
     *
     * @return null|array Generated Data Block or null if not Allowed
     */
    private function generateObjectData(string $objectType): ?array
    {
        //====================================================================//
        // Generate Required Fields List
        $this->fields = $this->fakeFieldsList($objectType, null, true, false);

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
            if (empty($this->fieldMd5)) {
                //====================================================================//
                // Store MD5 of New Generated Field Data
                $this->fieldMd5 = md5(serialize($fakeData));

                return $fakeData;
            }

            $fakeDataMd5 = md5(serialize($fakeData));

            //====================================================================//
            //   Ensure Field Data was modified
            ++$try;
        } while (($this->fieldMd5 === $fakeDataMd5) && ($try < 5));

        //====================================================================//
        // Store MD5 of New Generated Field Data
        $this->fieldMd5 = md5(serialize($fakeData));

        //====================================================================//
        // Return Generated Object Data
        return $fakeData;
    }
}
