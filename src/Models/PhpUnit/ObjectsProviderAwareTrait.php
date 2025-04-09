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

namespace Splash\Core\Models\PhpUnit;

use Exception;
use Splash\Core\Client\Splash;

/**
 * Add Simple Objects Provider to PhpUnit Test
 */
trait ObjectsProviderAwareTrait
{
    /**
     * Data Provider : Objects Types Tests Dataset
     *
     * @return array[]
     */
    public function simpleObjectTypesProvider(): array
    {
        $result = array();

        self::setUp();

        //====================================================================//
        // Fetch Objects Types
        try {
            $objectTypes = Splash::objects();
        } catch (Exception $e) {
            $objectTypes = array();
        }
        //====================================================================//
        // Walk on Objects Types
        foreach ($objectTypes as $objectType) {
            $result[$objectType] = array($objectType);
        }

        self::tearDown();

        return $result;
    }
}
