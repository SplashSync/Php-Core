<?php

namespace Splash\Tests\Tools;

use ArrayObject;

use Splash\Tests\Tools\TestCase;
    
use Splash\Client\Splash;

/**
 * @abstract    Abstract Base Class for Splash Modules Tests
 */
abstract class AbstractBaseCase extends TestCase
{
    use \Splash\Tests\Tools\Traits\ObjectsValidatorTrait;
    use \Splash\Tests\Tools\Traits\ObjectsAssertionsTrait;
        
    protected function setUp()
    {
        parent::setUp();
        
        //====================================================================//
        // BOOT or REBOOT MODULE
        Splash::reboot();
    }

    /**
     * @abstract        GENERATE FAKE SPLASH SERVER HOST URL
     *
     * @see             SERVER_NAME parameter that must be defined in PhpUnit Configuration File
     *
     * @return string   Local Server Soap Url
     * 
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function getLocalServerSoapUrl()
    {
        //====================================================================//
        // Get ServerInfos from WebService Componant
        $Infos = Splash::ws()->getServerInfos();
        
        //====================================================================//
        //   SAFETY CHECK
        //====================================================================//

        //====================================================================//
        //   Verify ServerPath is  Not Empty
        $this->assertNotEmpty($Infos["ServerPath"], "Splash Core Module was unable to detect your Soap root Server Path. Verify you do not overload 'ServerPath' parameter in your local confirguration.");
        //====================================================================//
        //   Verify ServerPath is  Not Empty
        $this->assertTrue(isset($_SERVER['SERVER_NAME']), "'SERVER_NAME' not defined in your PhpUnit XML configuration. Please define '<server name=\"SERVER_NAME\" value=\"http://localhost/Path/to/Your/Server\"/>'");
        $this->assertNotEmpty($_SERVER['SERVER_NAME'], "'SERVER_NAME' not defined in your PhpUnit XML configuration. Please define '<server name=\"SERVER_NAME\" value=\"http://localhost/Path/to/Your/Server\"/>'");
        
        //====================================================================//
        // GENERATE FAKE SPLASH SERVER HOST URL
        $SoapUrl    =    $_SERVER['SERVER_NAME'] . $Infos["ServerPath"];
        
        return  $SoapUrl;
    }
    
    /**
     *      @abstract      Verify Response Is Valid
     *
     *      @param         string   $In       WebService Raw Response Block
     *      @param         string   $Cfg      WebService Request Configuration
     *
     */
    public function checkResponse($In, $Cfg = null)
    {
        
        //====================================================================//
        // RESPONSE BLOCK IS NOT EMPTY
        $this->assertNotEmpty($In, "Response Block is Empty");
        //====================================================================//
        // DECODE BLOCK
        $Data       =   Splash::ws()->unPack($In);
        //====================================================================//
        // CHECK RESPONSE DATA
        $this->assertNotEmpty($Data, "Response Data is Empty or Malformed");
        $this->assertInstanceOf("ArrayObject", $Data, "Response Data is Not an ArrayObject");
        $this->assertArrayHasKey("result", $Data, "Request Result is Missing");
        
        //====================================================================//
        // CHECK RESPONSE LOG
        if (array_key_exists("log", $Data)) {
            $this->checkResponseLog($Data->log, $Cfg);
        }
        
        //====================================================================//
        // CHECK RESPONSE SERVER INFOS
        if (array_key_exists("server", $Data)) {
            $this->checkResponseServer($Data->server);
        }
        
        //====================================================================//
        // CHECK RESPONSE TASKS RESULTS
        if (array_key_exists("tasks", $Data)) {
            $this->checkResponseTasks($Data->tasks, $Cfg);
        }
        
        //====================================================================//
        // CHECK RESPONSE RESULT
        if (empty($Data->result)) {
            print_r($Data);
        }
        $this->assertNotEmpty($Data->result, "Request Result is not True, Why??");
        return $Data;
    }
    
