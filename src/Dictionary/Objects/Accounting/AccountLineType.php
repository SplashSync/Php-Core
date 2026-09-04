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

namespace Splash\Core\Dictionary\Objects\Accounting;

/**
 * Manage Access to Accounting Documents Line Types
 *
 * Qualifies WHAT a line of an accounting document (order, invoice, quotation)
 * really is: a sold item, a shipping charge, an extra fee, an optional line or
 * a simple comment. Without this information, receiving applications have to
 * guess from the line label, which never works across languages & shops.
 *
 * The value is a normalized CODE, exposed to Splash as a varchar list field.
 * An EMPTY value is not an error: it means the line is a regular product line
 * (see self::DEFAULT), so legacy connectors that do not know this field keep
 * behaving as before.
 *
 * @see https://schema.org/OrderItem additionalType
 */
class AccountLineType
{
    //====================================================================//
    // CODES DEFINITIONS
    //====================================================================//

    /**
     * Product Line: a sold product or service.
     *
     * The standard case: quantity x unit price, taxes applied.
     * This is the implicit type of any line without an explicit type.
     */
    public const PRODUCT = "product";

    /**
     * Shipping Line: delivery / carriage charges of the document.
     *
     * Most applications store shipping costs on the document itself rather
     * than as a line: connectors that expose them as a line MUST flag it,
     * so that the receiving application does not book it as a sold product.
     */
    public const SHIPPING = "shipping";

    /**
     * Fee Line: any extra charge or discount of the document.
     *
     * Covers handling fees, payment fees, eco-taxes, gift wrapping, and
     * global discounts (negative amounts). Not a sold product, but a real
     * amount impacting the document totals.
     */
    public const FEE = "fee";

    /**
     * Option Line: an optional line, not included in the document totals.
     *
     * Typical shape: quantity = 0 and unit price != 0. Used for quotations
     * to expose priced options the customer may add later.
     */
    public const OPTION = "option";

    /**
     * Comment Line: a free text line, without any accounting impact.
     *
     * Typical shape: quantity = 1 and unit price = 0. Used for section
     * titles, delivery instructions or any note printed on the document.
     */
    public const COMMENT = "comment";

    /**
     * Default Line Type, applied when no type is provided.
     */
    public const DEFAULT = self::PRODUCT;

    /**
     * All Normalized Line Types, with Human-Readable Labels.
     *
     * @var array<string, string>
     */
    public const ALL = array(
        self::PRODUCT => "Product / Service",
        self::SHIPPING => "Shipping Charges",
        self::FEE => "Fee / Discount",
        self::OPTION => "Optional Line",
        self::COMMENT => "Comment",
    );

    /**
     * Line Type Field Template Code (Splash\Templates\Accounting\Items\Type).
     *
     * Stored as a template code (dotted class) so that phpcore does not
     * require splash/scopes: the template is resolved at runtime only.
     */
    public const TEMPLATE = "Splash.Templates.Accounting.Items.Type";

    //====================================================================//
    // CHOICES
    //====================================================================//

    /**
     * Get All Possible Normalized Choices
     *
     * @return string[]
     */
    public static function getChoices(): array
    {
        return self::ALL;
    }

    //====================================================================//
    // FAST CHECKERS
    //====================================================================//

    /**
     * Check if this Line is a Product / Service Line
     *
     * Also true for empty values: no type means a regular product line.
     */
    public static function isProduct(?string $lineType): bool
    {
        return empty($lineType) || (self::PRODUCT === $lineType);
    }

    /**
     * Check if this Line is a Shipping Charges Line
     */
    public static function isShipping(?string $lineType): bool
    {
        return self::SHIPPING === $lineType;
    }

    /**
     * Check if this Line is a Fee / Discount Line
     */
    public static function isFee(?string $lineType): bool
    {
        return self::FEE === $lineType;
    }

    /**
     * Check if this Line is an Optional Line
     */
    public static function isOption(?string $lineType): bool
    {
        return self::OPTION === $lineType;
    }

    /**
     * Check if this Line is a Comment Line
     */
    public static function isComment(?string $lineType): bool
    {
        return self::COMMENT === $lineType;
    }

    /**
     * Check if this Line impacts the Document Totals
     *
     * Optional & comment lines are informative only: they must never be
     * summed into the document totals.
     */
    public static function isAccountable(?string $lineType): bool
    {
        return !self::isOption($lineType) && !self::isComment($lineType);
    }
}
