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
 * @abstract    This is Client include file for Splash PHP Module on Server Side
 * @author      B. Paquier <contact@splashsync.com>
 */
  
//====================================================================//
//   INCLUDES 
//====================================================================//  

//====================================================================//  
//  CLIENT MODE - Normal Loading of Splash Module Core Class
//====================================================================//  

//====================================================================//
// Save Library Home folder Detected
define('SPLASH_DIR' , dirname(__FILE__));
    
//====================================================================//  
// Notice internal routines we are in server request mode
define("SPLASH_SERVER_MODE"   ,   0);     

//====================================================================//
// Include Splash Constants Definitions
require_once(SPLASH_DIR."/inc/Splash.Inc.php");

//====================================================================//
// Include Required Files
require_once(SPLASH_DIR."/class/Splash.php");
Splash::Log()->Deb("Splash Started In Library Mode");    
