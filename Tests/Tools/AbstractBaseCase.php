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

use ArrayObject;
use Exception;
use Splash\Client\Splash;

/**
 * Abstract Base Class for Splash Modules Tests
 */
abstract class AbstractBaseCase extends TestCase
{
    use Traits\SettingsTrait;
    use Traits\ObjectsValidatorTrait;
    use Traits\ObjectsAssertionsTrait;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        //====================================================================//
        // BOOT or REBOOT MODULE
        Splash::reboot();
    }

    /**
     * Check if Sequence Name Is to be tested or Not
     *
     * @param string $sequenceName Sequence Name
     *
     * @return bool
     */
    public static function isAllowedSequence(string $sequenceName): bool
    {
        static $allowed;

        //====================================================================//
        //   Filter Tested Sequence Name  =>> Skip
        if (!isset($allowed)) {
            $sequences = Splash::constant("SPLASH_SEQUENCE") ?? Splash::env("SPLASH_SEQUENCE");
            if ($sequences) {
                $allowed = (array) explode(",", $sequences);
            } else {
                $allowed = array();
            }
        }

        return (empty($allowed) || in_array($sequenceName, $allowed, true));
    }

    /**
     * Check if Object Type Is to be tested or Not
     *
     * @param string $objectType Object Type Name
     *
     * @throws Exception
     *
     * @return bool
     */
    public static function isAllowedObjectType(string $objectType): bool
    {
        $types = Splash::constant("SPLASH_TYPES") ?? Splash::env("SPLASH_TYPES");
        //====================================================================//
        //   Filter Tested Object Types  =>> Skip
        if ($types) {
            $allowed = (array) explode(",", $types);
            if (!in_array($objectType, $allowed, true)) {
                return false;
            }
        }
        //====================================================================//
        //   If Object Type Is Disabled Type  =>> Skip
        if (Splash::object($objectType)->isDisabled()) {
            return false;
        }

        return true;
    }

    /**
     * Check if Object Field ID Is to be tested or Not
     *
     * @param string $identifier Object Field Identifier
     *
     * @return bool
     */
    public static function isAllowedObjectField(string $identifier): bool
    {
        static $allowed;

        //====================================================================//
        //   Filter Tested Object Fields  =>> Skip
        if (!isset($allowed)) {
            $fields = Splash::constant("SPLASH_FIELDS") ?? Splash::env("SPLASH_FIELDS");
            if ($fields) {
                $allowed = (array) explode(",", $fields);
            } else {
                $allowed = array();
            }
        }

        return (empty($allowed) || in_array($identifier, $allowed, true));
    }

    /**
     * GENERATE FAKE SPLASH SERVER HOST URL
     *
     * @see SERVER_NAME parameter that must be defined in PhpUnit Configuration File
     *
     * @return string Local Server Soap Url
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function getLocalServerSoapUrl(): string
    {
        //====================================================================//
        // Get ServerInfos from WebService Component
        $infos = Splash::ws()->getServerInfos();

        //====================================================================//
        //   SAFETY CHECK
        //====================================================================//

        //====================================================================//
        //   Verify ServerPath is  Not Empty
        $this->assertNotEmpty(
            $infos["ServerPath"],
            "Splash Core Module was unable to detect your Soap root Server Path. 
            Verify you do not overload 'ServerPath' parameter in your local configuration."
        );
        //====================================================================//
        //   Verify ServerPath is  Not Empty
        $this->assertTrue(
            isset($_SERVER['SERVER_NAME']),
            "'SERVER_NAME' not defined in your PhpUnit XML configuration. 
            Please define '<server name=\"SERVER_NAME\" value=\"http://localhost/Path/to/Your/Server\"/>'"
        );
        $this->assertNotEmpty(
            $_SERVER['SERVER_NAME'],
            "'SERVER_NAME' not defined in your PhpUnit XML configuration. 
            Please define '<server name=\"SERVER_NAME\" value=\"http://localhost/Path/to/Your/Server\"/>'"
        );

        //====================================================================//
        // GENERATE FAKE SPLASH SERVER HOST URL
        return $_SERVER['SERVER_NAME'].$infos["ServerPath"];
    }

    /**
     * Verify Response Is Valid
     *
     * @param null|string $response WebService Raw Response Block
     * @param null|array  $config   WebService Request Configuration
     *
     * @return array
     */
    public function checkResponse(?string $response, array $config = null): array
    {
        //====================================================================//
        // RESPONSE BLOCK IS NOT EMPTY
        $this->assertNotEmpty($response, "Response Block is Empty");
        $this->assertIsString($response, "Response Block is Null");
        //====================================================================//
        // DECODE BLOCK
        $data = Splash::ws()->unPack($response);
        //====================================================================//
        // CHECK RESPONSE DATA
        $this->assertNotEmpty($data, "Response Data is Empty or Malformed");
        $this->assertIsArray($data, "Response Data is Not an Array");
        $this->assertArrayHasKey("result", $data, "Request Result is Missing");

        //====================================================================//
        // CHECK RESPONSE LOG
        if (isset($data['log'])) {
            $this->checkResponseLog($data['log'], $config);
        }

        //====================================================================//
        // CHECK RESPONSE SERVER INFOS
        if (isset($data['server'])) {
            $this->checkResponseServer($data['server']);
        }

        //====================================================================//
        // CHECK RESPONSE TASKS RESULTS
        if (isset($data['tasks'])) {
            $this->checkResponseTasks($data['tasks'], $config);
        }

        //====================================================================//
        // CHECK RESPONSE RESULT
        if (empty($data['result'])) {
            print_r($data);
        }
        $this->assertNotEmpty($data['result'], "Request Result is not True, Why??");

        return $data;
    }

    /**
     * Verify Response Log Is Valid
     *
     * @param array      $logs   WebService Log Array
     * @param null|array $config WebService Request Configuration
     *
     * @return void
     */
    public function checkResponseLog(array $logs, array $config = null): void
    {
        //====================================================================//
        // SERVER LOG ARRAY FORMAT
        $this->assertIsArray($logs, "Response Log is Not an Array");

        //====================================================================//
        // SERVER LOGS MESSAGES FORMAT
        $this->checkResponseLogArray($logs, 'err', "Error");
        $this->checkResponseLogArray($logs, 'war', "Warning");
        $this->checkResponseLogArray($logs, 'msg', "Message");
        $this->checkResponseLogArray($logs, 'deb', "Debug Trace");

        //====================================================================//
        // UNEXPECTED SERVER LOG ITEMS
        foreach (array_keys($logs) as $key) {
            $this->assertTrue(
                in_array($key, array("err", "msg", "war", "deb"), true),
                "Received Unexpected Log Messages. ( Data->log->".$key.")"
            );
        }

        //====================================================================//
        // SERVER LOG With Silent Option Activated
        if ($config && isset($config['silent'])) {
            $this->assertEmpty($logs['war'], "Requested Silent operation but Received Warnings, Why??");
            $this->assertEmpty($logs['msg'], "Requested Silent operation but Received Messages, Why??");
            $this->assertEmpty($logs['deb'], "Requested Silent operation but Received Debug Traces, Why??");
        }

        //====================================================================//
        // SERVER LOG Without Debug Option Activated
        if ($config && !isset($config['debug'])) {
            $this->assertEmpty($logs['deb'], "Requested Non Debug operation but Received Debug Traces, Why??");
        }

        //====================================================================//
        //   Extract Logs From Response
        Splash::log()->merge($logs);
    }

    /**
     * Verify Response Log Is Valid
     *
     * @param array  $logs WebService Log Array
     * @param string $type Log Key
     * @param string $name Log Type Name
     *
     * @return void
     */
    public function checkResponseLogArray(array $logs, string $type, string $name)
    {
        if (empty($logs[$type])) {
            return;
        }
        $this->assertIsArray($logs[$type], "Logger ".$name." List is Not an Array");
        foreach ($logs[$type] as $message) {
            $this->assertTrue(
                (is_scalar($message) || is_null($message)),
                $name." is Not a string. (".print_r($message, true).")"
            );
        }
    }

    /**
     * Verify Response Server Infos Are Valid
     *
     * @param array $server WebService Server Infos Array
     *
     * @return void
     */
    public function checkResponseServer(array $server)
    {
        //====================================================================//
        // SERVER Informations  => Available
        $this->assertArrayHasKey("ServerHost", $server, "Server Info (ServerHost) is Missing");
        $this->assertArrayHasKey("ServerPath", $server, "Server Info (ServerPath) is Missing");
        $this->assertArrayHasKey("ServerType", $server, "Server Info (ServerType) is Missing");
        $this->assertArrayHasKey("ServerVersion", $server, "Server Info (ServerVersion) is Missing");
        $this->assertArrayHasKey("ServerAddress", $server, "Server Info (ServerAddress) is Missing");

        //====================================================================//
        // SERVER Informations  => Not Empty
        $this->assertNotEmpty($server["ServerHost"], "Server Info (ServerHost) is Empty");
        $this->assertNotEmpty($server["ServerPath"], "Server Info (ServerPath) is Empty");
        $this->assertNotEmpty($server["ServerType"], "Server Info (ServerType) is Empty");
        $this->assertNotEmpty($server["ServerVersion"], "Server Info (ServerVersion) is Empty");
    }

    /**
     * Verify Response Tasks Results are Valid
     *
     * @param array      $tasks  WebService Server Tasks Results Array
     * @param null|array $config WebService Request Configuration
     *
     * @return void
     */
    public function checkResponseTasks(array $tasks, array $config = null)
    {
        //====================================================================//
        // TASKS RESULTS ARRAY FORMAT
        $this->assertIsArray($tasks, "Response Tasks Result is Not an Array");

        foreach ($tasks as $task) {
            //====================================================================//
            // TASKS Results  => Available
            $this->assertArrayHasKey("id", $task, "Task Results => Task Id is Missing");
            $this->assertArrayHasKey("name", $task, "Task Results => Name is Missing");
            $this->assertArrayHasKey("desc", $task, "Task Results => Description is Missing");
            $this->assertArrayHasKey("result", $task, "Task Results => Task Result is Missing");
            $this->assertArrayHasKey("data", $task, "Task Results => Data is Missing");

            //====================================================================//
            // TASKS Results  => Not Empty
            $this->assertNotEmpty($task["id"], "Task Results => Task Id is Empty");
            $this->assertNotEmpty($task["name"], "Task Results => Name is Empty");
            $this->assertNotEmpty($task["desc"], "Task Results => Description is Empty");

            //====================================================================//
            // TASKS Delay Data
            if ($config && !isset($config['trace'])) {
                $this->assertArrayHasKey("delayms", $task, "Task Results => Trace requested but DelayMs is Missing");
                $this->assertArrayHasKey("delaystr", $task, "Task Results => Trace requested but DelayStr is Missing");
                $this->assertNotEmpty($task["delayms"], "Task Results => Trace requested but DelayMs is Empty");
                $this->assertNotEmpty($task["delaystr"], "Task Results => Trace requested but DelayStr is Empty");
            }
        }
    }

    //====================================================================//
    //   Data Provider Functions
    //====================================================================//

    /**
     * Data Provider : Simple Tests Sequences
     *
     * @throws Exception
     *
     * @return array
     */
    public function sequencesProvider(): array
    {
        $result = array();
        $testSequences = array("None");

        self::setUp();

        //====================================================================//
        // Check if Local Tests Sequences are defined
        if (method_exists(Splash::local(), "TestSequences")) {
            $testSequences = Splash::local()->testSequences("List");
        }
        //====================================================================//
        // Prepare Sequences Array
        foreach ($testSequences as $testSequence) {
            //====================================================================//
            //   Filter Tested Sequences  =>> Skip
            if (!self::isAllowedSequence($testSequence)) {
                continue;
            }
            $dataSetName = '['.$testSequence."]";
            $result[$dataSetName] = array($testSequence);
        }

        self::tearDown();

        return $result;
    }

    /**
     * Load or Reload Tests Parameters for Current Test Sequence
     *
     * @throws Exception
     *
     * @return void
     */
    protected function loadLocalTestParameters(): void
    {
        //====================================================================//
        // Safety Check
        if (!method_exists(Splash::local(), "TestParameters")) {
            return;
        }
        //====================================================================//
        // Read Local Parameters
        $localTestSettings = Splash::local()->testParameters();

        //====================================================================//
        // Import Local Parameters
        foreach ($localTestSettings as $key => $value) {
            $this->settings[$key] = $value;
        }
    }

    /**
     * Configure Environment for this Test Sequence
     *
     * @param string $testSequence
     *
     * @throws Exception
     *
     * @return void
     */
    protected function loadLocalTestSequence(string $testSequence): void
    {
        //====================================================================//
        // Check if Local Tests Sequences are defined
        if (!method_exists(Splash::local(), "TestSequences")) {
            return;
        }
        //====================================================================//
        // Setup Test Sequence
        Splash::local()->testSequences($testSequence);

        //====================================================================//
        // Reload Local Tests Parameters
        $this->loadLocalTestParameters();
    }

    /**
     * Perform Generic Server Side Action
     *
     * @param string $service     Webservice Service Name
     * @param string $action      Webservice Action Name
     * @param string $description Task Description
     * @param array  $parameters  Task Parameters
     *
     * @return array<string, null|array<string, null|array|scalar>|scalar>
     */
    protected function genericAction(
        string $service,
        string $action,
        string $description,
        array $parameters = array(true)
    ): array {
        //====================================================================//
        //   Prepare Request Data
        Splash::ws()->addTask($action, $parameters, $description);
        //====================================================================//
        //   Execute Action From Splash Server to Module
        $response = Splash::ws()->simulate($service);
        //====================================================================//
        //   Check Response
        $data = $this->checkResponse((string) $response);
        //====================================================================//
        //   Extract Task Result
        $task = array_shift($data['tasks']);
        $this->assertArrayHasKey("data", $task);
        $this->assertIsArray($task["data"]);
        //====================================================================//
        //   Turn On Output Buffering Again
        ob_start();

        return $task["data"];
    }

    /**
     * Perform Generic Server Side Action
     *
     * @param string $service     Webservice Service Name
     * @param string $action      Webservice Action Name
     * @param string $description Task Description
     * @param array  $parameters  Task Parameters
     *
     * @return string
     */
    protected function genericStringAction(
        string $service,
        string $action,
        string $description,
        array $parameters = array(true)
    ): string {
        //====================================================================//
        //   Prepare Request Data
        Splash::ws()->addTask($action, $parameters, $description);
        //====================================================================//
        //   Execute Action From Splash Server to Module
        $response = Splash::ws()->simulate($service);
        //====================================================================//
        //   Check Response
        $data = $this->checkResponse((string) $response);
        //====================================================================//
        //   Extract Task Result
        $task = array_shift($data['tasks']);
        $this->assertArrayHasKey("data", $task);
        $this->assertIsScalar($task["data"]);
        //====================================================================//
        //   Turn On Output Buffering Again
        ob_start();

        return (string) $task["data"];
    }

    /**
     * Perform Multiple Server Side Action
     *
     * @param string $service         Webservice Service Name
     * @param string $action          Webservice Action Name
     * @param string $description     Task Description
     * @param array  $tasksParameters Array of Task Parameters
     *
     * @return array
     */
    protected function multipleAction(
        string $service,
        string $action,
        string $description,
        array $tasksParameters = array()
    ): array {
        //====================================================================//
        //   Prepare Request Data
        foreach ($tasksParameters as $parameters) {
            Splash::ws()->addTask($action, $parameters, $description);
        }
        //====================================================================//
        //   Execute Action From Splash Server to Module
        $response = Splash::ws()->simulate($service);
        //====================================================================//
        //   Check Response
        $data = $this->checkResponse((string) $response);
        //====================================================================//
        //   Extract Task Results
        $results = array();
        do {
            $task = Splash::ws()->getNextTask($data);
            if (!$task || !isset($task['data'])) {
                continue;
            }
            $results[] = $task['data'];
        } while (!empty($task));
        //====================================================================//
        //   Turn On Output Buffering Again
        ob_start();

        return $results;
    }

    /**
     * Perform generic Server Side Action
     *
     * @param string $service     Webservice Service Name
     * @param string $action      Webservice Action Name
     * @param string $description Task Description
     * @param array  $parameters  Task Parameters
     *
     * @return void
     */
    protected function genericErrorAction(
        string $service,
        string $action,
        string $description,
        array $parameters = array(true)
    ): void {
        //====================================================================//
        //   Prepare Request Data
        Splash::ws()->addTask($action, $parameters, $description);
        //====================================================================//
        //   Execute Action From Splash Server to Module
        $response = Splash::ws()->simulate($service);
        //====================================================================//
        // RESPONSE BLOCK IS NOT EMPTY
        $this->assertNotEmpty($response, "Response Block is Empty");
        //====================================================================//
        // DECODE BLOCK
        $data = Splash::ws()->unPack((string) $response);
        //====================================================================//
        // CHECK RESPONSE DATA
        $this->assertNotEmpty($data, "Response Data is Empty or Malformed");
        $this->assertIsArray($data, "Response Data is Not an Array");
        $this->assertArrayHasKey("result", $data, "Request Result is Missing");
        $this->assertEmpty($data['result'], "Expect Errors but Request Result is True, Why??");
        //====================================================================//
        //   Turn On Output Buffering Again
        ob_start();
    }

    /**
     * Perform Generic Server Side Action (Without Verifications)
     *
     * @param string $service     Webservice Service Name
     * @param string $action      Webservice Action Name
     * @param string $description Task Description
     * @param array  $parameters  Task Parameters
     *
     * @return null|array|bool|string
     */
    protected function genericFastAction(
        string $service,
        string $action,
        string $description,
        array $parameters = array(true)
    ) {
        //====================================================================//
        //   Prepare Request Data
        Splash::ws()->addTask($action, $parameters, $description);
        //====================================================================//
        //   Execute Action From Splash Server to Module
        $response = Splash::ws()->simulate($service);
        if (null === $response) {
            return null;
        }
        //====================================================================//
        // DECODE BLOCK
        $data = Splash::ws()->unPack($response);
        //====================================================================//
        // CHECK RESPONSE DATA
        $this->assertNotEmpty($data, "Response Data is Empty or Malformed");
        $this->assertIsArray($data, "Response Data is Not an Array");
        $this->assertArrayHasKey("result", $data, "Request Result is Missing");
        $this->assertNotEmpty($data['result'], "Request Result is not True, Why??");
        //====================================================================//
        //   Extract Task Result
        $task = array_shift($data['tasks']);
        //====================================================================//
        //   Turn On Output Buffering Again
        ob_start();

        return $task["data"];
    }

    /**
     * Normalize Array or ArrayObject to Array
     *
     * @param null|array|ArrayObject|string $data
     *
     * @throws Exception
     *
     * @return array
     */
    protected static function toArray($data): array
    {
        if (($data instanceof ArrayObject)) {
            return $data->getArrayCopy();
        }
        if (is_null($data) || ("" === $data)) {
            return array();
        }

        if (is_scalar($data)) {
            throw new Exception("This Data Should a Splash Array Data");
        }

        return $data;
    }

    /**
     * Normalize Array or ArrayObject to Array
     *
     * @param null|array|ArrayObject|string $data
     *
     * @throws Exception
     *
     * @return array
     */
    protected static function toArrayRecursive($data): array
    {
        $data = self::toArray($data);
        foreach ($data as &$item) {
            $item = ($item instanceof ArrayObject) ? self::toArray($item) : $item;
        }

        return $data;
    }
}
