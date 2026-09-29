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
use Splash\Core\Fields\FieldConstraint;

/**
 * Test Field Constraints: lookup type & alternatives
 */
class T117FieldConstraintTest extends TestCase
{
    /**
     * Test a constraint may restrict its field lookup to a type
     */
    public function testLookupType(): void
    {
        $constraint = new FieldConstraint("https://schema.org/Invoice", "paymentMethodId");
        Assert::assertNull($constraint->getType());
        Assert::assertSame($constraint, $constraint->setType(SplFields::VARCHAR));
        Assert::assertEquals(SplFields::VARCHAR, $constraint->getType());
        //====================================================================//
        // Reset clears the lookup type as any other flag
        $constraint->reset();
        Assert::assertNull($constraint->getType());
    }

    /**
     * Test a constraint may carry alternatives, at least one of them being expected
     */
    public function testAlternatives(): void
    {
        $byId = new FieldConstraint("http://schema.org/Product", "productID");
        $bySku = new FieldConstraint("http://schema.org/Product", "sku");
        Assert::assertEmpty($byId->getAlternatives());

        Assert::assertSame($byId, $byId->addAlternative($bySku));
        Assert::assertCount(1, $byId->getAlternatives());
        Assert::assertSame($bySku, $byId->getAlternatives()[0]);
        //====================================================================//
        // Alternatives are structural: a reset keeps them
        $byId->reset();
        Assert::assertCount(1, $byId->getAlternatives());
    }
}
