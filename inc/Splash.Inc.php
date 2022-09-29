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

/**
 * Global SplashSync Webservice Functions Constants & Definitions
 * DO NOT EDIT OR CHANGE ANY CONTENTS OF THIS FILE
 *
 * @author      B. Paquier <contact@splashsync.com>
 */

/**
 * CHANGES LOG
 *
 * Version 1.0:
 *  - Initial & Historical Version
 *
 * Version 1.1:
 *  - Add Stream Fields: Same to File with TTL (Time to Live) on Splash server
 *
 * Version 1.2:
 *  - Add Inline Fields: Short & Simple items list. Designed for Multi-select
 */

//====================================================================//
//====================================================================//
//  CONSTANTS DEFINITION
//====================================================================//
//====================================================================//

define('SPL_PROTOCOL', '1.2');

//====================================================================//
//====================================================================//
// SPL Objects Operations
// List of all available operations on objects modes
//====================================================================//
//====================================================================//
define('SPL_A_IMPORT', 'import');    // Object is imported from a remote Node
define('SPL_A_EXPORT', 'export');    // Object is exported to a remote Node
define('SPL_A_UPDATE', 'update');    // Object was localy modified
define('SPL_A_UCREATE', 'ucreate');   // Object was localy modified but doesn't exist on remote
define('SPL_A_CREATE', 'create');    // Object localy created
define('SPL_A_DELETE', 'delete');    // Object localy deleted
define('SPL_A_RUPDATE', 'rupdate');   // Object was localy modified
define('SPL_A_RUCREATE', 'rucreate');  // Object was remotly modified but doesn't exist localy
define('SPL_A_RCREATE', 'rcreate');   // Object remotly created
define('SPL_A_RDELETE', 'rdelete');   // Object remotly deleted
define('SPL_A_RENAME', 'rename');    // Object Identifier Was Modified
define('SPL_A_UNLINK', 'unlink');    // Object Link Deleted

//====================================================================//
//====================================================================//
// SplashSync Data Types
// List of all available data type used in transactions
//====================================================================//
// These Data Types are used to define available data on slave side.
// For any objects, remote slave will return a complete list of all
// available data. These list is used to setup synchronisation
//====================================================================//
//====================================================================//

//====================================================================//
// Single Fields, Shared in a single named variable
// Sample :
// $data["name"] = $value
//====================================================================//
define('SPL_T_BOOL', 'bool');               // Boolean, stored as 0 or 1
define('SPL_T_INT', 'int');                 // Signed Integer
define('SPL_T_DOUBLE', 'double');           // Signed Double, used for all float values
define('SPL_T_VARCHAR', 'varchar');         // Short texts (Inf 256 char)
define('SPL_T_TEXT', 'text');               // Long text
define('SPL_T_EMAIL', 'email');             // Email Address
define('SPL_T_PHONE', 'phone');             // Phone Number
define('SPL_T_DATE', 'date');               // Day Timestamps
define('SPL_T_DATECAST', 'Y-m-d');          // Day Timestamps Format
define('SPL_T_DATETIME', 'datetime');       // Timestamps
define('SPL_T_DATETIMECAST', 'Y-m-d H:i:s');// Timestamps Format
define('SPL_T_LANG', 'lang');               // Iso Language code (en_US / fr_FR ...)
define('SPL_T_COUNTRY', 'country');         // Iso country code (FR / US ...)
define('SPL_T_STATE', 'state');             // Iso state code (CA / FR ...)
define('SPL_T_CURRENCY', 'currency');       // Iso Currency code (EUR / USD ... )
define('SPL_T_URL', 'url');                 // External Url

//====================================================================//
//====================================================================//
// Structured Fields, Shared in a standard array of named variable
//====================================================================//
//====================================================================//

//====================================================================//
// File Structure
//====================================================================//
// Sample :
// $data["file"]["name"]        =>      File Name/Description
// $data["file"]["file"]        =>      File Identifier to Require File from Server
// $data["file"]["filename"]    =>      Filename with Extension
// $data["file"]["path"]        =>      Full File path on client system
// $data["file"]["url"]         =>      Complete Public Url, Usable for Direct Download
// $data["file"]["md5"]         =>      File Md5 Checksum
// $data["file"]["size"]        =>      File Size in Bytes
//====================================================================//
define('SPL_T_FILE', 'file');

//====================================================================//
// Image Structure
//====================================================================//
// Sample :
// $data["image"]["name"]       =>      Image Name
// $data["image"]["file"]       =>      Image Identifier to Require File from Server
// $data["image"]["filename"]   =>      Image Filename with Extension
// $data["image"]["path"]       =>      Image Full path on local system
// $data["image"]["url"]        =>      Complete Public Url, Used to display image
// $data["image"]["t_url"]      =>      Complete Thumb Public Url, Used to display image
// $data["image"]["width"]      =>      Image Width In Px
// $data["image"]["height"]     =>      Image Height In Px
// $data["image"]["md5"]        =>      Image File Md5 Checksum
// $data["image"]["size"]       =>      Image File Size
//====================================================================//
define('SPL_T_IMG', 'image');

//====================================================================//
// Stream File Structure
//====================================================================//
// Sample :
// $data["file"]["name"]        =>      File Name/Description
// $data["file"]["file"]        =>      File Identifier to Require File from Server
// $data["file"]["filename"]    =>      Filename with Extension
// $data["file"]["path"]        =>      Full File path on client system
// $data["file"]["url"]         =>      Complete Public Url, Usable for Direct Download
// $data["file"]["md5"]         =>      File Md5 Checksum
// $data["file"]["size"]        =>      File Size in Bytes
// $data["file"]["ttl"]         =>      Time to Live (in Days)
//====================================================================//
define('SPL_T_STREAM', 'stream');

