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

namespace Splash\Core\Interfaces\Fields;

/**
 * Interface for Field Definitions Templates
 */
interface FieldTemplateInterface
{
    const DF_LANG = "en_US";

    /**
     * Template Constructor
     */
    public function __construct();

    /**
     * Get Code
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
     * Get Field Configuration to Apply
     *
     * @return array<string, array|scalar>
     */
    public function getConfiguration(?string $isoLang = null): array;

    /**
     * Get Markdown Description
     * -> Field Documentation / Description as Markdown Text
     */
    public function getMdDescription(?string $isoLang = null): string;

    /**
     * Get Technical Description
     */
    public function getTechnicalDescription(): string;
}
