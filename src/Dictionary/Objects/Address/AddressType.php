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

namespace Splash\Core\Dictionary\Objects\Address;

/**
 * Manage Access to Address Normalized Types
 */
class AddressType
{
    /**
     * Billing Address: invoicing & administrative contact of the parent entity.
     */
    public const BILLING = "billing";

    /**
     * Delivery Address: shipping / logistics contact of the parent entity.
     */
    public const DELIVERY = "delivery";

    /**
     * Other Address: any address without a normalized role (home, office, ...).
     */
    public const OTHER = "other";

    /**
     * All Normalized Address Types, with Human-Readable Labels.
     *
     * @var array<string, string>
     */
    public const ALL = array(
        self::BILLING => "Billing Address",
        self::DELIVERY => "Delivery Address",
        self::OTHER => "Other",
    );

    /**
     * Address Type Field Template Code (Splash\Templates\Address\Core\Type).
     *
     * Stored as a template code (dotted class) so that phpcore does not
     * require splash/scopes: the template is resolved at runtime only.
     */
    public const TEMPLATE = "Splash.Templates.Address.Core.Type";

    /**
     * Get All Possible Normalized Choices
     *
     * @return string[]
     */
    public static function getChoices(): array
    {
        return self::ALL;
    }

    /**
     * Convert App Address Type to Splash Normalized Value
     */
    public static function toSplash(?string $input, ?array $map = null): ?string
    {
        if (null === $input) {
            return null;
        }

        $map ??= array();

        return $map[$input] ?? (array_key_exists($input, self::ALL) ? $input : null);
    }

    /**
     * Convert Splash Normalized Value to App Address Type
     */
    public static function toApp(?string $addressType, ?array $map = null): ?string
    {
        if (null === $addressType) {
            return null;
        }
        if (empty($map)) {
            return array_key_exists($addressType, self::ALL) ? $addressType : null;
        }
        $index = array_search($addressType, $map, false);
        if (false === $index) {
            return null;
        }

        return (string) $index;
    }
}
