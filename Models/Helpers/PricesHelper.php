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

namespace   Splash\Models\Helpers;

use Splash\Core\SplashCore      as Splash;

/**
 * Helper for Prices Fields Management
 */
class PricesHelper
{
    /**
     * Build a new price field array
     *
     * @param null|float $taxExcl Price Without VAT (Or Null if Price Send with VAT)
     * @param float      $vat     VAT percentile
     * @param null|float $taxIncl Price With VAT
     * @param string     $code    Price Currency Code
     * @param string     $symbol  Price Currency Symbol
     * @param string     $name    Price Currency Name
     *
     * @return null|array
     */
    public static function encode(
        ?float $taxExcl,
        float $vat,
        float $taxIncl = null,
        string $code = "",
        string $symbol = "",
        string $name = ""
    ): ?array {
        //====================================================================//
        // Safety Checks
        if (!is_float($taxExcl) && !is_float($taxIncl)) {
            return Splash::log()->errNull("ErrPriceInvalid", __FUNCTION__);
        }
        if (is_float($taxExcl) && is_float($taxIncl)) {
            return Splash::log()->errNull("ErrPriceBothValues", __FUNCTION__);
        }
        if ($vat < 0.0) {
            return Splash::log()->errNull("ErrPriceNoVATValue", __FUNCTION__);
        }
        if (empty($code)) {
            return Splash::log()->errNull("ErrPriceNoCurrCode", __FUNCTION__);
        }
        //====================================================================//
        // Build Price Array
        $price = array("vat" => $vat, "code" => $code,"symbol" => $symbol,"name" => $name);
        if (!is_null($taxExcl)) {
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
     * Compare Two Price Array
     *
     * @param mixed $price1    Price field Array
     * @param mixed $price2    Price field Array
     * @param int   $precision Number of Decimals to Check
     *
     * @return bool
     */
    public static function compare($price1, $price2, int $precision = 6): bool
    {
        //====================================================================//
        // Check Both Prices are valid
        if (!($price1 = self::isValid($price1)) || !($price2 = self::isValid($price2))) {
            Splash::log()->war(__FUNCTION__." : Given Prices are invalid");
            if (!self::isValid($price1)) {
                Splash::log()->www(__FUNCTION__." Price 1", $price1);
            }
            if (!self::isValid($price2)) {
                Splash::log()->www(__FUNCTION__." Price 2", $price2);
            }

            return false;
        }
        //====================================================================//
        // Compare Base Price
        if (((bool) $price1["base"]) != ((bool) $price2["base"])) {
            return false;
        }
        //====================================================================//
        // Compare Price
        return self::compareAmounts($price1, $price2, $precision);
    }

    /**
     * Compare Two Price Array without Validation
     *
     * @param array $price1    Price field Array
     * @param array $price2    Price field Array
     * @param int   $precision Number of Decimals to Check
     *
     * @return bool
     */
    public static function compareAmounts(array $price1, array $price2, int $precision = 6): bool
    {
        //====================================================================//
        // Build Compare Delta
        $delta = (float) ("1E-".$precision);
        //====================================================================//
        // Compare Price
        if ($price1["base"]) {
            if (abs($price1["ttc"] - $price2["ttc"]) > $delta) {
                return false;
            }
        } else {
            if (abs($price1["ht"] - $price2["ht"]) > $delta) {
                return false;
            }
        }
        //====================================================================//
        // Compare VAT
        if (abs($price1["vat"] - $price2["vat"]) > $delta) {
            return false;
        }
        //====================================================================//
        // Compare Currency If Set on Both Sides
        if (empty($price1["code"]) || empty($price2["code"])) {
            return true;
        }
        if ($price1["code"] !== $price2["code"]) {
            return false;
        }
        //====================================================================//
        // Prices Are Identical
        return true;
    }

    /**
     * Verify Price field array
     *
     * @param mixed $price Price field definition Array
     *
     * @return null|array
     */
    public static function isValid($price): ?array
    {
        if (!is_array($price)) {
            return null;
        }
        if (!isset($price["base"])) {
            return null;
        }
        if (!isset($price["ht"]) || !isset($price["ttc"]) || !isset($price["vat"])) {
            return null;
        }
        if (!self::isValidAmount($price)) {
            return null;
        }
        if (!self::isValidCurrency($price)) {
            return null;
        }

        return $price;
    }

    /**
     * Extract Data from Price Array
     *
     * @param array  $price Price field definition Array
     * @param string $key   Data Key
     *
     * @return null|float
     */
    public static function extract(array $price, string $key = "ht"): ?float
    {
        // Check Contents
        if (!isset($price[$key])) {
            return null;
        }
        if (!empty($price[$key]) && !is_numeric($price[$key])) {
            return null;
        }
        // Return Result
        return (float) $price[$key];
    }

    /**
     * Extract Price without VAT from Price Array
     *
     * @param array $price Price field definition Array
     *
     * @return null|float
     */
    public static function taxExcluded(array $price): ?float
    {
        return self::extract($price);
    }

    /**
     * Extract Price with VAT from Price Array
     *
     * @param array $price Price field definition Array
     *
     * @return null|float
     */
    public static function taxIncluded(array $price): ?float
    {
        return self::extract($price, 'ttc');
    }

    /**
     * Extract Price with VAT from Price Array
     *
     * @param array $price Price field definition Array
     *
     * @return null|float
     */
    public static function taxPercent(array $price): ?float
    {
        return self::extract($price, 'vat');
    }

    /**
     * Extract Price VAT Ratio from Price Array
     *
     * @param array $price Price field definition Array
     *
     * @return float
     */
    public static function taxRatio($price): float
    {
        return (float) self::extract($price, 'vat') / 100;
    }

    /**
     * Extract Price Tax Amount from Price Array
     *
     * @param array $price Price field definition Array
     *
     * @return null|float
     */
    public static function taxAmount(array $price): ?float
    {
        return self::extract($price, 'tax');
    }

    /**
     * Verify Price Array Amount Infos are Available
     *
     * @param array $price Price field definition Array
     *
     * @return bool
     */
    private static function isValidAmount(array $price): bool
    {
        //====================================================================//
        // Check Contents Type
        if (!empty($price["ht"]) && !is_numeric($price["ht"])) {
            return false;
        }
        if (!empty($price["ttc"]) && !is_numeric($price["ttc"])) {
            return false;
        }
        if (!empty($price["vat"]) && !is_numeric($price["vat"])) {
            return false;
        }

        return true;
    }

    /**
     * Verify Price Array Currency Infos are Available
     *
     * @param array $price Price field definition Array
     *
     * @return bool
     */
    private static function isValidCurrency(array $price): bool
    {
        //====================================================================//
        // Check Contents Available
        if (!isset($price["tax"])) {
            return false;
        }
        if (!isset($price["symbol"])) {
            return false;
        }
        if (!isset($price["code"])) {
            return false;
        }
        if (!isset($price["name"])) {
            return false;
        }

        return true;
    }
}
