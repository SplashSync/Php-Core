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

use Splash\Core\SplashCore      as Splash;

/*
 * @abstract   Declare fatal Error Handler => Called in case of Script Exceptions
 */
function fatal_handler()
{
    //====================================================================//
    // Read Last Error
    $error  =   error_get_last();
    if (!$error) {
        return;
    }
    //====================================================================//
    // Fatal Error
    if ($error["type"] == E_ERROR) {
        //====================================================================//
        // Parse Error in Response.
        Splash::com()->fault($error);
        //====================================================================//
        // Process methods & Return the results.
        Splash::com()->handle();
    //====================================================================//
    // Non Fatal Error
    } else {
        Splash::log()->war($error["message"] . " on File " . $error["file"] . " Line " . $error["line"]);
    }
}
