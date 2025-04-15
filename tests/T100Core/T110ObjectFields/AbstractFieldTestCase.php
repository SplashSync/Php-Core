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

namespace Splash\Core\Tests\T100Core\T110ObjectFields;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Splash\Core\Dictionary\SplFields;
use Splash\Core\Models\Fields\AbstractField;

/**
 * PhpUnit Test Case with Generic Assertions for Fields
 */
abstract class AbstractFieldTestCase extends TestCase
{
    /**
     * Test Splash Abstract Field Creation.
     */
    protected function assertNewField(
        string $type = SplFields::VARCHAR,
        ?string $identifier = null
    ): AbstractField {
        $mockField = $this->getMockForAbstractClass(
            AbstractField::class,
            array($type, $identifier)
        );

        Assert::assertInstanceOf(AbstractField::class, $mockField);

        return $mockField;
    }

    /**
     * Test Splash Field Generic Boolean Property Setup.
     */
    protected static function assertBooleanPropertyWorks(
        AbstractField $mockField,
        string $property,
        callable $getter,
        callable $setter,
        bool $default = false
    ): void {
        //====================================================================//
        // Default Value
        Assert::assertEquals($default, $getter());
        Assert::assertEquals($default, $mockField->toArray()[$property]);
        //====================================================================//
        // Direct Setup
        $setter(true);
        Assert::assertTrue($getter());
        Assert::assertTrue($mockField->toArray()[$property]);
        $setter(false);
        Assert::assertFalse($getter());
        Assert::assertFalse($mockField->toArray()[$property]);
        //====================================================================//
        // No Unexpected Update
        $mockField->update(array());
        Assert::assertFalse($getter());
        $mockField->update(array($property => null));
        Assert::assertFalse($getter());
        $mockField->update(array($property => ""));
        Assert::assertFalse($getter());
        Assert::assertFalse($mockField->toArray()[$property]);
        //====================================================================//
        // Expected Update
        $mockField->update(array($property => true));
        Assert::assertTrue($getter());
        Assert::assertTrue($mockField->toArray()[$property]);
        $mockField->update(array($property => false));
        Assert::assertFalse($getter());
        Assert::assertFalse($mockField->toArray()[$property]);
    }

    /**
     * Test Splash Field Generic String Property Setup.
     */
    protected static function assertStringPropertyWorks(
        AbstractField $mockField,
        string $property,
        callable $getter,
        callable $setter,
        ?string $default = null
    ): void {
        $strValue = uniqid(ucfirst($property));
        //====================================================================//
        // Default Value
        Assert::assertEquals($default, $getter());
        Assert::assertEquals($default, $mockField->toArray()[$property]);
        //====================================================================//
        // Direct Setup
        $setter($strValue);
        Assert::assertEquals($strValue, $getter());
        //====================================================================//
        // No Unexpected Update
        $mockField->update(array());
        Assert::assertEquals($strValue, $getter());
        Assert::assertEquals($strValue, $mockField->toArray()[$property]);
        $mockField->update(array($property => null));
        Assert::assertEquals($strValue, $getter());
        Assert::assertEquals($strValue, $mockField->toArray()[$property]);
        $mockField->update(array($property => ""));
        Assert::assertEquals($strValue, $getter());
        Assert::assertEquals($strValue, $mockField->toArray()[$property]);
        $mockField->update(array($property => array()));
        Assert::assertEquals($strValue, $getter());
        Assert::assertEquals($strValue, $mockField->toArray()[$property]);
        //====================================================================//
        // Expected Update
        $updateValue = uniqid(ucfirst($property));
        $mockField->update(array($property => $updateValue));
        Assert::assertEquals($updateValue, $getter());
        Assert::assertEquals($updateValue, $mockField->toArray()[$property]);
    }
}
