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

/**
 * Boolean, stored as 0 or 1
 *
 * @deprecated use \Splash\Framework\Dictionary\SplFields instead
 */
define('SPL_T_BOOL', 'bool');

/**
 * Signed Integer
 *
 * @deprecated use \Splash\Framework\Dictionary\SplFields instead
 */
define('SPL_T_INT', 'int');

/**
 * Signed Double, used for all float values
 *
 * @deprecated use \Splash\Framework\Dictionary\SplFields instead
 */
define('SPL_T_DOUBLE', 'double');

/**
 * Short texts (Inf 256 char)
 *
 * @deprecated use \Splash\Framework\Dictionary\SplFields instead
 */
define('SPL_T_VARCHAR', 'varchar');

/**
 * Long text
 *
 * @deprecated use \Splash\Framework\Dictionary\SplFields instead
 */
define('SPL_T_TEXT', 'text');

/**
 * Email Address
 *
 * @deprecated use \Splash\Framework\Dictionary\SplFields instead
 */
define('SPL_T_EMAIL', 'email');

/**
 * Phone Number
 *
 * @deprecated use \Splash\Framework\Dictionary\SplFields instead
 */
define('SPL_T_PHONE', 'phone');

/**
 * Day Timestamps
 *
 * @deprecated use \Splash\Framework\Dictionary\SplFields instead
 */
define('SPL_T_DATE', 'date');

/**
 * Date Timestamps Format
 *
 * @deprecated use \Splash\Models\Helpers\DatesHelper instead
 */
define('SPL_T_DATECAST', 'Y-m-d');

/**
 * Datetime Timestamps
 *
 * @deprecated use \Splash\Framework\Dictionary\SplFields instead
 */
define('SPL_T_DATETIME', 'datetime');

/**
 * Datetime Timestamps Format
 *
 * @deprecated use \Splash\Models\Helpers\DatesHelper instead
 */
define('SPL_T_DATETIMECAST', 'Y-m-d H:i:s');

/**
 * Iso Language code (en_US / fr_FR ...)
 *
 * @deprecated use \Splash\Framework\Dictionary\SplFields instead
 */
define('SPL_T_LANG', 'lang');

/**
 * Iso Language code (en_US / fr_FR ...)
 *
 * @deprecated use \Splash\Framework\Dictionary\SplFields instead
 */
define('SPL_T_COUNTRY', 'country');

/**
 * Iso state code (CA / FR ...)
 *
 * @deprecated use \Splash\Framework\Dictionary\SplFields instead
 */

define('SPL_T_STATE', 'state');

/**
 * Iso Currency code (EUR / USD ... )
 *
 * @deprecated use \Splash\Framework\Dictionary\SplFields instead
 */

define('SPL_T_CURRENCY', 'currency');

/**
 * External Url
 *
 * @deprecated use \Splash\Framework\Dictionary\SplFields instead
 */
define('SPL_T_URL', 'url');

//====================================================================//
//====================================================================//
// Structured Fields, Shared in a standard array of named variable
//====================================================================//
//====================================================================//

/**
 * File Structure
 *
 * @deprecated use \Splash\Framework\Dictionary\SplFields instead
 */
define('SPL_T_FILE', 'file');

/**
 * Image Structure
 *
 * @deprecated use \Splash\Framework\Dictionary\SplFields instead
 */
define('SPL_T_IMG', 'image');

/**
 * Stream File Structure
 *
 * @deprecated use \Splash\Framework\Dictionary\SplFields instead
 */
define('SPL_T_STREAM', 'stream');

/**
 * Multilingual Short texts (Inf 256 char)
 *
 * @deprecated use \Splash\Framework\Dictionary\SplFields instead
 */
define('SPL_T_MVARCHAR', 'mvarchar');

/**
 * Multilingual Long text
 *
 * @deprecated use \Splash\Framework\Dictionary\SplFields instead
 */
define('SPL_T_MTEXT', 'mtext');

/**
 * Price definition array
 *
 * @deprecated use \Splash\Framework\Dictionary\SplFields instead
 */
define('SPL_T_PRICE', 'price');

//====================================================================//
// Fields Lists
//====================================================================//

/**
 * List Field Definition
 *
 * @deprecated use \Splash\Framework\Dictionary\SplFields instead
 */
define('SPL_T_LIST', 'list');

/**
 * List Field Splitter
 *
 * @deprecated use \Splash\Models\Helpers\ListsHelper instead
 */
define('LISTSPLIT', '@');

//====================================================================//
// Inline Lists
//====================================================================//

/**
 * Inline Field Definition
 *
 * @deprecated use \Splash\Framework\Dictionary\SplFields instead
 */
define('SPL_T_INLINE', 'inline');

//====================================================================//
// Object Identifier Field
//====================================================================//

/**
 * Object Identifier Field
 *
 * @deprecated use \Splash\Framework\Dictionary\SplFields instead
 */
define('SPL_T_ID', 'objectid');

/**
 * Object Identifier Splitter
 *
 * @deprecated use \Splash\Models\Helpers\ObjectsHelper instead
 */
define('IDSPLIT', '::');

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

/**
 * Connexion tests, only to check availability & access of remote server
 *
 * @deprecated use \Splash\Framework\Dictionary\Services instead
 */
define('SPL_S_PING', "Ping");

/**
 * Connect to remote and read server information
 *
 * @deprecated use \Splash\Framework\Dictionary\Services instead
 */
define('SPL_S_CONNECT', "Connect");

