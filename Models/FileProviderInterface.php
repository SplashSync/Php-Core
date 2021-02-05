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

namespace Splash\Models;

/**
 * Local System File Provider Class Interface
 */
interface FileProviderInterface
{
    /**
     * Check if file is Available from Splash Local Class
     *
     * @param string $file File Identifier (Given by Splash Server)
     * @param string $md5  Local FileName
     *
     * @return bool
     */
    public function hasFile($file = null, $md5 = null);

    /**
     * Read a file from Splash Local Class
     *
     * @param string $file File Identifier (Given by Splash Server)
     * @param string $md5  Local FileName
     *
     * @return array|false $file       False if not found, else file contents array
     */
    public function readFile($file = null, $md5 = null);
}
