<?php
/*
 * This file is part of SplashSync Project.
 *
 * Copyright (C) Splash Sync <www.splashsync.com>
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @abstract    Xml Encoding & Decoding Functions Collector Class
 * @author      B. Paquier <contact@splashsync.com>
 */

namespace   Splash\Components;

use XmlWriter;
use stdClass;
use ArrayObject;

//====================================================================//
//  CLASS DEFINITION
//====================================================================//

class XmlManager
{

    /**
     *      @abstract   XMLWritter Class
     *      @var        XMLWritter
     *      @static
     */
    private static $xml;
    
    /*
     *  Fault String
     */
    public $fault_str;

    /**
     *      @abstract      Class Constructor
     *      @return     int             <0 if KO, >0 if OK
     */
    public function __construct()
    {
        //====================================================================//
        // Initialize XML Parser
        self::$xml = new XmlWriter();
        self::$xml->openMemory();
        self::$xml->startDocument('1.0');
        self::$xml->setIndent(true);
        
        return true;
    }

    //====================================================================//
    //  XML Parser High Level User Functions
    //====================================================================//

    /**
    * @abstract     Method to convert Object into XML string
    * @param        array      $obj
    * @return       array      $result
    */
    public function objectToXml($obj)
    {
        //====================================================================//
        // Create Transmition Object
        $tmp = new stdClass();
        $tmp->SPLASH    =   $obj;
        //====================================================================//
        // Convert Object to XML Recursively
        $this->objectToXmlCore(self::$xml, $tmp);
        //====================================================================//
        // Put End Element on Xml
        self::$xml->endElement();
        //====================================================================//
        // Output Result
        return self::$xml->outputMemory(true);
    }

    /**
     * @abstract     Method to convert XML string into Array
     * @param        xml        $Xml
     * @return       array      $result
     */
    protected function xmlToArray($Xml)
    {
        //====================================================================//
        // SimpleXMLElement Object to Array
        return self::simpleXmlToArray($this->xmlToElements($Xml));
    }
    
    /**
     * @abstract     Method to convert XML string into ArrayObject
     * @param        xml        $Xml
     * @return       array      $result
     */
    public function xmlToArrayObject($Xml)
    {
        //====================================================================//
        // SimpleXMLElement Object to Array
        return  self::simpleXmlToArrayObject($this->xmlToElements($Xml));
    }

    //====================================================================//
    //  XML Parser Low Level Core Functions
    //====================================================================//
    
    /**
     * @abstract     Method to convert XML string into SimpleXmlElement Object
     * @param        xml                    $Xml
     * @return       SimpleXMLElement       $result
     */
    private function xmlToElements($Xml)
    {
        //====================================================================//
        // Convert XML to Object Recursively
        try {
            $result = simplexml_load_string($Xml, "SimpleXMLElement", LIBXML_NOERROR);
        } catch (Exception $ex) {
            $this->fault_str = $ex->getMessage();
            return null;
        }
        return $result;
    }
    
    /**
    * @abstract     Recursive Method to Object  XML string
    * @param        XMLWriter  $xml
    * @param        mixed      $Object
    * @return       array      $result
    */
    private function objectToXmlCore(XMLWriter $xml, $Object)
    {
        //====================================================================//
        // Read each entitie of an object
        foreach ($Object as $key => $value) {
            //====================================================================//
            // Insert Object
            //====================================================================//
            if (is_object($value)) {
                //====================================================================//
                // Rename Numeric Keys
                $key = self::keysFilter($key, 'object-');
                //====================================================================//
                // Recurcive Add Of This Object
                $xml->startElement($key);
                $this->objectToXmlCore($xml, $value);
                $xml->endElement();
                continue;
            } //====================================================================//
            // Insert Array
            elseif (is_array($value)) {
                //====================================================================//
                // Rename Numeric Keys
                $key = self::keysFilter($key, 'array-');
                //====================================================================//
                // Safety Check
                if ($xml->startElement($key) != true) {
                    $this->logger->error(
                        "Xml Parser - Wrong StartElement Key : " . print_r($key, 1) . " Value : " . print_r($value, 1)
                    );
                }
                //====================================================================//
                // Recurcive Add Of This Array
                $this->objectToXmlCore($xml, $value);
                $xml->endElement();
                continue;
            } //====================================================================//
            // Insert String
            //====================================================================//
            elseif (is_string($value)) {
                //====================================================================//
                // Rename Numeric Keys
                $key = self::keysFilter($key, 'string-');
            } //====================================================================//
            // Insert Numeric
            elseif (is_numeric($value)) {
                //====================================================================//
                // Rename Numeric Keys
                $key = self::keysFilter($key, 'int-');
            } //====================================================================//
            // Insert Boolean
            elseif (is_bool($value)) {
                //====================================================================//
                // Rename Numeric Keys
                $key = self::keysFilter($key, 'bool-');
                //====================================================================//
                // Convert Value To String
                $value = $value?"1":"0";
            }
            //====================================================================//
            // Insert Data
            $xml->writeElement($key, base64_encode($value));
        }
    }

