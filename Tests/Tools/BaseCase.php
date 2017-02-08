<?php

namespace Splash\Tests\Tools;

use PHPUnit\Framework\TestCase;

use Splash\Client\Splash;
use Splash\Server\SplashServer;

if ( !defined("SPLASH_SERVER_MODE") ) {
    define("SPLASH_SERVER_MODE", True);
} 

/**
 * @abstract    Admin Test Suite - Ping Client Verifications
 *
 * @author SplashSync <contact@splashsync.com>
 */
class BaseCase extends TestCase {

    protected function onNotSuccessfulTest($e)
    {
        fwrite(STDOUT, Splash::Log()->GetConsoleLog() );
        throw $e;
    }    
    
    protected function setUp()
    {
        //====================================================================//
        // BOOT or REBOOT MODULE
        Splash::Reboot();
    }    

    /**
     * @abstract        Verify if Data is present in Array and in right Internal Format
     * 
     * @param mixed     $Data           Tested Array
     * @param mixed     $Key            Tested Array Key
     * @param mixed     $Type           Expected Data Type
     * @param string    $Comment
     */    
    public function assertArrayInternalType($Data , $Key, $Type, $Comment)
    {
        $this->assertArrayHasKey(   $Key,   $Data,      $Comment . " => Key '" . $Key . "' not defined");
        $this->assertNotEmpty(      $Data[$Key],        $Comment . " => Key '" . $Key . "' is Empty");
        $this->assertInternalType($Type, $Data[$Key],   $Comment . " => Key '" . $Key . "' is of Expected Internal Type");
    }
    
    /**
     * @abstract        Verify if Data is present in Array and in right Internal Format
     * 
     * @param mixed     $Data           Tested Array
     * @param mixed     $Key            Tested Array Key
     * @param mixed     $Type           Expected Data Type
     * @param string    $Comment
     */    
    public function assertArrayInstanceOf($Data , $Key, $Type, $Comment)
    {
        $this->assertArrayHasKey(   $Key,   $Data,      $Comment . " => Key '" . $Key . "' not defined");
        $this->assertNotEmpty(      $Data[$Key],        $Comment . " => Key '" . $Key . "' is Empty");
        $this->assertInstanceOf($Type, $Data[$Key],     $Comment . " => Key '" . $Key . "' is of Expected Internal Type");
    }
    

    /**
     * @abstract        Verify if Data is a valid Splash Data Block Bool Value
     * 
     * @param mixed     $Data
     * @param string    $Comment
     */    
    public function assertIsSplashBool($Data , $Comment)
    {
        $Test = is_bool($Data) || ($Data === "0") || ($Data === "1");
        $this->assertTrue( $Test , $Comment );
    }
    
    /**
     * @abstract        Verify if Data is present in Array and is Splash Bool
     * 
     * @param mixed     $Data           Tested Array
     * @param mixed     $Key            Tested Array Key
     * @param string    $Comment
     */    
    public function assertArraySplashBool($Data , $Key, $Comment)
    {
        $this->assertArrayHasKey(   $Key,   $Data,      $Comment . " => Key '" . $Key . "' not defined");
        $this->assertIsSplashBool($Data[$Key],          $Comment . " => Key '" . $Key . "' is of Expected Internal Type");
    }

    /**
     * @abstract        Verify if Data is a valid Splash Field Data Value
     * 
     * @param mixed     $Data
     * @param string    $Type
     * @param string    $Comment
     */    
    public function assertIsValidSplashFieldData($Data, $Type, $Comment)
    {
        //====================================================================//
        // Verify Type is Valid
        $ClassName = self::isValidType( $Type );
        $this->assertNotEmpty(  $ClassName , "Field Type '" . $Type . "' is not a Valid Splash Field Type.");
    
        //====================================================================//
        // Verify Data is Valid
        $this->assertTrue(  $ClassName::validate($Data), "Data is not a Valid Splash '" . $Type . "'. (" . print_r($Data,True) . ")");
    }

    
    /**
     *      @abstract      Verify Response Is Valid
     * 
     *      @param         string   $In       WebService Raw Response Block
     *      @param         string   $Cfg      WebService Request Configuration
     * 
     */    
    public function CheckResponse($In , $Cfg = Null)
    {
        
        //====================================================================//
        // RESPONSE BLOCK IS NOT EMPTY
        $this->assertNotEmpty( $In                        , "Response Block is Empty");
        //====================================================================//
        // DECODE BLOCK 
        $Data       =   Splash::Ws()->unPack( $In ); 
        //====================================================================//
        // CHECK RESPONSE DATA
        $this->assertNotEmpty( $Data                      , "Response Data is Empty or Malformed");
        $this->assertInstanceOf( "ArrayObject" , $Data    , "Response Data is Not an ArrayObject");
        $this->assertArrayHasKey( "result", $Data         , "Request Result is Missing");
        
        //====================================================================//
        // CHECK RESPONSE LOG
        if ( array_key_exists("log",$Data) ) {
            $this->CheckResponseLog($Data->log , $Cfg);
        }
        
        //====================================================================//
        // CHECK RESPONSE SERVER INFOS 
        if ( array_key_exists("server",$Data) ) {
            $this->CheckResponseServer($Data->server);
        }
        
        //====================================================================//
        // CHECK RESPONSE TASKS RESULTS 
        if ( array_key_exists("tasks",$Data) ) {
            $this->CheckResponseTasks($Data->tasks , $Cfg);
        }
        
        //====================================================================//
        // CHECK RESPONSE RESULT
        if ( empty($Data->result) ) {
            print_r($Data);
        }
        $this->assertNotEmpty( $Data->result              , "Request Result is not True, Why??");
        return $Data;
    }
    
