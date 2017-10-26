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
 * 
 * @author Bernard Paquier <contact@splashsync.com>
 */

namespace Splash\Tests\Tools\Traits;

/**
 * Description of onSuccessfulTest_PHP7
 *
 * @author nanard33
 */
trait onSuccessfulTest_PHP7 {
    
    function onNotSuccessfulTest(\Throwable $Exception)
    {
        //====================================================================//
        // Do not display log on Skipped Tests
        if ( is_a($Exception, "PHPUnit\Framework\SkippedTestError") ) {
            throw $Exception;
        }
        //====================================================================//
        // OutPut Splash Logs
        fwrite(STDOUT, Splash::Log()->GetConsoleLog() );
        //====================================================================//
        // OutPut Phpunit Exeption
        throw $Exception;
    }
}