    /**
     *      @abstract      Verify Response Log Is Valid
     *
     *      @param         ArrayObject  $Log        WebService Log Array
     *      @param         string       $Cfg        WebService Request Configuration
     */
    public function checkResponseLog($Log, $Cfg = null)
    {
        //====================================================================//
        // SERVER LOG ARRAY FORMAT
        $this->assertInstanceOf("ArrayObject", $Log, "Response Log is Not an ArrayObject");
        
        //====================================================================//
        // SERVER LOGS MESSAGES FORMAT
        $this->checkResponseLogArray($Log, 'err', "Error");
        $this->checkResponseLogArray($Log, 'war', "Warning");
        $this->checkResponseLogArray($Log, 'msg', "Message");
        $this->checkResponseLogArray($Log, 'deb', "Debug Trace");
        
        //====================================================================//
        // UNEXPECTED SERVER LOG ITEMS
        foreach (array_keys($Log->getArrayCopy()) as $Key) {
            $this->assertTrue(
                    in_array($Key, array("err", "msg", "war", "deb")), 
                    "Received Unexpected Log Messages. ( Data->log->" . $Key . ")"
                    );
        }
        
        //====================================================================//
        // SERVER LOG With Silent Option Activated
        if (is_a($Cfg, "ArrayObject") && array_key_exists("silent", $Cfg)) {
            $this->assertEmpty($Log->war, "Requested Silent operation but Received Warnings, Why??");
            $this->assertEmpty($Log->msg, "Requested Silent operation but Received Messages, Why??");
            $this->assertEmpty($Log->deb, "Requested Silent operation but Received Debug Traces, Why??");
        }

        //====================================================================//
        // SERVER LOG Without Debug Option Activated
        if (is_a($Cfg, "ArrayObject") && !array_key_exists("debug", $Cfg)) {
            $this->assertEmpty($Log->deb, "Requested Non Debug operation but Received Debug Traces, Why??");
        }
            
        //====================================================================//
        //   Extract Logs From Response
        Splash::log()->merge($Log);
    }
    /**
     * @abstract    Verify Response Log Is Valid
     * @param       ArrayObject     $Log        WebService Log Array
     * @param       string          $Type       Log Key
     * @param       string          $Name       Log Type Name
     * @return      void
     */
    public function checkResponseLogArray($Log, $Type, $Name)
    {
        if (!array_key_exists($Type, $Log) || empty($Log->$Type)) {
            return;
        }
        
        //====================================================================//
        // SERVER LOG FORMAT
        $this->assertInstanceOf("ArrayObject", $Log->$Type, "Logger " . $Name . " List is Not an ArrayObject");
        foreach ($Log->$Type as $Message) {
            $this->assertTrue((is_scalar($Message) || is_null($Message)), $Name . " is Not a string. (" . print_r($Message, true) . ")");
        }
    }
    
    /**
     * @abstract    Verify Response Server Infos Are Valid
     * @param       ArrayObject     $Server         WebService Server Infos Array
     * @return      void
     */
    public function checkResponseServer($Server)
    {
        //====================================================================//
        // SERVER Informations  => Available
        $this->assertArrayHasKey("ServerHost", $Server, "Server Info (ServerHost) is Missing");
        $this->assertArrayHasKey("ServerPath", $Server, "Server Info (ServerPath) is Missing");
        $this->assertArrayHasKey("ServerType", $Server, "Server Info (ServerType) is Missing");
        $this->assertArrayHasKey("ServerVersion", $Server, "Server Info (ServerVersion) is Missing");
        $this->assertArrayHasKey("ServerAddress", $Server, "Server Info (ServerAddress) is Missing");
        
        //====================================================================//
        // SERVER Informations  => Not Empty
        $this->assertNotEmpty($Server["ServerHost"], "Server Info (ServerHost) is Empty");
        $this->assertNotEmpty($Server["ServerPath"], "Server Info (ServerPath) is Empty");
        $this->assertNotEmpty($Server["ServerType"], "Server Info (ServerType) is Empty");
        $this->assertNotEmpty($Server["ServerVersion"], "Server Info (ServerVersion) is Empty");
    }
    