    /**
     *      @abstract      Verify Response Log Is Valid
     * 
     *      @param         arrayobject  $Log        WebService Log Array
     *      @param         string       $Cfg        WebService Request Configuration
     */    
    public function CheckResponseLog($Log , $Cfg = Null)
    {
        //====================================================================//
        // SERVER LOG ARRAY FORMAT
        $this->assertInstanceOf( "ArrayObject" , $Log    , "Response Log is Not an ArrayObject");
        
        //====================================================================//
        // SERVER LOGS MESSAGES FORMAT
        $this->CheckResponseLogArray($Log, 'err' , "Error");
        $this->CheckResponseLogArray($Log, 'war' , "Warning");
        $this->CheckResponseLogArray($Log, 'msg' , "Message");
        $this->CheckResponseLogArray($Log, 'deb' , "Debug Trace");
        
        //====================================================================//
        // UNEXPECTED SERVER LOG ITEMS
        foreach ($Log as $Key => $Lines) {
            $this->assertTrue( in_array($Key, array("err", "msg", "war", "deb") ) , "Received Unexpected Log Messages. ( Data->log->" . $Key . ")");
        }
        
        //====================================================================//
        // SERVER LOG With Silent Option Activated
        if ( is_a($Cfg,"ArrayObject") && array_key_exists("silent",$Cfg) ) {
            $this->assertEmpty( $Log->war            , "Requested Silent operation but Received Warnings, Why??");
            $this->assertEmpty( $Log->msg            , "Requested Silent operation but Received Messages, Why??");
            $this->assertEmpty( $Log->deb            , "Requested Silent operation but Received Debug Traces, Why??");
        }

        //====================================================================//
        // SERVER LOG Without Debug Option Activated
        if ( is_a($Cfg,"ArrayObject") && !array_key_exists("debug",$Cfg) ) {
            $this->assertEmpty( $Log->deb            , "Requested Non Debug operation but Received Debug Traces, Why??");
        }            
            
        //====================================================================//
        //   Extract Logs From Response 
        Splash::Log()->Merge($Log);
//var_dump($Log->deb);
        

    }
    /**
     *      @abstract      Verify Response Log Is Valid
     *      @param         arrayobject  $Log       WebService Log Array
     *      @param         string       $Type      Log Key
     *      @param         string       $Name      Log Type Name
     */    
    public function CheckResponseLogArray($Log, $Type, $Name)
    {
        if ( !array_key_exists($Type,$Log) || empty($Log->$Type) ) {
            return;
        }  
        
        //====================================================================//
        // SERVER LOG FORMAT
        $this->assertInstanceOf( "ArrayObject" , $Log->$Type    , "Logger " . $Name . " List is Not an ArrayObject");
        foreach ($Log->$Type as $Message) {
            $this->assertTrue( ( is_scalar($Message) || is_null($Message) ) , $Name . " is Not a string. (" . print_r($Message, True) . ")");
        }
    }
    
    /**
     *      @abstract      Verify Response Server Infos Are Valid
     * 
     *      @param         arrayobject  $Server         WebService Server Infos Array
     */    
    public function CheckResponseServer($Server)
    {

        //====================================================================//
        // SERVER Informations  => Available
        $this->assertArrayHasKey( "ServerHost",     $Server     , "Server Info (ServerHost) is Missing");
        $this->assertArrayHasKey( "ServerPath",     $Server     , "Server Info (ServerPath) is Missing");
        $this->assertArrayHasKey( "ServerType",     $Server     , "Server Info (ServerType) is Missing");
        $this->assertArrayHasKey( "ServerVersion",  $Server     , "Server Info (ServerVersion) is Missing");
        $this->assertArrayHasKey( "ServerAddress",  $Server     , "Server Info (ServerAddress) is Missing");
        
        //====================================================================//
        // SERVER Informations  => Not Empty
//        $this->assertNotEmpty( $Server["ServerHost"]            , "Server Info (ServerHost) is Empty");
        $this->assertNotEmpty( $Server["ServerPath"]            , "Server Info (ServerPath) is Empty");
        $this->assertNotEmpty( $Server["ServerType"]            , "Server Info (ServerType) is Empty");
        $this->assertNotEmpty( $Server["ServerVersion"]         , "Server Info (ServerVersion) is Empty");
//        $this->assertNotEmpty( $Server["ServerAddress"]         , "Server Info (ServerAddress) is Empty");
            
    }
    
