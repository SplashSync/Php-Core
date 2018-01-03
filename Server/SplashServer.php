<?php
/*
 * This file is part of SplashSync Project.
 *
 * Copyright (C) Splash Sync <www.splashsync.com>
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @abstract    Splash Sync Server. Manage Splash Requests & Responses.
 *              This file is included only in case on NuSOAP call to slave server.
 * @author      B. Paquier <contact@splashsync.com>
 */

namespace Splash\Server;

use Splash\Core\SplashCore  as Splash;
use ArrayObject;

//====================================================================//
//  CLASS DEFINITION
//====================================================================//  
 
class SplashServer 
{
    
    //====================================================================//
    // Webservice I/O Buffers     
    //====================================================================//
    private static $_In;                   // Input Buffer
    private static $_Out;                  // Output Buffer
    
    
    /**
     *      @abstract       Class Constructor
     *      @return         bool
     */
    public function __construct()
    {
        return self::Init();
    }    
    
//====================================================================//
//  WEBSERVICE REGISTERED REQUEST FUNCTIONS
//====================================================================//
    
    /**
     *      @abstract      Minimal Test of Webservice connexion 
     * 
     *      @return        mixed    WebService Packaged Data Outputs or NUSOAP Error
     */
    public static function Ping()
    {
        self::Init();
        
        //====================================================================//
        // Simple Message reply, No Encryption
        Splash::Log()->Msg("Ping Successful.");
        self::$_Out->result  = True;
        
        //====================================================================//
        // Transmit Answer with No Encryption
        return Splash::Ws()->Pack( self::$_Out , 1 ); 
    }   
    
    /**
     *      @abstract      Connect Webservice and fetch server informations 
     * 
     *      @param         string   $id         OsWs WebService Node Identifier
     *      @param         string   $data       OsWs WebService Packaged Data Inputs
     * 
     *      @return        mixed    WebService Packaged Data Outputs or NUSOAP Error
     */
    public static function Connect($id,$data)
    {
        //====================================================================//
        // Verify Node Id 
        //====================================================================//
        if ( Splash::Configuration()->WsIdentifier !== $id ) {
            return Null;
        }
        self::Init();
        //====================================================================//
        // Unpack NuSOAP Request
        //====================================================================//
        if (self::Receive($data) != True) {
            return Null;
        }
        //====================================================================//
        // Execute Request
        //====================================================================// 
        Splash::Log()->Msg("Connection Successful (" . Splash::getName() . " V" . Splash::getVersion() . ")");
        //====================================================================//
        // Transmit Answers To Master
        //====================================================================//         
        return self::Transmit(True); 
    }
    
    /**
     *      @abstract      Administrative server functions 
     * 
     *      @param         string   $id         OsWs WebService Node Identifier
     *      @param         string   $data       OsWs WebService Packaged Data Inputs
     * 
     *      @return        mixed    WebService Packaged Data Outputs or NUSOAP Error

     */
    public static function Admin($id,$data)
    {
        return self::Run($id,$data,__FUNCTION__);
    }	

    /**
     *      @abstract      Objects server functions 
     * 
     *      @param         string   $id         OsWs WebService Node Identifier
     *      @param         string   $data       OsWs WebService Packaged Data Inputs
     * 
     *      @return        mixed    WebService Packaged Data Outputs or NUSOAP Error
     */
    public static function Objects($id,$data)
    {
        return self::Run($id,$data,__FUNCTION__);
    }	    

    /**
     *      @abstract      Files Transfers server functions 
     * 
     *      @param         string   $id         OsWs WebService Node Identifier
     *      @param         string   $data       OsWs WebService Packaged Data Inputs
     * 
     *      @return        mixed    WebService Packaged Data Outputs or NUSOAP Error
     */
    public static function Files($id,$data)
    {
        return self::Run($id,$data,__FUNCTION__);
    }

    /**
     *      @abstract      Widgets Retrieval server functions 
     * 
     *      @param         string   $id         OsWs WebService Node Identifier
     *      @param         string   $data       OsWs WebService Packaged Data Inputs
     * 
     *      @return        mixed    WebService Packaged Data Outputs or NUSOAP Error
     */
    public static function Widgets($id,$data)
    {
        return self::Run($id,$data,__FUNCTION__);
    }
        
//====================================================================//
//  WEBSERVICE SERVER MANAGEMENT
//====================================================================//
   
