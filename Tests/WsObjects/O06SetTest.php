<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2019 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Tests\WsObjects;

use Splash\Tests\Tools\ObjectsCase;
use Splash\Tests\Tools\Traits\ObjectsSetTestsTrait;

/**
 * @abstract    Objects Test Suite - Verify Read/Write of any R/W fields is Ok.
 *
 * @author SplashSync <contact@splashsync.com>
 */
class O06SetTest extends ObjectsCase
{
    use ObjectsSetTestsTrait;

    /**
     * @dataProvider objectFieldsProvider
     *
     * @param mixed      $testSequence
     * @param mixed      $objectType
     * @param mixed      $field
     * @param null|mixed $forceObjectId
     */
    public function testSetSingleFieldFromModule($testSequence, $objectType, $field, $forceObjectId = null)
    {
        //====================================================================//
        //   Load Test Sequence
        $this->loadLocalTestSequence($testSequence);

        //====================================================================//
        //   Execute Set Test
        $this->coreTestSetSingleFieldFromModule($objectType, $field, $forceObjectId);
    }

    /**
     * @dataProvider objectFieldsProvider
     *
     * @param mixed      $testSequence
     * @param mixed      $objectType
     * @param mixed      $field
     * @param null|mixed $forceObjectId
     */
    public function testSetSingleFieldFromService($testSequence, $objectType, $field, $forceObjectId = null)
    {
        //====================================================================//
        //   Load Test Sequence
        $this->loadLocalTestSequence($testSequence);

        //====================================================================//
        //   Execute Set Test
        $this->coreTestSetSingleFieldFromService($objectType, $field, $forceObjectId);
    }
}
