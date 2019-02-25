<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2019 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

/**
 * Bootstrap Splash for Pḧpstan
 */

//====================================================================//
// Include Splash Constants Definitions
require_once(dirname(dirname(__DIR__))."/inc/Splash.Inc.php");
require_once(dirname(dirname(__DIR__))."/inc/defines.inc.php");
require_once(dirname(dirname(__DIR__))."/inc/fatal.inc.php");
require_once(dirname(dirname(__DIR__))."/inc/fatal.inc.php");
require_once(dirname(dirname(__DIR__))."/Components/NuSOAP/nusoap.php");
define("SPLASH_SERVER_MODE", 0);
