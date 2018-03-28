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
class ooprice
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
     * @param   string $Price
     *
     * @return bool     True if OK, Error String if KO
     */
    public static function validate($Price)
    {
        //==============================================================================
        //      Verify Data is an Array
        if (!is_array($Price) && !is_a($Price, "ArrayObject")) {
            return "Field Data is not an Array.";
        }

        //====================================================================//
        // Check Contents Available
        if (!is_array($Price) && !is_a($Price, "ArrayObject")) {
            return "Price Field Data is not an Array.";
        }
        if (!array_key_exists("base", $Price)) {
            return "Price Field => 'base' price (ht/ttc) is missing.";
        }
        if (!array_key_exists("ht", $Price)) {
            return "Price Field => 'ht' price is missing.";
        }
        if (!array_key_exists("ttc", $Price)) {
            return "Price Field => 'ttc' price is missing.";
        }
        if (!array_key_exists("vat", $Price)) {
            return "Price Field => 'vat' rate (%) is missing.";
        }
        if (!array_key_exists("tax", $Price)) {
            return "Price Field => 'tax' total is missing.";
        }
        if (!array_key_exists("symbol", $Price)) {
            return "Price Field => Currency 'symbol' is missing.";
        }
        if (!array_key_exists("code", $Price)) {
            return "Price Field => Currency 'code' is missing.";
        }
        if (!array_key_exists("name", $Price)) {
            return "Price Field => Currency 'name' is missing.";
        }
        
        //====================================================================//
        // Check Contents Type
        if (!empty($Price["ht"]) && !is_numeric($Price["ht"])) {
            return "Price Field => 'ht' price is empty or non numeric.";
        }
        if (!empty($Price["ttc"]) && !is_numeric($Price["ttc"])) {
            return "Price Field => 'ttc' price is empty or non numeric.";
        }
        if (!empty($Price["vat"]) && !is_numeric($Price["vat"])) {
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
     * @param      array   $Settings   User Defined Faker Settings
     *
     * @return mixed
     */
    public static function fake($Settings)
    {
        $Price      =   mt_rand(1000, 100000)/100;
        $Currency   =   !empty($Settings["Currency"])       ?   $Settings["Currency"]       :"EUR";
        $Symbol     =   !empty($Settings["CurrencySymbol"]) ?   $Settings["CurrencySymbol"] :"&euro";
        $VAT        =   isset($Settings["VAT"])             ?   $Settings["VAT"]            :20;
        $Type       =   !empty($Settings["PriceBase"])      ?   $Settings["PriceBase"]      :"HT";
        
        if ($Type == "HT") {
            return  self::encodePrice((double) $Price, (double) $VAT, null, $Currency, $Symbol, "");
        } else {
            return  self::encodePrice(null, (double) $VAT, (double) $Price, $Currency, $Symbol, "");
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
     * @param       mixed   $Source     Original Data Block
     * @param       mixed   $Target     New Data Block
     * @param       array   $Settings   User Defined Faker Settings
     *
     * @return  bool                TRUE if both Data Block Are Similar
     */
    public static function compare($Source, $Target, $Settings)
    {
        
        //====================================================================//
        //  If Raw Text received, Not Array ==> Raw text Compare
        if (!is_array($Source) && !is_a($Target, "ArrayObject") && !is_array($Target) && !is_a($Target, "ArrayObject")) {
            return ($Source === $Target)?true:false;
        }
        //====================================================================//
        // Compare Price
        if ($Source["base"]) {
            if (!self::isEqualFloat($Source["ttc"], $Target["ttc"], $Settings)) {
                return false;
            }
        } else {
            if (!self::isEqualFloat($Source["ht"], $Target["ht"], $Settings)) {
                return false;
            }
        }
        //====================================================================//
        // Compare VAT
        if (!empty($Source["vat"]) && !empty($Target["vat"]) &&
                (!self::isEqualFloat($Source["vat"], $Target["vat"], $Settings))) {
            return false;
        }
        //====================================================================//
        // Compare Currency If Set on Both Sides
        if (!empty($Source["code"])) {
            return true;
        }
        if (!empty($Target["code"])) {
            return true;
        }
        if ($Source["code"] !== $Target["code"]) {
            return false;
        }
        //====================================================================//
        // Prices Are Identical
        return true;
    }
    
    private static function isEqualFloat($Source, $Target, $Settings)
    {
        //====================================================================//
        // Compare Float Values
        if (abs(round($Source, $Settings["PricesPrecision"]) - round($Target, $Settings["PricesPrecision"])) > 1E-6) {
            return false;
        }
        return true;
    }
    
    //====================================================================//
    //  PRICE TYPES MANAGEMENT
    //====================================================================//
    
    /**
    *   @abstract   Build a new price field array
    *   @param      double      $HT             Price Without VAT
    *   @param      double      $VAT            VAT percentile
    *   @param      double      $TTC            Price With VAT
    *   @param      string      $Code           Price Currency Code
    *   @param      string      $Symbol         Price Currency Symbol
    *   @param      string      $Name           Price Currency Name
    *   @return     array                      Contact Firstname, Lastname & Compagny Name
    */
    public static function encodePrice($HT, $VAT, $TTC=null, $Code="", $Symbol="", $Name="")
    {
        //====================================================================//
        // Safety Checks
        if (!is_double($HT) && !is_double($TTC)) {
            return __FUNCTION__ . "Price Value is Invalid";
        }
        if (is_double($HT) && is_double($TTC)) {
            return __FUNCTION__ . "Price Value is Invalid";
        }
        if (!is_double($VAT)) {
            return __FUNCTION__ . "Price VAT is Invalid";
        }
        
        //====================================================================//
        // Build Price Array
        $Price = array("vat" => $VAT, "code" => $Code,"symbol" => $Symbol,"name" => $Name);
        if (is_double($HT)) {
            $Price["base"]  =    0;
            $Price["ht"]    =    $HT;
            $Price["tax"]   =    $HT * ($VAT/100);
            $Price["ttc"]   =    $HT * (1 + $VAT/100);
        } else {
            $Price["base"]  =    1;
            $Price["ht"]    =    $TTC / (1 + $VAT/100);
            $Price["tax"]   =    $TTC - $Price["ht"];
            $Price["ttc"]   =    $TTC;
        }
        return $Price;
    }
}