    /**
     *      @abstract      Verify Response Tasks Results are Valid
     * 
     *      @param         arrayobject  $Tasks          WebService Server Tasks Results Array
     *      @param         string       $Cfg            WebService Request Configuration
     */    
    public function CheckResponseTasks($Tasks , $Cfg = Null)
    {    
        //====================================================================//
        // TASKS RESULTS ARRAY FORMAT
        $this->assertInstanceOf( "ArrayObject" , $Tasks    , "Response Tasks Result is Not an ArrayObject");
        
        foreach ($Tasks as $Task) {
            
            //====================================================================//
            // TASKS Results  => Available
            $this->assertArrayHasKey( "id",         $Task      , "Task Results => Task Id is Missing");
            $this->assertArrayHasKey( "name",       $Task      , "Task Results => Name is Missing");
            $this->assertArrayHasKey( "desc",       $Task      , "Task Results => Description is Missing");
            $this->assertArrayHasKey( "result",     $Task      , "Task Results => Task Result is Missing");
            $this->assertArrayHasKey( "data",       $Task      , "Task Results => Data is Missing");
            
            //====================================================================//
            // TASKS Results  => Not Empty
            $this->assertNotEmpty( $Task["id"]                 , "Task Results => Task Id is Empty");
            $this->assertNotEmpty( $Task["name"]               , "Task Results => Name is Empty");
            $this->assertNotEmpty( $Task["desc"]               , "Task Results => Description is Empty");
//            $this->assertNotEmpty( $Task["result"]             , "Task Results => Task Result is OK, Did this Task Really Failed?");
//            $this->assertNotEmpty( $Task["data"]               , "Task Results => Data is Empty");
            
            //====================================================================//
            // TASKS Delay Data
            if ( is_a($Cfg,"ArrayObject") && !array_key_exists("trace",$Cfg) ) {
                $this->assertArrayHasKey( "delayms",   $Task    , "Task Results => Trace requested but DelayMs is Missing");
                $this->assertArrayHasKey( "delaystr",  $Task    , "Task Results => Trace requested but DelayStr is Missing");
                $this->assertNotEmpty( $Task["delayms"]         , "Task Results => Trace requested but DelayMs is Empty");
                $this->assertNotEmpty( $Task["delaystr"]        , "Task Results => Trace requested but DelayStr is Empty");
            }
            
        }

    }  
    
    /**
     *      @abstract   Perform generic Server Side Action
     *  
     *      @return     mixed            
     */
    protected function GenericAction($Service, $Action, $Description, array $Parameters = array(True))   
    {
        
        //====================================================================//
        //   Prepare Request Data
        Splash::Ws()->AddTask( $Action, $Parameters , $Description );
        Splash::Ws()->Call_Init( $Service );
        Splash::Ws()->Call_AddTasks();
        
        //====================================================================//
        //   Encode Request Data
        $Request =  Splash::Ws()->Pack( Splash::Ws()->getOutputBuffer() );
        
        //====================================================================//
        //   Execute Action From Splash Server to Module  
        $Response   =   SplashServer::$Service(Splash::Configuration()->WsIdentifier, $Request);
        
        
        //====================================================================//
        //   Check Response 
        $Data       =   $this->CheckResponse( $Response ); 

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
    protected function GenericErrorAction($Service, $Action, $Description, array $Parameters = array(True))   
    {
        //====================================================================//
        //   Prepare Request Data
        Splash::Ws()->AddTask( $Action, $Parameters , $Description );
        Splash::Ws()->Call_Init( $Service );
        Splash::Ws()->Call_AddTasks();
        //====================================================================//
        //   Encode Request Data
        $Request    =   Splash::Ws()->Pack( Splash::Ws()->getOutputBuffer() );
        //====================================================================//
        //   Execute Action From Splash Server to Module  
        $Response   =   SplashServer::$Service(Splash::Configuration()->WsIdentifier, $Request);
        //====================================================================//
        // RESPONSE BLOCK IS NOT EMPTY
        $this->assertNotEmpty( $Response                    , "Response Block is Empty");
        //====================================================================//
        // DECODE BLOCK 
        $Data       =   Splash::Ws()->unPack( $Response ); 
        //====================================================================//
        // CHECK RESPONSE DATA
        $this->assertNotEmpty( $Data                        , "Response Data is Empty or Malformed");
        $this->assertInstanceOf( "ArrayObject" , $Data      , "Response Data is Not an ArrayObject");
        $this->assertArrayHasKey( "result", $Data           , "Request Result is Missing");
        $this->assertEmpty( $Data->result                   , "Expect Errors but Request Result is True, Why??");
        
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
    
    
    public function testDummy()
    {
        $this->assertTrue(True);
    }
    
    
}
