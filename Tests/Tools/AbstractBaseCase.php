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

namespace Splash\Tests\Tools;

use ArrayObject;
use Exception;
use Splash\Client\Splash;

/**
 * Abstract Base Class for Splash Modules Tests
 */
abstract class AbstractBaseCase extends TestCase
{
    use \Splash\Tests\Tools\Traits\SettingsTrait;
    use \Splash\Tests\Tools\Traits\ObjectsValidatorTrait;
    use \Splash\Tests\Tools\Traits\ObjectsAssertionsTrait;

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
    public static function isAllowedSequence($sequenceName)
    {
        //====================================================================//
        //   Filter Tested Sequence Name  =>> Skip
        if (defined("SPLASH_SEQUENCE") && is_string(SPLASH_SEQUENCE) && !empty(explode(",", SPLASH_SEQUENCE))) {
            if (!in_array($sequenceName, explode(",", SPLASH_SEQUENCE), true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if Object Type Is to be tested or Not
     *
     * @param string $objectType Object Type Name
     *
     * @return bool
     */
    public static function isAllowedObjectType($objectType)
    {
        //====================================================================//
        //   Filter Tested Object Types  =>> Skip
        if (defined("SPLASH_TYPES") && is_string(SPLASH_TYPES) && !empty(explode(",", SPLASH_TYPES))) {
            if (!in_array($objectType, explode(",", SPLASH_TYPES), true)) {
                return false;
            }
        }
        //====================================================================//
        //   If Object Type Is Disabled Type  =>> Skip
        if (Splash::object($objectType)->getIsDisabled()) {
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
    public static function isAllowedObjectField($identifier)
    {
        //====================================================================//
        //   Filter Tested Object Fields  =>> Skip
        if (defined("SPLASH_FIELDS") && is_string(SPLASH_FIELDS) && !empty(explode(",", SPLASH_FIELDS))) {
            if (!in_array($identifier, explode(",", SPLASH_FIELDS), true)) {
                return false;
            }
        }

        return true;
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
    public function getLocalServerSoapUrl()
    {
        //====================================================================//
        // Get ServerInfos from WebService Componant
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
        $soapUrl = $_SERVER['SERVER_NAME'].$infos["ServerPath"];

        return  $soapUrl;
    }

    /**
     * Verify Response Is Valid
     *
     * @param string      $response WebService Raw Response Block
     * @param ArrayObject $config   WebService Request Configuration
     *
     * @return ArrayObject
     */
    public function checkResponse($response, $config = null)
    {
        //====================================================================//
        // RESPONSE BLOCK IS NOT EMPTY
        $this->assertNotEmpty($response, "Response Block is Empty");
        //====================================================================//
        // DECODE BLOCK
        $data = Splash::ws()->unPack($response);
        //====================================================================//
        // CHECK RESPONSE DATA
        $this->assertNotEmpty($data, "Response Data is Empty or Malformed");
        $this->assertInstanceOf("ArrayObject", $data, "Response Data is Not an ArrayObject");
        $this->assertArrayHasKey("result", $data, "Request Result is Missing");

        //====================================================================//
        // CHECK RESPONSE LOG
        if (isset($data->log)) {
            $this->checkResponseLog($data->log, $config);
        }

        //====================================================================//
        // CHECK RESPONSE SERVER INFOS
        if (isset($data->server)) {
            $this->checkResponseServer($data->server);
        }

        //====================================================================//
        // CHECK RESPONSE TASKS RESULTS
        if (isset($data->tasks)) {
            $this->checkResponseTasks($data->tasks, $config);
        }

        //====================================================================//
        // CHECK RESPONSE RESULT
        if (empty($data->result)) {
            print_r($data);
        }
        $this->assertNotEmpty($data->result, "Request Result is not True, Why??");

        return $data;
    }

    /**
     * Verify Response Log Is Valid
     *
     * @param ArrayObject $logs   WebService Log Array
     * @param ArrayObject $config WebService Request Configuration
     *
     * @return void
     */
    public function checkResponseLog($logs, $config = null)
    {
        //====================================================================//
        // SERVER LOG ARRAY FORMAT
        $this->assertInstanceOf("ArrayObject", $logs, "Response Log is Not an ArrayObject");

        //====================================================================//
        // SERVER LOGS MESSAGES FORMAT
        $this->checkResponseLogArray($logs, 'err', "Error");
        $this->checkResponseLogArray($logs, 'war', "Warning");
        $this->checkResponseLogArray($logs, 'msg', "Message");
        $this->checkResponseLogArray($logs, 'deb', "Debug Trace");

        //====================================================================//
        // UNEXPECTED SERVER LOG ITEMS
        foreach (array_keys($logs->getArrayCopy()) as $key) {
            $this->assertTrue(
                in_array($key, array("err", "msg", "war", "deb"), true),
                "Received Unexpected Log Messages. ( Data->log->".$key.")"
            );
        }

        //====================================================================//
        // SERVER LOG With Silent Option Activated
        if (($config instanceof ArrayObject) && isset($config->silent)) {
            $this->assertEmpty($logs->war, "Requested Silent operation but Received Warnings, Why??");
            $this->assertEmpty($logs->msg, "Requested Silent operation but Received Messages, Why??");
            $this->assertEmpty($logs->deb, "Requested Silent operation but Received Debug Traces, Why??");
        }

        //====================================================================//
        // SERVER LOG Without Debug Option Activated
        if (($config instanceof ArrayObject) && !isset($config->debug)) {
            $this->assertEmpty($logs->deb, "Requested Non Debug operation but Received Debug Traces, Why??");
        }

        //====================================================================//
        //   Extract Logs From Response
        Splash::log()->merge($logs);
    }
    /**
     * Verify Response Log Is Valid
     *
     * @param ArrayObject $logs WebService Log Array
     * @param string      $type Log Key
     * @param string      $name Log Type Name
     *
     * @return void
     */
    public function checkResponseLogArray($logs, $type, $name)
    {
        if (!isset($logs->{$type}) || empty($logs->{$type})) {
            return;
        }

        //====================================================================//
        // SERVER LOG FORMAT
        $this->assertInstanceOf("ArrayObject", $logs->{$type}, "Logger ".$name." List is Not an ArrayObject");
        foreach ($logs->{$type} as $message) {
            $this->assertTrue(
                (is_scalar($message) || is_null($message)),
                $name." is Not a string. (".print_r($message, true).")"
            );
        }
    }

    /**
     * Verify Response Server Infos Are Valid
     *
     * @param ArrayObject $server WebService Server Infos Array
     *
     * @return void
     */
    public function checkResponseServer($server)
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
     * @param ArrayObject $tasks  WebService Server Tasks Results Array
     * @param ArrayObject $config WebService Request Configuration
     *
     * @return void
     */
    public function checkResponseTasks($tasks, $config = null)
    {
        //====================================================================//
        // TASKS RESULTS ARRAY FORMAT
        $this->assertInstanceOf("ArrayObject", $tasks, "Response Tasks Result is Not an ArrayObject");

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
            if (($config instanceof ArrayObject) && !isset($config->trace)) {
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
     * Data Privider : Simple Tests Sequences
     *
     * @return array
     */
    public function sequencesProvider()
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
     * @return void
     */
    protected function loadLocalTestParameters()
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
        // Validate Local Parameters
        if (!Splash::validate()->isValidLocalTestParameterArray($localTestSettings)) {
            return;
        }

        //====================================================================//
        // Import Local Parameters
        foreach ($localTestSettings as $key => $value) {
            $this->settings[$key] = $value;
        }
    }

    /**
     * Configure Environement for this Test Sequence
     *
     * @param string $testSequence
     *
     * @return void
     */
    protected function loadLocalTestSequence($testSequence)
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
     * @return ArrayObject|bool|string
     */
    protected function genericAction($service, $action, $description, array $parameters = array(true))
    {
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
        if (is_a($data->tasks, "ArrayObject")) {
            $data->tasks = $data->tasks->getArrayCopy();
        }
        $task = array_shift($data->tasks);

        //====================================================================//
        //   Turn On Output Buffering Again
        ob_start();

        return $task["data"];
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
    protected function multipleAction($service, $action, $description, array $tasksParameters = array())
    {
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
            $task = Splash::ws()->getNextResult($data);
            if (!$task) {
                continue;
            }
            $results[] = ($task instanceof ArrayObject) ? $task->getArrayCopy() : $task;
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
     * @return ArrayObject
     */
    protected function genericErrorAction($service, $action, $description, array $parameters = array(true))
    {
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
        $this->assertInstanceOf("ArrayObject", $data, "Response Data is Not an ArrayObject");
        $this->assertArrayHasKey("result", $data, "Request Result is Missing");
        $this->assertEmpty($data->result, "Expect Errors but Request Result is True, Why??");

        //====================================================================//
        //   Extract Task Result
        if (is_a($data->tasks, "ArrayObject")) {
            $data->tasks = $data->tasks->getArrayCopy();
        }
        $task = array_shift($data->tasks);

        //====================================================================//
        //   Turn On Output Buffering Again
        ob_start();

        return $task["data"];
    }

    /**
     * Perform Generic Server Side Action (Without Verifications)
     *
     * @param string $service     Webservice Service Name
     * @param string $action      Webservice Action Name
     * @param string $description Task Description
     * @param array  $parameters  Task Parameters
     *
     * @return ArrayObject|bool|string
     */
    protected function genericFastAction($service, $action, $description, array $parameters = array(true))
    {
        //====================================================================//
        //   Prepare Request Data
        Splash::ws()->addTask($action, $parameters, $description);
        //====================================================================//
        //   Execute Action From Splash Server to Module
        $response = Splash::ws()->simulate($service);
        if (false === $response) {
            return false;
        }
        //====================================================================//
        // DECODE BLOCK
        $data = Splash::ws()->unPack($response);
        //====================================================================//
        //   Extract Task Result
        if (is_a($data->tasks, "ArrayObject")) {
            $data->tasks = $data->tasks->getArrayCopy();
        }
        $task = array_shift($data->tasks);
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
    protected static function toArray($data)
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
}