//====================================================================//
// Multilangual Fields, Shared as Single Fields with Iso Language code #tag
//====================================================================//
// Sample :
// $data["name"]["iso_code"]            =>      Value
// Where name is field name and code is a valid SPL_T_LANG Iso Language Code
//====================================================================//
define('SPL_T_MVARCHAR', 'mvarchar');   // Mulitlangual Short texts (Inf 256 char)
define('SPL_T_MTEXT', 'mtext');      // Mulitlangual Long text

//====================================================================//
// Price Fields, Shared as an array including all price informations
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
define('SPL_T_PRICE', 'price');        // Price definition array

//====================================================================//
// Fields Lists
//====================================================================//
// Declared as SPL_T_XX@SPL_T_LIST
// Shared as fieldname@listname
// Multiple Fields may be attached to same List Name
//====================================================================//
define('SPL_T_LIST', 'list');             // Object List
define('LISTSPLIT', '@');                // Object List Splitter

//====================================================================//
// Inline Lists
//====================================================================//
// Store an Array of Values as a JSON String
//
// Example:
//  - ["value1", "value2", "value3"]
//
//====================================================================//
define('SPL_T_INLINE', 'inline');

//====================================================================//
// Object Identifier Field
//====================================================================//
// Declared as any other field type, this type is used to identify Objects
// links between structures.
//
// How does it works :
//
//    - Identifier uses a specific format : ObjectId:@@:TypeName
//      where ObjectId is Object Identifier on Local System and
//      TypeName is the standard OsWs Type of this object.
//      => ie : Product with Id 56 is : 56:@@:Products
//
//    - When reading an object, you can add Object identifiers field
//      in any data structure, or list.
//
//    - Before Data CheckIn or CheckOut, OsWs Scan all data and :
//      => Translate already linked object from Local to Remote Server
//      => Import or Export Missing Objects on Local or Remote Server
//      => Return Translated Objects Id to requested server
//
//====================================================================//
define('SPL_T_ID', 'objectid');         // Object Id
define('IDSPLIT', '::');               // Object Id Splitter

//====================================================================//
//====================================================================//
// SPL Sync Mode
// List of all available synchronisation modes
//====================================================================//
//====================================================================//
define('SPL_M_NOSYNC', 'sync-none');        // This type of objects are not sync
define('SPL_M_MINE', 'sync-mine');        // Only modifications done on Master are exported to Slave
define('SPL_M_THEIRS', 'sync-theirs');      // Only modifications done on Slave are imported to Master
define('SPL_M_BOTH', 'sync-both');        // All modifications done are sync between Master and Slave

//====================================================================//
//--------------------------------------------------------------------//
//==== Main Available WebServices                                 ====//
//--------------------------------------------------------------------//
//====================================================================//
define('SPL_S_PING', "Ping");                   // Connexion tests, only to check availabilty & access of remote server
define('SPL_S_CONNECT', "Connect");                // Connect to remote and read server informations
define('SPL_S_ADMIN', "Admin");                  // Global Remote Shop information retrieval
define('SPL_S_OBJECTS', "Objects");                // Common Data Transactions
define('SPL_S_FILE', "Files");                  // Files exchenges functions
define('SPL_S_WIDGETS', "Widgets");                // Information Blocks Retieval functions

//====================================================================//
//--------------------------------------------------------------------//
//==== Webservice : Admin                                         ====//
//--------------------------------------------------------------------//
//====================================================================//
//  Available Functions
//====================================================================//
define("SPL_F_GET_INFOS", 'infos');    // Get Server Informations (Name, Address and more...)
define("SPL_F_GET_OBJECTS", 'objects');  // Get List of Available Objects
define("SPL_F_GET_SELFTEST", 'selftest'); // Get Result of SelfTest Sequence
define("SPL_F_GET_WIDGETS", 'widgets');  // Get List of Available Widgets

//====================================================================//
//--------------------------------------------------------------------//
//==== Webservice : Objects                                       ====//
//--------------------------------------------------------------------//
//====================================================================//
//  Available Functions
//====================================================================//
define("SPL_F_OBJECTS", 'Objects');     // Get List of Available Objects
define("SPL_F_COMMIT", 'Commit');       // Commit Object Change on Server
define("SPL_F_DESC", 'Description');    // Read Object Description
define("SPL_F_FIELDS", 'Fields');       // Read Object Available Fields List
define("SPL_F_LIST", 'ObjectsList');    // Read Object List
define("SPL_F_IDENTIFY", 'Identify');   // Identify Object by Primary Keys
define("SPL_F_GET", 'Get');             // Read Object Data
define("SPL_F_SET", 'Set');             // Write Object Data
define("SPL_F_DEL", 'Delete');          // Delete An Object

//====================================================================//
//--------------------------------------------------------------------//
//==== Webservice : File                                          ====//
//--------------------------------------------------------------------//
//====================================================================//
//  Available Functions
//====================================================================//
define("SPL_F_ISFILE", 'isFile');          // Check if file exist
define("SPL_F_GETFILE", 'ReadFile');        // Download file from slave
define("SPL_F_SETFILE", 'SetFile');         // Upload file to slave
define("SPL_F_DELFILE", 'DeleteFile');      // Delete file from slave

//====================================================================//
//--------------------------------------------------------------------//
//==== Webservice : Widgets                                       ====//
//--------------------------------------------------------------------//
//====================================================================//
//  Available Functions
//====================================================================//
define("SPL_F_WIDGET_LIST", 'WidgetsList');     // Get List of Available Widgets
define("SPL_F_WIDGET_DEFINITION", 'Description');     // Get Widget Definition
define("SPL_F_WIDGET_GET", 'Get');             // Get Informations
