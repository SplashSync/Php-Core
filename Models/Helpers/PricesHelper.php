<?php
/**
 * This file is part of SplashSync Project.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 *  @author    Splash Sync <www.splashsync.com>
 *  @copyright 2015-2017 Splash Sync
 *  @license   GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007
 *
 **/

namespace   Splash\Models\Helpers;

use Splash\Core\SplashCore      as Splash;

/**
 * @abstract    Helper for Prices Fields Management
 */
class PricesHelper
{
    /**
     * @abstract   Build a new price field array
     *
     * @param      double      $TaxExcl        Price Without VAT (Or Null if Price Send with VAT)
     * @param      double      $VAT            VAT percentile
     * @param      double      $TaxIncl        Price With VAT
     * @param      string      $Code           Price Currency Code
     * @param      string      $Symbol         Price Currency Symbol
     * @param      string      $Name           Price Currency Name
     *
     * @return     array
     */
    public static function encode($TaxExcl, $VAT, $TaxIncl = null, $Code = "", $Symbol = "", $Name = "")
    {
        //====================================================================//
        // Safety Checks
        if (!is_double($TaxExcl) && !is_double($TaxIncl)) {
            Splash::log()->err("ErrPriceInvalid", __FUNCTION__);
            return "Error Invalid Price";
        }
        if (is_double($TaxExcl) && is_double($TaxIncl)) {
            Splash::log()->err("ErrPriceBothValues", __FUNCTION__);
            return "Error Too Much Input Values";
        }
        if (!is_double($VAT)) {
            Splash::log()->err("ErrPriceNoVATValue", __FUNCTION__);
            return "Error Invalid VAT";
        }
        if (empty($Code)) {
            Splash::log()->err("ErrPriceNoCurrCode", __FUNCTION__);
            return "Error no Currency Code";
        }
        //====================================================================//
        // Build Price Array
        $Price = array("vat" => $VAT, "code" => $Code,"symbol" => $Symbol,"name" => $Name);
        if (!is_null($TaxExcl)) {
            $Price["base"]  =    0;
            $Price["ht"]    =    $TaxExcl;
            $Price["tax"]   =    $TaxExcl * ($VAT/100);
            $Price["ttc"]   =    $TaxExcl * (1 + $VAT/100);
        } else {
            $Price["base"]  =    1;
            $Price["ht"]    =    $TaxIncl / (1 + $VAT/100);
            $Price["tax"]   =    $TaxIncl - $Price["ht"];
            $Price["ttc"]   =    $TaxIncl;
        }
        return $Price;
    }
    
    /**
     * @abstract   Read price without Vat
     *
     * @param      array       $Price1          Price field Array
     * @param      array       $Price2          Price field Array
     *
     * @return     boolean                      return true if Price are identical
     */
    public static function compare($Price1, $Price2)
    {
        //====================================================================//
        // Check Both Prices are valid
        if (!self::isValid($Price1) || !self::isValid($Price2)) {
            Splash::log()->war(__FUNCTION__ . " : Given Prices are invalid");
            if (!self::isValid($Price1)) {
                Splash::log()->www(__FUNCTION__ . " Price 1", $Price1);
            }
            if (!self::isValid($Price2)) {
                Splash::log()->www(__FUNCTION__ . " Price 2", $Price2);
            }
            return false;
        }
        //====================================================================//
        // Compare Base Price
        if (((bool) $Price1["base"]) != ((bool) $Price2["base"])) {
            return false;
        }
        //====================================================================//
        // Compare Price
        return self::compareAmounts($Price1, $Price2);
    }
    
