<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

global $config, $finder;

include_once "vendor/badpixxel/php-sdk/phpcs/headers/splashsync.php";

$finder = PhpCsFixer\Finder::create()
    ->in($_SERVER['PWD'])
    ->exclude('vendor')
    ->exclude('Components/NuSOAP')
;

include_once "vendor/badpixxel/php-sdk/phpcs/cs.rules.php";

return $config;