    /**
     *      @abstract       Class Initialisation
     *      @return         bool
     */
    public static function Init()
    {
        //====================================================================//
        // Initialize I/O Data Buffers
        self::$_In          = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        self::$_Out         = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);        
        return True;
    }   
 
    /**
     *      @abstract   Treat Received Data and Initialize Server before request exectution 
     * 
     *      @param      string      $data       Received Raw Data 
     * 
     *      @return     bool
     */
    private static function Receive($data) {
        
        //====================================================================//
        // Unpack Raw received data 
        self::$_In = Splash::Ws()->unPack($data);
        if (empty(self::$_In)) {
            return False;
        }
        
        //====================================================================//
        // Import Server request Configuration
        if ( isset(self::$_In->cfg) && !empty(self::$_In->cfg) ) {
            //====================================================================//
            // Store Server Request Configuration 
            Splash::Configuration()->server = self::$_In->cfg;
            
            //====================================================================//
            // Setup Debug allowed or not
            Splash::Log()->SetDebug(self::$_In->cfg->debug);
        } else {
            //====================================================================//
            // Store Server Request Configuration 
            Splash::Configuration()->server = array();
        }
        
        
        //====================================================================//
        // Fill Static Server Informations To Output 
        self::$_Out->server = Splash::Ws()->getServerInfos();
        
        return True;
    }

    /**
     *      @abstract   Treat Computed Data and return packaged data buffer for tranmit to master 
     * 
     *      @param      bool        $result     Global Operation Result (0 if KO, 1 if OK) 
     * 
     *      @return     string      To Transmit Raw Data or False if KO
     */
    private static function Transmit($result) {
        
        //====================================================================//
        // Safety Check 
        if (empty(self::$_Out)) {
            return False;
        }
        
        //====================================================================//
        // Prepare Data Output Buffer
        //====================================================================//
        
        //====================================================================//
        // Set Global Operation Result
        self::$_Out->result = $result;
        
        //====================================================================//
        // Flush Php Output Buffer
        Splash::Log()->FlushOuputBuffer();
        
        //====================================================================//
        // Transfers Log Reccords to _Out Buffer
        self::$_Out->log = Splash::Log();
        
        //====================================================================//
        // Package data and return to Server
        return Splash::Ws()->Pack(self::$_Out);
    }
  
    /**
     *      @abstract       All-In-One SOAP Server Messages Reception & Dispaching 
     *                      Unpack all pending tasks and send order to local task routers for execution. 
     * 
     *      @param          string      $Id         WebService Node Identifier
     *      @param          string      $Data       WebService Packaged Data Inputs
     *      @param          string      $Router     Name of the router function to use for task execution
     * 
     *      @return        mixed    WebService Packaged Data Outputs or NUSOAP Error
     */
    private static function Run($Id,$Data,$Router)
    {
        //====================================================================//
        // Verify Node Id 
        //====================================================================//
        if ( Splash::Configuration()->WsIdentifier !== $Id ) {
            return self::Transmit(False);
        }
        self::Init();
        //====================================================================//
        // Unpack NuSOAP Request
        //====================================================================//
        if (self::Receive($Data) != True) {
            return self::Transmit(False);
        }
        //====================================================================//
        // Execute Request
        //====================================================================//
        $GlobalResult = Splash::Router()->Execute($Router, self::$_In, self::$_Out);
        //====================================================================//
        // Transmit Answers To Master
        //====================================================================//         
        return self::Transmit($GlobalResult); 
    }
   
//====================================================================//
//  SERVER STATUS & CONFIG DEBUG FUNCTIONS
//====================================================================//
        
    /**
     *      @abstract      Analyze & Debug Server Status 
     * 
     *      @return        html
     */
    public static function GetStatusInformations()    {
        
        
        $Html = Null;

        //====================================================================//
        // Exectute Splash Local SelfTest
        if (!Splash::SelfTest()) {
            $Html   .=      Splash::Log()->GetHtmlListItem("Splash Module SelfTest as Failed" , "Error");
        } else {
            $Html   .=      Splash::Log()->GetHtmlListItem("Splash Module SelfTest is Passed");
        }
        
        //====================================================================//
        // Output Server Informations
        $Html   .=      Splash::Log()->GetHtmlListItem("Server Informations");
        $Html   .=      "<PRE>" . print_r(Splash::Ws()->getServerInfos()->getArrayCopy() , True) . "</PRE>";

        return $Html;
    }
    
}

?>