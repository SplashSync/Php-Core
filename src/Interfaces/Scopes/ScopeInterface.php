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

use Splash\Core\Interfaces\Fields\FieldConstraintInterface;

/**
 * Interface for Server Scope Definition
 */
interface ScopeInterface
{
    const DF_LANG = "en_US";

    /**
     * Scope Constructor
     */
    public function __construct();

    /**
     * Get Scope Identification Code
     */
    public static function getCode(): string;

    /**
     * Get Name
     */
    public function getName(?string $isoLang = null): string;

    /**
     * Get Short Description
     */
    public function getShortDescription(?string $isoLang = null): string;

    /**
     * Get Scope Icon Code
     * -> FontAwesome icon name only, e.g. "fa-box-open"
     */
    public function getIconCode(): string;

    /**
     * Get the list of Splash Object Types Impacted by this Scope
     *
     * @return string[]
     */
    public function getImpactedTypes(): array;

    /**
     * Get the list of Object Fields Constraints for this Scope
     * -> Fields Constraints are there to check Object Fields definitions
     * -> It doesn't execute any functional test
     * -> It's used to check if a field is compatible with this scope
     * -> In case of chainable scope, the first defined constraint is used
     *
     * @return array<string, FieldConstraintInterface>
     */
    public function getFieldConstraints(): array;
}
