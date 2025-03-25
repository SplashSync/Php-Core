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

namespace Splash\Framework\Dictionary;

/**
 * Dictionary for All Splash Fields Types Names
 */
class SplFields
{
    //====================================================================//
    // Single / Simple Splash Sync  Data Fields
    //====================================================================//

    /**
     * Boolean, stored as 0 or 1
     */
    const BOOL = 'bool';

    /**
     * Signed Integer
     */
    const INT = 'int';

    /**
     * Signed Double, used for all float values
     */
    const DOUBLE = 'double';

    /**
     * Short texts (Inf 256 char)
     */
    const VARCHAR = 'varchar';

    /**
     * Long text
     */
    const TEXT = 'text';

    /**
     * Email Address
     */
    const EMAIL = 'email';

    /**
     * Phone Number
     */
    const PHONE = 'phone';

    /**
     * Day Timestamps
     */
    const DATE = 'date';

    /**
     * Timestamps
     */
    const DATETIME = 'datetime';

    /**
     * Iso Language code (en_US / fr_FR ...)
     */
    const LANG = 'lang';

    /**
     * Iso country code (FR / US ...)
     */
    const COUNTRY = 'country';

    /**
     * Iso state code (CA / FR ...)
     */
    const STATE = 'state';

    /**
     * Iso Currency code (EUR / USD ... )
     */
    const CURRENCY = 'currency';

    /**
     * External Url
     */
    const URL = 'url';

    //====================================================================//
    // Structured Fields, Shared in a standard array of named variable
    //====================================================================//

    /**
     * File Structure
     *
     * Sample :
     *    $data["file"]["name"]        =>      File Name/Description
     *    $data["file"]["file"]        =>      File Identifier to Require File from Server
     *    $data["file"]["filename"]    =>      Filename with Extension
     *    $data["file"]["path"]        =>      Full File path on client system
     *    $data["file"]["url"]         =>      Complete Public Url, Usable for Direct Download
     *    $data["file"]["md5"]         =>      File Md5 Checksum
     *    $data["file"]["size"]        =>      File Size in Bytes
     */
    const FILE = 'file';

    /**
     * Image Structure
     *
     * Sample:
     *    $data["image"]["name"]       =>      Image Name
     *    $data["image"]["file"]       =>      Image Identifier to Require File from Server
     *    $data["image"]["filename"]   =>      Image Filename with Extension
     *    $data["image"]["path"]       =>      Image Full path on local system
     *    $data["image"]["url"]        =>      Complete Public Url, Used to display image
     *    $data["image"]["t_url"]      =>      Complete Thumb Public Url, Used to display image
     *    $data["image"]["width"]      =>      Image Width In Px
     *    $data["image"]["height"]     =>      Image Height In Px
     *    $data["image"]["md5"]        =>      Image File Md5 Checksum
     *    $data["image"]["size"]       =>      Image File Size
     */
    const IMG = 'image';

    /**
     * Stream File Structure
     *
     * Sample :
     *    $data["file"]["name"]        =>      File Name/Description
     *    $data["file"]["file"]        =>      File Identifier to Require File from Server
     *    $data["file"]["filename"]    =>      Filename with Extension
     *    $data["file"]["path"]        =>      Full File path on client system
     *    $data["file"]["url"]         =>      Complete Public Url, Usable for Direct Download
     *    $data["file"]["md5"]         =>      File Md5 Checksum
     *    $data["file"]["size"]        =>      File Size in Bytes
     *    $data["file"]["ttl"]         =>      Time to Live (in Days)
     */
    const STREAM = 'stream';

    /**
     * Multilingual Varchar Fields, Shared as Single Fields with Iso Language code #tag
     *
     * Sample :
     *  $data["name"]["iso_code"] => Value
     *
     * Where name is field name and code is a valid LANG Iso Language Code
     *
     * @deprecated Use multiple varchar  fields instead
     */
    const M_VARCHAR = 'mvarchar';

    /**
     * Multilingual Text Fields, Shared as Single Fields with Iso Language code #tag
     *
     * Sample :
     *   $data["name"]["iso_code"] => Value
     *
     * Where name is field name and code is a valid LANG Iso Language Code
     *
     * @deprecated Use multiple text fields instead
     */
    const M_TEXT = 'mtext';

    /**
     * Price Fields, Shared as an array including complete price information
     *
     * Price Definition Array
     *
     * Sample : Required Information
     *    $data["price"]["base"]           =>  BOOL      Reference Price With or Without Tax? True => With VAT
     *    $data["price"]["ht"]             =>  DOUBLE    Price Without Tax
     *    $data["price"]["ttc"]            =>  DOUBLE    Price With Tax
     *    $data["price"]["vat"]            =>  DOUBLE    VAT Tax in Percent
     *    $data["price"]["tax"]            =>  DOUBLE    VAT Tax amount
     *
     * Sample : Optional Information
     *   $data["price"]["symbol"]          =>  STRING    Currency Symbol
     *   $data["price"]["code"]            =>  STRING    Currency Code
     *   $data["price"]["name"]            =>  STRING    Currency Name
     *
     * Where code field is a valid CURRENCY Iso Currency Code
     */
    const PRICE = 'price';

    /**
     * Declare a List of Fields Data
     *
     * List fields are declared as FIELD_TYPE@LIST, Shared as field_name@list_name
     *
     * Multiple Fields may be attached to same List
     */
    const LIST = 'list';

    /**
     * Inline Lists, store an Array of Values as a JSON String
     *
     * Example:
     *  - ["value1", "value2", "value3"]
     */
    const INLINE = 'inline';

    /**
     * Object Identifier Field, used to identify Object links between structures.
     *
     * Identifier uses a specific format : ObjectId::TypeName
     *
     * Samples:
     *  - Product with ID 56 is : 56::Products
     *  - ThirdParty with ID 33 is : 33::ThirdParty
     */
    const ID = 'objectid';
}
