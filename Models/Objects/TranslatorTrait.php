<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2021 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace   Splash\Models\Objects;

use Splash\Core\SplashCore      as Splash;

/**
 * @abstract    Implement Translator Management using Splash Translator
 */
trait TranslatorTrait
{
    /**
     * @abstract       Load translations from a specified INI file into Static array.
     *                 If data for file already loaded, do nothing.
     *                 All data in translation array are stored in UTF-8 format.
     *                 trans_loaded is completed with $file key.
     *
     * @param string $fileName File name to load (.ini file).
     *                         Must be "file" or "file@local" for local language files:
     *                         If $FileName is "file@local" instead of "file" then we look for local lang file
     *                         in localpath/langs/code_CODE/file.lang
     *
     * @return bool
     */
    public function load($fileName)
    {
        return Splash::translator()->load($fileName);
    }

    /**
     * @abstract   Return text translated of text received as parameter (and encode it into HTML)
     *
     * @param string $key     Key to translate
     * @param string $param1  chaine de param1
     * @param string $param2  chaine de param2
     * @param string $param3  chaine de param3
     * @param string $param4  chaine de param4
     * @param int    $maxsize Max length of text
     *
     * @return string Translated string (encoded into HTML entities and UTF8)
     */
    public static function trans(
        $key,
        $param1 = '',
        $param2 = '',
        $param3 = '',
        $param4 = '',
        $maxsize = 0
    ) {
        return Splash::translator()->translate($key, $param1, $param2, $param3, $param4, $maxsize);
    }
}
