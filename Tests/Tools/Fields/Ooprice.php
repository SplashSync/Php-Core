<?php

namespace Splash\Tests\Tools\Fields;

/**
 * @abstract    Price Field : price definition Array
 *
//====================================================================//
// Price Definition Array
// Sample : Required Informations
// $data["price"]["base"]           =>  BOOL      Reference Price With or Without Tax? True => With VAT
// $data["price"]["ht"]             =>  DOUBLE    Price Without Tax
// $data["price"]["ttc"]            =>  DOUBLE    Price With Tax
// $data["price"]["vat"]            =>  DOUBLE    VAT Tax in Percent
// $data["price"]["tax"]            =>  DOUBLE    VAT Tax amount
// Sample : Optionnal Informations
// $data["price"]["symbol"]         =>  STRING    Currency Symbol
// $data["price"]["code"]           =>  STRING    Currency Code
// $data["price"]["name"]           =>  STRING    Currency Name
// Where code field is a valid SPL_T_CURRENCY Iso Currency Code
//====================================================================//
 *
 */
class Ooprice
{
    //==============================================================================
    //      Structural Data
    //==============================================================================

    protected $FORMAT        =   'Price';
    
    //==============================================================================
    //      DATA VALIDATION
    //==============================================================================

    /**
     * Verify given Raw Data is Valid
     *
     * @param   string $data
     *
     * @return bool     True if OK, Error String if KO
     */
    public static function validate($data)
    {
        //==============================================================================
        //      Verify Data is an Array
        if (!is_array($data) && !is_a($data, "ArrayObject")) {
            return "Field Data is not an Array.";
        }

        //====================================================================//
        // Check Contents Available
        if (!self::validateContentsAvailablility($data)) {
            return self::validateContentsAvailablility($data);
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

        return true;
    }
    
    private static function validateContentsAvailablility($price)
    {
        //====================================================================//
        // Check Contents Available
        if (!is_array($price) && !is_a($price, "ArrayObject")) {
            return "Price Field Data is not an Array.";
        }
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
        
        return true;
    }
    
    private static function validateCurrency($price)
    {
        //====================================================================//
        // Check Contents Available
        if (!array_key_exists("symbol", $price)) {
            return "Price Field => Currency 'symbol' is missing.";
        }
        if (!array_key_exists("code", $price)) {
            return "Price Field => Currency 'code' is missing.";
        }
        if (!array_key_exists("name", $price)) {
            return "Price Field => Currency 'name' is missing.";
        }
        
        return true;
    }
    
    private static function validateContentsTypes($price)
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

        return true;
    }
    
    //==============================================================================
    //      FAKE DATA GENERATOR
    //==============================================================================

    /**
     * @abstract    Generate Fake Raw Field Data for Debugger Simulations
     *
     * @param      array   $settings   User Defined Faker Settings
     *
     * @return mixed
     */
    public static function fake($settings)
    {
        $price      =   mt_rand(1000, 100000)/100;
        $currency   =   !empty($settings["Currency"])       ?   $settings["Currency"]       :"EUR";
        $symbol     =   !empty($settings["CurrencySymbol"]) ?   $settings["CurrencySymbol"] :"&euro";
        $vat        =   isset($settings["VAT"])             ?   $settings["VAT"]            :20;
        $type       =   !empty($settings["PriceBase"])      ?   $settings["PriceBase"]      :"HT";
        
        if ($type == "HT") {
            return  self::encodePrice((double) $price, (double) $vat, null, $currency, $symbol, "");
        } else {
            return  self::encodePrice(null, (double) $vat, (double) $price, $currency, $symbol, "");
        }
    }
    
    //==============================================================================
    //      DATA COMPARATOR (OPTIONNAL)
    //==============================================================================
    
    /**
     * Compare Two Data Block to See if similar (Update Required)
     *
     * !important : Target Data is always validated before compare
     *
     * @param       mixed   $source     Original Data Block
     * @param       mixed   $target     New Data Block
     * @param       array   $settings   User Defined Faker Settings
     *
     * @return  bool                TRUE if both Data Block Are Similar
     */
    public static function compare($source, $target, $settings)
    {
        
        //====================================================================//
        //  If Raw Text received, Not Array ==> Raw text Compare
        if (!is_array($source) && !is_a($target, "ArrayObject")
            && !is_array($target) && !is_a($target, "ArrayObject")) {
            return ($source === $target)?true:false;
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
    
    private static function compareAmounts($source, $target, $settings)
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
    
    private static function compareCurrency($source, $target)
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
    
    private static function isEqualFloat($source, $target, $settings)
    {
        //====================================================================//
        // Compare Float Values
        if (abs(round($source, $settings["PricesPrecision"]) - round($target, $settings["PricesPrecision"])) > 1E-6) {
            return false;
        }
        return true;
    }
    
    //====================================================================//
    //  PRICE TYPES MANAGEMENT
    //====================================================================//
    
    /**
     * @abstract    Build a new price field array
     * @param   double      $taxExcl        Price Without VAT
     * @param   double      $vat            VAT percentile
     * @param   double      $taxIncl        Price With VAT
     * @param   string      $code           Price Currency Code
     * @param   string      $symbol         Price Currency Symbol
     * @param   string      $name           Price Currency Name
     * @return  array                       Contact Firstname, Lastname & Compagny Name
     */
    public static function encodePrice($taxExcl, $vat, $taxIncl = null, $code = "", $symbol = "", $name = "")
    {
        //====================================================================//
        // Safety Checks
        if (!is_double($taxExcl) && !is_double($taxIncl)) {
            return __FUNCTION__ . "Price Value is Invalid";
        }
        if (is_double($taxExcl) && is_double($taxIncl)) {
            return __FUNCTION__ . "Price Value is Invalid";
        }
        if (!is_double($vat)) {
            return __FUNCTION__ . "Price VAT is Invalid";
        }
        
        //====================================================================//
        // Build Price Array
        $price = array("vat" => $vat, "code" => $code,"symbol" => $symbol,"name" => $name);
        if (is_double($taxExcl)) {
            $price["base"]  =    0;
            $price["ht"]    =    $taxExcl;
            $price["tax"]   =    $taxExcl * ($vat/100);
            $price["ttc"]   =    $taxExcl * (1 + $vat/100);
        } else {
            $price["base"]  =    1;
            $price["ht"]    =    $taxIncl / (1 + $vat/100);
            $price["tax"]   =    $taxIncl - $price["ht"];
            $price["ttc"]   =    $taxIncl;
        }
        return $price;
    }
}
