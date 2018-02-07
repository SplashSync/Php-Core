<?php

namespace Splash\Tests\Tools;

if ( !defined("SPLASH_SERVER_MODE") ) {
    define("SPLASH_SERVER_MODE", True);
} 

/**
 * Compatibility Patch for PhpUnit before PHP7
 */
if (PHP_VERSION_ID > 70000) {
    class BaseCase extends AbstractBaseCase {
        use \Splash\Tests\Tools\Traits\onSuccessfulTest_PHP7;
    }
} else {
    class BaseCase extends AbstractBaseCase {
        use \Splash\Tests\Tools\Traits\onSuccessfulTest_PHP5;
    }
}  