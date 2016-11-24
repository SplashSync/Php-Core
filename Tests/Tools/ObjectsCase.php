<?php

namespace Splash\Tests\Tools;

use Splash\Client\Splash;
use Splash\Server\SplashServer;

if ( !defined("SPLASH_DEBUG") ) {
    define("SPLASH_DEBUG" , True);
} 

/**
 * @abstract    Splash Test Tools - Objects Test Case Base Class
 *
 * @author SplashSync <contact@splashsync.com>
 */
class ObjectsCase extends BaseCase {

    
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
//var_dump($Data);        
        
        //====================================================================//
        //   Extract Task Result 
        if (is_a($Data->tasks, "ArrayObject")) {
            $Task = array_shift($Data->tasks->getArrayCopy());
        } elseif (is_array($Data->tasks)) {
            $Task = array_shift($Data->tasks);
        }
//var_dump($Task);        

        return $Task["data"];
    }
    
    
    
    //====================================================================//
    //   Data Provider Functions  
    //====================================================================//
    
    public function ObjectTypesProvider()
    {
        $Result = array();
        
        foreach (Splash::Objects() as $ObjectType) {
            $Result[] = array($ObjectType);            
        }
        
        return $Result;
    }
    
    public function testDummy()
    {
        $this->assertTrue(True);
    }

    
}
