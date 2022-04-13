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
 * Price Field : price definition Array
 *
 * //====================================================================//
 * // Price Definition Array
 * // Sample : Required Informations
 * // $data["price"]["base"]           =>  BOOL      Reference Price With or Without Tax? True => With VAT
 * // $data["price"]["ht"]             =>  DOUBLE    Price Without Tax
 * // $data["price"]["ttc"]            =>  DOUBLE    Price With Tax
 * // $data["price"]["vat"]            =>  DOUBLE    VAT Tax in Percent
 * // $data["price"]["tax"]            =>  DOUBLE    VAT Tax amount
 * // Sample : Optional Informations
 * // $data["price"]["symbol"]         =>  STRING    Currency Symbol
 * // $data["price"]["code"]           =>  STRING    Currency Code
 * // $data["price"]["name"]           =>  STRING    Currency Name
 * // Where code field is a valid SPL_T_CURRENCY Iso Currency Code
 * //====================================================================//
 */
class OoPrice implements FieldInterface
{
    //==============================================================================
    //      Structural Data
    //==============================================================================

    /**
     * @var string
     */
    const FORMAT = 'Price';

    //==============================================================================
    //      DATA VALIDATION
    //==============================================================================

    /**
     * {@inheritdoc}
     */
    public static function validate($data): ?string
    {
        //==============================================================================
        //      Verify Data is an Array
        if (is_scalar($data)) {
            return "Field Data is not an Array.";
        }
        //==============================================================================
        //      Verify Data is an Array
        if (!is_array($data)) {
            return "Field Data is not an Array.";
        }

        //====================================================================//
        // Check Contents Available
        if (!self::validateContentsAvailability($data)) {
            return self::validateContentsAvailability($data);
        }

        //====================================================================//
        // Check Contents Type
        if (!self::validateCurrency($data)) {
            return self::validateCurrency($data);
        }

        //====================================================================//
        // Check Contents Type
        if (!self::validateContentsTypes($data)) {
            return self::validateContentsTypes($data);
        }

        return null;
    }

    //==============================================================================
    //      FAKE DATA GENERATOR
    //==============================================================================

    /**
     * {@inheritdoc}
     */
    public static function fake(array $settings)
    {
        $price = mt_rand(1000, 100000) / 100;
        $currency = !empty($settings["Currency"])       ?   $settings["Currency"]       :"EUR";
        $symbol = !empty($settings["CurrencySymbol"]) ?   $settings["CurrencySymbol"] :"&euro";
        $vat = $settings["VAT"] ?? 20;
        $type = !empty($settings["PriceBase"])      ?   $settings["PriceBase"]      :"HT";

        if ("HT" == $type) {
            return  self::encodePrice((double) $price, (double) $vat, null, $currency, $symbol);
        }

        return  self::encodePrice(null, (double) $vat, (double) $price, $currency, $symbol);
    }

    //==============================================================================
    //      DATA COMPARATOR (OPTIONAL)
    //==============================================================================

    /**
     * {@inheritdoc}
     */
    public static function compare($source, $target, array $settings): bool
    {
        //====================================================================//
        //  If Raw Text received, Not Array ==> Raw text Compare
        if (!is_array($source) && !is_array($target)) {
            return $source === $target;
        }
        //====================================================================//
        //  If mixed types received => Error
        if (!is_array($source) || !is_array($target)) {
            return false;
        }
        //====================================================================//
        // Compare Amounts
        if (!self::compareAmounts($source, $target, $settings)) {
            return false;
        }
        //====================================================================//
        // Compare Currency If Set on Both Sides
        if (!self::compareCurrency($source, $target)) {
            return false;
        }
        //====================================================================//
        // Prices Are Identical
        return true;
    }

    //====================================================================//
    //  PRICE TYPES MANAGEMENT
    //====================================================================//