/**
 * Global Remote Shop information retrieval
 *
 * @deprecated use \Splash\Framework\Dictionary\Services instead
 */
define('SPL_S_ADMIN', "Admin");

/**
 * Common Data Transactions
 *
 * @deprecated use \Splash\Framework\Dictionary\Services instead
 */
define('SPL_S_OBJECTS', "Objects");

/**
 * Files exchanges functions
 *
 * @deprecated use \Splash\Framework\Dictionary\Services instead
 */
define('SPL_S_FILE', "Files");

/**
 * Information Blocks Retrieval functions
 *
 * @deprecated use \Splash\Framework\Dictionary\Services instead
 */
define('SPL_S_WIDGETS', "Widgets");

//====================================================================//
//--------------------------------------------------------------------//
//==== Webservice : Admin                                         ====//
//--------------------------------------------------------------------//
//====================================================================//
//  Available Functions
//====================================================================//

/**
 * Get Server Information (Name, Address and more...)
 *
 * @deprecated use \Splash\Framework\Dictionary\Methods\SplAdminMethods instead
 */
define("SPL_F_GET_INFOS", 'infos');

/**
 * Get List of Available Objects
 *
 * @deprecated use \Splash\Framework\Dictionary\Methods\SplAdminMethods instead
 */
define("SPL_F_GET_OBJECTS", 'objects');

/**
 * Get Result of SelfTest Sequence
 *
 * @deprecated use \Splash\Framework\Dictionary\Methods\SplAdminMethods instead
 */
define("SPL_F_GET_SELFTEST", 'selftest');

/**
 * Get List of Available Widgets
 *
 * @deprecated use \Splash\Framework\Dictionary\Methods\SplAdminMethods instead
 */
define("SPL_F_GET_WIDGETS", 'widgets');

//====================================================================//
//--------------------------------------------------------------------//
//==== Webservice : Objects                                       ====//
//--------------------------------------------------------------------//
//====================================================================//
//  Available Functions
//====================================================================//

/**
 * Get List of Available Objects
 *
 * @deprecated use \Splash\Framework\Dictionary\Methods\SplObjectMethods instead
 */
define("SPL_F_OBJECTS", 'Objects');

/**
 * Commit Object Change on Server
 *
 * @deprecated use \Splash\Framework\Dictionary\Methods\SplObjectMethods instead
 */
define("SPL_F_COMMIT", 'Commit');

/**
 * Read Object Description
 *
 * @deprecated use \Splash\Framework\Dictionary\Methods\SplObjectMethods instead
 */
define("SPL_F_DESC", 'Description');

/**
 * Read Available Fields for an  Object
 *
 * @deprecated use \Splash\Framework\Dictionary\Methods\SplObjectMethods instead
 */
define("SPL_F_FIELDS", 'Fields');

/**
 * Read Objects List
 *
 * @deprecated use \Splash\Framework\Dictionary\Methods\SplObjectMethods instead
 */

define("SPL_F_LIST", 'ObjectsList');

/**
 * Identify Object by Primary Keys
 *
 * @deprecated use \Splash\Framework\Dictionary\Methods\SplObjectMethods instead
 */
define("SPL_F_IDENTIFY", 'Identify');

/**
 * Read Object Data
 *
 * @deprecated use \Splash\Framework\Dictionary\Methods\SplObjectMethods instead
 */
define("SPL_F_GET", 'Get');

/**
 * Write Object Data
 *
 * @deprecated use \Splash\Framework\Dictionary\Methods\SplObjectMethods instead
 */
define("SPL_F_SET", 'Set');

/**
 * Delete An Object
 *
 * @deprecated use \Splash\Framework\Dictionary\Methods\SplObjectMethods instead
 */
define("SPL_F_DEL", 'Delete');

//====================================================================//
//--------------------------------------------------------------------//
//==== Webservice : File                                          ====//
//--------------------------------------------------------------------//
//====================================================================//
//  Available Functions
//====================================================================//

/**
 * Check if file exist
 *
 * @deprecated use \Splash\Framework\Dictionary\Methods\SplFilesMethods instead
 */
define("SPL_F_ISFILE", 'isFile');

/**
 * Download file from slave
 *
 * @deprecated use \Splash\Framework\Dictionary\Methods\SplFilesMethods instead
 */
define("SPL_F_GETFILE", 'ReadFile');

/**
 * Upload file to slave
 *
 * @deprecated use \Splash\Framework\Dictionary\Methods\SplFilesMethods instead
 */
define("SPL_F_SETFILE", 'SetFile');

/**
 * Delete file from slave
 *
 * @deprecated use \Splash\Framework\Dictionary\Methods\SplFilesMethods instead
 */
define("SPL_F_DELFILE", 'DeleteFile');

//====================================================================//
//--------------------------------------------------------------------//
//==== Webservice : Widgets                                       ====//
//--------------------------------------------------------------------//
//====================================================================//
//  Available Functions
//====================================================================//

/**
 * Get List of Available Widgets
 *
 * @deprecated use \Splash\Framework\Dictionary\Methods\SplWidgetsMethods instead
 */
define("SPL_F_WIDGET_LIST", 'WidgetsList');

/**
 * Get Widget Definition
 *
 * @deprecated use \Splash\Framework\Dictionary\Methods\SplWidgetsMethods instead
 */
define("SPL_F_WIDGET_DEFINITION", 'Description');

/**
 * Get Information
 *
 * @deprecated use \Splash\Framework\Dictionary\Methods\SplWidgetsMethods instead
 */
define("SPL_F_WIDGET_GET", 'Get');
