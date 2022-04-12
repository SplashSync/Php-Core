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

namespace Splash\Tests\Tools\Fields;

/**
 * Bool Field : Basic Boolean
 */
interface FieldInterface
{
    //==============================================================================
    //      DATA VALIDATION
    //==============================================================================

    /**
     * Verify given Raw Data is Valid
     *
     * @param null|array|scalar $data
     *
     * @return null|string
     */
    public static function validate($data): ?string;

    //==============================================================================
    //      FAKE DATA GENERATOR
    //==============================================================================

    /**
     * Generate Fake Raw Field Data for Debugger Simulations
     *
     * @param array $settings User Defined Faker Settings
     *
     * @return array|scalar
     */
    public static function fake(array $settings);

    /**
     * Compare Two Data Block to See if similar (Update Required)
     *
     * !important : Target Data is always validated before compare
     *
     * @param mixed $source   Original Data Block
     * @param mixed $target   New Data Block
     * @param array $settings User Defined Faker Settings
     *
     * @return bool
     */
    public static function compare($source, $target, array $settings): bool;
}