    /**
     * Build a new price field array
     *
     * @param null|double $taxExcl Price Without VAT
     * @param double      $vat     VAT percentile
     * @param null|double $taxIncl Price With VAT
     * @param string      $code    Price Currency Code
     * @param string      $pic     Price Currency Symbol
     * @param string      $name    Price Currency Name
     *
     * @return array|string
     */
    public static function encodePrice(
        float $taxExcl = null,
        float $vat = 0.0,
        float $taxIncl = null,
        string $code = "",
        string $pic = "",
        string $name = ""
    ) {
        //====================================================================//
        // Safety Checks
        if (!is_double($taxExcl) && !is_double($taxIncl)) {
            return __FUNCTION__."Price Value is Invalid";
        }
        if (is_double($taxExcl) && is_double($taxIncl)) {
            return __FUNCTION__."Price Value is Invalid";
        }
        if (!is_double($vat)) {
            return __FUNCTION__."Price VAT is Invalid";
        }

        //====================================================================//
        // Build Price Array
        $price = array("vat" => $vat, "code" => $code,"symbol" => $pic,"name" => $name);
        if (is_double($taxExcl)) {
            $price["base"] = 0;
            $price["ht"] = $taxExcl;
            $price["tax"] = $taxExcl * ($vat / 100);
            $price["ttc"] = $taxExcl * (1 + $vat / 100);
        } else {
            $price["base"] = 1;
            $price["ht"] = $taxIncl / (1 + $vat / 100);
            $price["tax"] = $taxIncl - $price["ht"];
            $price["ttc"] = $taxIncl;
        }

        return $price;
    }

    /**
     * @param array $price
     *
     * @return null|string
     */
    private static function validateContentsAvailability(array $price): ?string
    {
        //====================================================================//
        // Check Contents Available
        if (!array_key_exists("base", $price)) {
            return "Price Field => 'base' price (ht/ttc) is missing.";
        }
        if (!array_key_exists("ht", $price)) {
            return "Price Field => 'ht' price is missing.";
        }
        if (!array_key_exists("ttc", $price)) {
            return "Price Field => 'ttc' price is missing.";
        }
        if (!array_key_exists("vat", $price)) {
            return "Price Field => 'vat' rate (%) is missing.";
        }
        if (!array_key_exists("tax", $price)) {
            return "Price Field => 'tax' total is missing.";
        }

        return null;
    }

    /**
     * @param array $price
     *
     * @return null|string
     */
    private static function validateCurrency(array $price): ?string
    {
        //====================================================================//
        // Check Contents Available
        if (!isset($price["symbol"])) {
            return "Price Field => Currency 'symbol' is missing.";
        }
        if (!isset($price["code"])) {
            return "Price Field => Currency 'code' is missing.";
        }
        if (!isset($price["name"])) {
            return "Price Field => Currency 'name' is missing.";
        }

        return null;
    }

    /**
     * @param array $price
     *
     * @return null|string
     */
    private static function validateContentsTypes(array $price): ?string
    {
        //====================================================================//
        // Check Contents Type
        if (!empty($price["ht"]) && !is_numeric($price["ht"])) {
            return "Price Field => 'ht' price is empty or non numeric.";
        }
        if (!empty($price["ttc"]) && !is_numeric($price["ttc"])) {
            return "Price Field => 'ttc' price is empty or non numeric.";
        }
        if (!empty($price["vat"]) && !is_numeric($price["vat"])) {
            return "Price Field => 'vat' rate is empty or non numeric.";
        }

        return null;
    }

    /**
     * @param array $source
     * @param array $target
     * @param array $settings
     *
     * @return bool
     */
    private static function compareAmounts(array $source, array $target, array $settings): bool
    {
        //====================================================================//
        // Compare Price
        if ($source["base"]) {
            if (!self::isEqualFloat($source["ttc"], $target["ttc"], $settings)) {
                return false;
            }
        } else {
            if (!self::isEqualFloat($source["ht"], $target["ht"], $settings)) {
                return false;
            }
        }
        //====================================================================//
        // Compare VAT
        if (!empty($source["vat"]) && !empty($target["vat"]) &&
                (!self::isEqualFloat($source["vat"], $target["vat"], $settings))) {
            return false;
        }

        return true;
    }

    /**
     * @param array $source
     * @param array $target
     *
     * @return bool
     */
    private static function compareCurrency(array $source, array $target): bool
    {
        //====================================================================//
        // Compare Currency If Set on Both Sides
        if (!empty($source["code"])) {
            return true;
        }
        if (!empty($target["code"])) {
            return true;
        }
        if ($source["code"] !== $target["code"]) {
            return false;
        }

        return true;
    }

    /**
     * @param float|string $source
     * @param float|string $target
     * @param array        $settings
     *
     * @return bool
     */
    private static function isEqualFloat($source, $target, array $settings): bool
    {
        //====================================================================//
        // Compare Float Values
        $srcFloat = round((float) $source, $settings["PricesPrecision"]);
        $targetFloat = round((float) $target, $settings["PricesPrecision"]);
        if (abs($srcFloat - $targetFloat) > 1E-6) {
            return false;
        }

        return true;
    }
}