    public static function compareAmounts($Price1, $Price2)
    {
        //====================================================================//
        // Compare Price
        if ($Price1["base"]) {
            if (abs($Price1["ttc"] - $Price2["ttc"]) > 1E-6) {
                return false;
            }
        } else {
            if (abs($Price1["ht"] - $Price2["ht"]) > 1E-6) {
                return false;
            }
        }
        //====================================================================//
        // Compare VAT
        if (abs($Price1["vat"] - $Price2["vat"]) > 1E-6) {
            return false;
        }
        //====================================================================//
        // Compare Currency If Set on Both Sides
        if (empty($Price1["code"]) || empty($Price2["code"])) {
            return true;
        }
        if ($Price1["code"] !== $Price2["code"]) {
            return false;
        }
        //====================================================================//
        // Prices Are Identical
        return true;
    }
    
    /**
     *  @abstract   Verify price field array
     *
     *  @param      array       $Price          Price field definition Array
     *
     *  @return     bool
     */
    public static function isValid($Price)
    {
        //====================================================================//
        // Check Contents Available
        if (!is_array($Price) && !is_a($Price, "ArrayObject")) {
            return false;
        }
        if (!array_key_exists("base", $Price)) {
            return false;
        }
        if (!isset($Price["ht"]) || !isset($Price["ttc"]) || !isset($Price["vat"])) {
            return false;
        }
        if (!self::isValidAmount($Price)) {
            return false;
        }
        if (!self::isValidCurrency($Price)) {
            return false;
        }
        
        return true;
    }
    
    private static function isValidAmount($Price)
    {
        //====================================================================//
        // Check Contents Type
        if (!empty($Price["ht"]) && !is_numeric($Price["ht"])) {
            return false;
        }
        if (!empty($Price["ttc"]) && !is_numeric($Price["ttc"])) {
            return false;
        }
        if (!empty($Price["vat"]) && !is_numeric($Price["vat"])) {
            return false;
        }

        return true;
    }
    
    private static function isValidCurrency($Price)
    {
        //====================================================================//
        // Check Contents Available
        if (!array_key_exists("tax", $Price)) {
            return false;
        }
        if (!array_key_exists("symbol", $Price)) {
            return false;
        }
        if (!array_key_exists("code", $Price)) {
            return false;
        }
        if (!array_key_exists("name", $Price)) {
            return false;
        }
        return true;
    }
    
    /**
     *  @abstract   Extract Data from Price Array
     *
     *  @param      array       $Price          Price field definition Array
     *  @param      string      $Key            Data Key
     *
     *  @return     double
     */
    public static function extract($Price, $Key = "ht")
    {
        // Check Contents
        if (!isset($Price[$Key])) {
            return false;
        }
        if (!empty($Price[$Key]) && !is_numeric($Price[$Key])) {
            return false;
        }
        // Return Result
        return (double) $Price[$Key];
    }
    
    /**
     *  @abstract   Extract Price without VAT from Price Array
     *
     *  @param      array       $Price          Price field definition Array
     *
     *  @return     double
     */
    public static function taxExcluded($Price)
    {
        return self::extract($Price, 'ht');
    }

    /**
     *  @abstract   Extract Price with VAT from Price Array
     *
     *  @param      array       $Price          Price field definition Array
     *
     *  @return     double
     */
    public static function taxIncluded($Price)
    {
        return self::extract($Price, 'ttc');
    }
        
    /**
     *  @abstract   Extract Price with VAT from Price Array
     *
     *  @param      array       $Price          Price field definition Array
     *
     *  @return     double
     */
    public static function taxPercent($Price)
    {
        return self::extract($Price, 'vat');
    }
    
    /**
     *  @abstract   Extract Price VAT Ratio from Price Array
     *
     *  @param      array       $Price          Price field definition Array
     *
     *  @return     double
     */
    public static function taxRatio($Price)
    {
        return (double) self::extract($Price, 'vat') / 100;
    }
    
    
    /**
     *  @abstract   Extract Price Tax Amount from Price Array
     *
     *  @param      array       $Price          Price field definition Array
     *
     *  @return     double
     */
    public static function taxAmount($Price)
    {
        return self::extract($Price, 'tax');
    }
}
