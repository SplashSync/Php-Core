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
 * Interface for Chaining Scopes
 */
interface ChainableInterface
{
    /**
     * Get Child Scope Codes
     *
     * @return string[]
     */
    public function getChildCodes(): array;

    /**
     * Get Children Scopes
     *
     * @return array<string, ScopeInterface>
     */
    public function getChildren(string $objectType = null): array;

    /**
     * Get All Children Scopes (Recursive)
     *
     * @return array<string, ScopeInterface>
     */
    public function getAllChildren(string $objectType = null): array;
}