    /**
     *      @abstract      Verify Response Tasks Results are Valid
     *
     *      @param         ArrayObject  $Tasks          WebService Server Tasks Results Array
     *      @param         string       $Cfg            WebService Request Configuration
     */
    public function checkResponseTasks($Tasks, $Cfg = null)
    {
        //====================================================================//
        // TASKS RESULTS ARRAY FORMAT
        $this->assertInstanceOf("ArrayObject", $Tasks, "Response Tasks Result is Not an ArrayObject");
        
        foreach ($Tasks as $Task) {
            //====================================================================//
            // TASKS Results  => Available
            $this->assertArrayHasKey("id", $Task, "Task Results => Task Id is Missing");
            $this->assertArrayHasKey("name", $Task, "Task Results => Name is Missing");
            $this->assertArrayHasKey("desc", $Task, "Task Results => Description is Missing");
            $this->assertArrayHasKey("result", $Task, "Task Results => Task Result is Missing");
            $this->assertArrayHasKey("data", $Task, "Task Results => Data is Missing");
            
            //====================================================================//
            // TASKS Results  => Not Empty
            $this->assertNotEmpty($Task["id"], "Task Results => Task Id is Empty");
            $this->assertNotEmpty($Task["name"], "Task Results => Name is Empty");
            $this->assertNotEmpty($Task["desc"], "Task Results => Description is Empty");
//            $this->assertNotEmpty( $Task["result"]             , "Task Results => Task Result is OK, Did this Task Really Failed?");
//            $this->assertNotEmpty( $Task["data"]               , "Task Results => Data is Empty");
            
            //====================================================================//
            // TASKS Delay Data
            if (is_a($Cfg, "ArrayObject") && !array_key_exists("trace", $Cfg)) {
                $this->assertArrayHasKey("delayms", $Task, "Task Results => Trace requested but DelayMs is Missing");
                $this->assertArrayHasKey("delaystr", $Task, "Task Results => Trace requested but DelayStr is Missing");
                $this->assertNotEmpty($Task["delayms"], "Task Results => Trace requested but DelayMs is Empty");
                $this->assertNotEmpty($Task["delaystr"], "Task Results => Trace requested but DelayStr is Empty");
            }
        }
    }
    
    /**
     *      @abstract   Perform generic Server Side Action
     *
     *      @return     mixed
     */
    protected function genericAction($Service, $Action, $Description, array $Parameters = array(true))
    {
        //====================================================================//
        //   Prepare Request Data
        Splash::ws()->addTask($Action, $Parameters, $Description);
        //====================================================================//
        //   Execute Action From Splash Server to Module
        $Response   =   Splash::ws()->simulate($Service);
        //====================================================================//
        //   Check Response
        $Data       =   $this->checkResponse($Response);
        //====================================================================//
        //   Extract Task Result
        if (is_a($Data->tasks, "ArrayObject")) {
            $Data->tasks = $Data->tasks->getArrayCopy();
        }
        $Task = array_shift($Data->tasks);

        //====================================================================//
        //   Turn On Output Buffering Again
        ob_start();
        
        return $Task["data"];
    }
    
    /**
     *      @abstract   Perform generic Server Side Action
     *
     *      @return     mixed
     */
    protected function genericErrorAction($Service, $Action, $Description, array $Parameters = array(true))
    {
        //====================================================================//
        //   Prepare Request Data
        Splash::ws()->addTask($Action, $Parameters, $Description);
        //====================================================================//
        //   Execute Action From Splash Server to Module
        $Response   =   Splash::ws()->simulate($Service);
        //====================================================================//
        // RESPONSE BLOCK IS NOT EMPTY
        $this->assertNotEmpty($Response, "Response Block is Empty");
        //====================================================================//
        // DECODE BLOCK
        $Data       =   Splash::ws()->unPack($Response);
        //====================================================================//
        // CHECK RESPONSE DATA
        $this->assertNotEmpty($Data, "Response Data is Empty or Malformed");
        $this->assertInstanceOf("ArrayObject", $Data, "Response Data is Not an ArrayObject");
        $this->assertArrayHasKey("result", $Data, "Request Result is Missing");
        $this->assertEmpty($Data->result, "Expect Errors but Request Result is True, Why??");
        
        //====================================================================//
        //   Extract Task Result
        if (is_a($Data->tasks, "ArrayObject")) {
            $Data->tasks = $Data->tasks->getArrayCopy();
        }
        $Task = array_shift($Data->tasks);

        //====================================================================//
        //   Turn On Output Buffering Again
        ob_start();
        
        return $Task["data"];
    }
}