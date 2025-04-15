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

namespace Splash\Core\Tests\T400Components;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Splash\Core\Components\FieldsFactory;
use Splash\Core\Dictionary\SplFields;

/**
 * Components Test Suite - Fields Factory Verifications
 */
class T407FieldsFactoryTest extends TestCase
{
    /**
     * Test the creation of a field with fieldId and fieldName
     */
    public function testBasicFieldsCreation(): void
    {
        $factory = new FieldsFactory();
        //====================================================================//
        // Create Field With Type Only
        $factory
            ->create(SplFields::VARCHAR)
            ->identifier(uniqid("id_"))
            ->name(uniqid("name_"))
        ;
        //====================================================================//
        // Create Field With ID And Name
        $factory
            ->create(SplFields::VARCHAR, uniqid("id_"))
            ->name(uniqid("name_"))
        ;
        //====================================================================//
        // Create Field With ID And Name
        $factory->create(SplFields::VARCHAR, uniqid("id_"), uniqid("name_"));
        //====================================================================//
        // Verify
        Assert::assertCount(3, $factory->build()->getCollection());
        Assert::assertCount(3, $factory->toArray());
        Assert::assertCount(3, $factory->publish() ?? array());
    }

    /**
     * Test Creating Invalid Fields is non Blocking
     */
    public function testInvalidFieldsCreation(): void
    {
        $factory = new FieldsFactory();

        //====================================================================//
        // Create valid fields
        $factory->create(SplFields::VARCHAR, "field_one", "Field One");
        $factory->create(SplFields::VARCHAR, "field_two", "Field Two");
        //====================================================================//
        // Create invalid field
        $factory->create(SplFields::VARCHAR);
        //====================================================================//
        // Create valid fields
        $factory->create(SplFields::VARCHAR, "field_three", "Field Three");
        $factory->create(SplFields::VARCHAR, "field_four", "Field Four");
        //====================================================================//
        // Verify
        Assert::assertCount(4, $factory->build()->getCollection());
        Assert::assertCount(4, $factory->toArray());
        Assert::assertCount(4, $factory->publish() ?? array());
    }
}
