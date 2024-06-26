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

namespace Splash\Models\Objects\ThirdParty;

use Splash\Components\FieldsFactory;

/**
 * Manage Access to ThirdParty & Address Contacts Civility Types
 */
class Civility
{
    public const UNKNOWN = 0;

    public const MALE = "male";

    public const FEMALE = "female";

    public const NEUTRAL = "neutral";

    public const ALL = array(
        self::MALE => "Male",
        self::FEMALE => "Female",
        self::NEUTRAL => "Non Binary",
    );

    public const MAP = array(
        "m" => self::MALE,
        "f" => self::FEMALE,
        "n" => self::NEUTRAL,
    );

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
     * Convert App Civility to Splash Normalized Value
     */
    public static function toSplash(?string $input, array $map = null): ?string
    {
        if (null === $input) {
            return null;
        }

        $map ??= static::MAP;

        return $map[$input] ?? null;
    }

    /**
     * Convert Splash Gender Type to Sellsy Civility
     */
    public static function toApp(?string $genderType, array $map = null): ?string
    {
        if (null === $genderType) {
            return null;
        }

        $map ??= static::MAP;
        $index = array_search($genderType, $map, false);
        if (false === $index) {
            return null;
        }

        return $index;
    }

    /**
     * Register User Civility Field
     */
    public static function registerCivilityField(FieldsFactory $factory, string $fieldId): FieldsFactory
    {
        $factory->create(SPL_T_VARCHAR)
            ->identifier($fieldId)
            ->name('Civility')
            ->description('Civility Type')
            ->microData("http://schema.org/Person", "gender")
            ->addChoices(self::getChoices())
        ;

        return $factory;
    }
}