    /**
     * @abstract     Filter XML Key to remove single numeric keys by adding a prefix
     * @param        string     $key
     * @param        string     $prefix
     * @return       string     $result
     */
    private static function keysFilter($key, $prefix)
    {
        //====================================================================//
        // Convert Numeric Keys with Prefix
        if (is_numeric($key)) {
            return $prefix . $key;
        //====================================================================//
        // Keap Scalar Keys
        } else {
            return $key;
        }
    }
    
    /**
     * @abstract    Convert a SimpleXML object to an Array
     * @param       SimpleXMLElement    $Element
     * @return      array               $array
     */
    private static function simpleXmlToArray($Element)
    {
        //====================================================================//
        // Init Result
        $Result = array();
        //====================================================================//
        // Get First Level Childrens
        $children = $Element->children();
        $isArrayElement = false;
        //====================================================================//
        // For All Childrens
        foreach ($children as $elementName => $node) {
            //====================================================================//
            // This Element is an ArrayElement
            $isArrayElement = true;
            //====================================================================//
            // If Element Doesn't Already Exists => Store as Single Element
            if (!isset($Result[$elementName])) {
                $Result[$elementName]    = array();
                $Result[$elementName]    = self::simpleXmlToArray($node);
                continue;
            }
            //====================================================================//
            // Element Already Exists => Store as Array Element
            //====================================================================//
            // Convert Single Element to Array Element
            if (!is_array($Result[$elementName])) {
                $SingleElement          =   $Result[$elementName];       // Store Firts Element
                $Result[$elementName]    =   array();                    // Create New Array
                $Result[$elementName][]  =   $SingleElement;             // Append To Array
            }
            //====================================================================//
            // Append Array Element
            $Result[$elementName][] = self::simpleXmlToArray($node);
        }
        //====================================================================//
        // Return Single Element
        if (!$isArrayElement && $children->getName() == '') {
            $Result =  base64_decode((string) $Element);
        }
        return $Result;
    }
    
    /**
     * @abstract    Convert a SimpleXML object to an ArrayObject
     * @param       SimpleXMLElement    $Element
     * @return      array               $array
     */
    private static function simpleXmlToArrayObject($Element)
    {
        //====================================================================//
        // Init Result
        $Result = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        //====================================================================//
        // Get First Level Childrens
        $children = $Element->children();
        $isArrayElement = false;
        //====================================================================//
        // For All Childrens
        foreach ($children as $elementName => $node) {
            //====================================================================//
            // This Element is an ArrayElement
            $isArrayElement = true;
            //====================================================================//
            // If Element Doesn't Already Exists => Store as Single Element
            if (!isset($Result[$elementName])) {
                $Result[$elementName]    = array();
                $Result[$elementName]    = self::simpleXmlToArrayObject($node);
                continue;
            }
            //====================================================================//
            // Element Already Exists => Store as Array Element
            //====================================================================//
            // Convert Single Element to Array Element
            if (!is_array($Result[$elementName])) {
                $SingleElement           =   $Result[$elementName];       // Store First Element
                $Result[$elementName]    =   array();                    // Create New Array
                $Result[$elementName][]  =   $SingleElement;             // Append To Array
            }
            //====================================================================//
            // Append Array Element
            $Result[$elementName][] = self::simpleXmlToArrayObject($node);
        }
        //====================================================================//
        // Return Single Element
        if (!$isArrayElement && $children->getName() == '') {
            $Result =  base64_decode((string) $Element);
        }
        return $Result;
    }
}
