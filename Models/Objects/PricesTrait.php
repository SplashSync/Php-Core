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

namespace   Splash\Models\Objects;

use Splash\Core\SplashCore      as Splash;

/**
 * @abstract    This class implements access to Prices Fields Helper.
 */
trait PricesTrait
{
    /**
     * @var Static Class Storage
     */
    private static    $PricesHelper;
    
    /**
     *      @abstract   Get a singleton Prices Helper Class
     * 
     *      @return     PricesHelper
     */    
    public static function Prices()
    {
        // Helper Class Exists
        if (isset(self::$PricesHelper)) {
            return self::$PricesHelper;
        }
        // Initialize Class
        self::$PricesHelper        = new PricesHelper();  
        // Return Helper Class
        return self::$PricesHelper;
    }  
}

/**
 * @abstract    Helper for Prices Fields Management
 */
class PricesHelper
{
    /**
     * @abstract   Build a new price field array 
     * 
     * @param      double      $HT             Price Without VAT (Or Null if Price Send with VAT)
     * @param      double      $VAT            VAT percentile
     * @param      double      $TTC            Price With VAT
     * @param      string      $Code           Price Currency Code
     * @param      string      $Symbol         Price Currency Symbol
     * @param      string      $Name           Price Currency Name
     * 
     * @return     array
     */    
    public static function Encode($HT, $VAT, $TTC=Null, $Code="",$Symbol="",$Name="")
    {
        //====================================================================//
        // Safety Checks 
        if ( !is_double($HT) && !is_double($TTC) ) {
            Splash::Log()->Err("ErrPriceInvalid",__FUNCTION__);
            return "Error Invalid Price";
        }
        if ( is_double($HT) && is_double($TTC) ) {
            Splash::Log()->Err("ErrPriceBothValues",__FUNCTION__);
            return "Error Too Much Input Values";
        }
        if ( !is_double($VAT) ) {
            Splash::Log()->Err("ErrPriceNoVATValue",__FUNCTION__);
            return "Error Invalid VAT";
        }
        if ( empty($Code) ) {
            Splash::Log()->Err("ErrPriceNoCurrCode",__FUNCTION__);
            return "Error no Currency Code";
        }
        //====================================================================//
        // Build Price Array
        $Price = array("vat" => $VAT, "code" => $Code,"symbol" => $Symbol,"name" => $Name);
        if ( !is_null($HT) ) {
            $Price["base"]  =    0;
            $Price["ht"]    =    $HT;
            $Price["tax"]   =    $HT * ( $VAT/100);
            $Price["ttc"]   =    $HT * (1 + $VAT/100);
        }
        else {
            $Price["base"]  =    1;
            $Price["ht"]    =    $TTC / (1 + $VAT/100);
            $Price["tax"]   =    $TTC - $Price["ht"];
            $Price["ttc"]   =    $TTC;
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
    public static function Compare($Price1,$Price2)
    {
        //====================================================================//
        // Check Both Prices are valid
        if ( !self::isValid($Price1) || !self::isValid($Price2))  {
            Splash::Log()->War(__FUNCTION__ . " : Given Prices are invalid" );
            if ( !self::isValid($Price1) )  {
                Splash::Log()->www(__FUNCTION__ . " Price 1" , $Price1 );
            }
            if ( !self::isValid($Price2) )  {
                Splash::Log()->www(__FUNCTION__ . " Price 2" , $Price2 );
            }
            return False;
        }
        //====================================================================//
        // Compare Base Price
        if ( ((bool) $Price1["base"]) != ((bool) $Price2["base"]) ) {
            return False;
        }
        //====================================================================//
        // Compare Price
        if ( $Price1["base"] ) {
            if ( abs($Price1["ttc"] - $Price2["ttc"]) > 1E-6 ) {
                return False;
            }
        } else {
            if ( abs($Price1["ht"] - $Price2["ht"]) > 1E-6 ) {
                return False;
            }
        }
        //====================================================================//
        // Compare VAT
        if ( abs($Price1["vat"] - $Price2["vat"]) > 1E-6 ) {
            return False;
        }
        //====================================================================//
        // Compare Currency If Set on Both Sides
        if ( empty($Price1["code"]) ) {    return True;    }
        if ( empty($Price2["code"]) ) {    return True;    }
        if ( $Price1["code"] !== $Price2["code"] ) {
            return False;
        }
        //====================================================================//
        // Prices Are Identical
        return True;
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
        if ( !is_array($Price) && !is_a($Price, "ArrayObject"))         {      return False;   }
        if ( !array_key_exists("base",$Price) )                         {      return False;   }
        if ( !array_key_exists("ht",$Price) )                           {      return False;   }
        if ( !array_key_exists("ttc",$Price) )                          {      return False;   }
        if ( !array_key_exists("vat",$Price) )                          {      return False;   }
        if ( !array_key_exists("tax",$Price) )                          {      return False;   }
        if ( !array_key_exists("symbol",$Price))                        {      return False;   }
        if ( !array_key_exists("code",$Price) )                         {      return False;   }
        if ( !array_key_exists("name",$Price) )                         {      return False;   }
        
        
        //====================================================================//
        // Check Contents Type
        if ( !empty($Price["ht"]) && !is_numeric($Price["ht"]) )         {      return False;   }
        if ( !empty($Price["ttc"]) && !is_numeric($Price["ttc"]) )       {      return False;   }
        if ( !empty($Price["vat"]) && !is_numeric($Price["vat"]) )       {      return False;   }

        return TRUE;
    }  
    
    /**
     *  @abstract   Extract Data from Price Array
     *  
     *  @param      array       $Price          Price field definition Array
     *  @param      string      $Key            Data Key
     *  
     *  @return     double                       
     */    
    public static function Extract($Price , $Key = "ht")
    {
        // Check Contents
        if ( !isset($Price[$Key]) )                                     {      return False;   }
        if ( !empty($Price[$Key]) && !is_numeric($Price[$Key]) )        {      return False;   }
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
    public static function TaxExcluded($Price)
    {
        return self::Extract($Price, 'ht');
    }  

    /**
     *  @abstract   Extract Price with VAT from Price Array
     *  
     *  @param      array       $Price          Price field definition Array
     *  
     *  @return     double                       
     */    
    public static function TaxIncluded($Price)
    {
        return self::Extract($Price, 'ttc');
    }       
        
    /**
     *  @abstract   Extract Price with VAT from Price Array
     *  
     *  @param      array       $Price          Price field definition Array
     *  
     *  @return     double                       
     */    
    public static function TaxPercent($Price)
    {
        return self::Extract($Price, 'vat');
    }    
}

?>