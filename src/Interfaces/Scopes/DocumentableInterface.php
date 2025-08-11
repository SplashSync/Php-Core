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

namespace Splash\Core\Interfaces\Scopes;

/**
 * Interface for Server Scope Documentation
 */
interface DocumentableInterface
{
    /**
     * Get Icon Class
     */
    public function getIconClass(): string;

    /**
     * Get Markdown Description
     * -> Field Documentation / Description as Markdown Text
     */
    public function getMdDescription(?string $isoLang = null): string;
}
