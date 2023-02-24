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

namespace   Splash\Components;

use ArrayObject;
use Exception;
use SimpleXMLElement;
use XMLWriter;

/**
 * Xml Encoding & Decoding Functions Collector Class
 */
class XmlManager
{
    /**
     * Fault String
     *
     * @var string
     */
    public string $fault;

    /**
     * XMLWriter Class
     *
     * @var XMLWriter
     *
     * @static
     */
    private static XMLWriter $xml;

    /**
     * Class Constructor
     */
    public function __construct()
    {
        //====================================================================//
        // Initialize XML Parser
        self::$xml = new XmlWriter();
        self::$xml->openMemory();
        self::$xml->startDocument('1.0');
        self::$xml->setIndent(true);
    }

    //====================================================================//
    //  XML Parser High Level User Functions
    //====================================================================//

    /**
     * Method to convert Object into XML string
     *
     * @param ArrayObject $obj
     *
     * @return string
     */
    public function objectToXml(ArrayObject $obj): string
    {
        //====================================================================//
        // Convert Object to XML Recursively
        $this->objectToXmlCore(self::$xml, array("SPLASH" => $obj));
        //====================================================================//
        // Put End Element on Xml
        self::$xml->endElement();
        //====================================================================//
        // Output Result
        return self::$xml->outputMemory(true);
    }

    /**
     * Method to convert Array into XML string
     *
     * @param array $input
     *
     * @return string
     */
    public function arrayToXml(array $input): string
    {
        //====================================================================//
        // Convert Object to XML Recursively
        $this->objectToXmlCore(self::$xml, array("SPLASH" => $input));
        //====================================================================//
        // Put End Element on Xml
        self::$xml->endElement();
        //====================================================================//
        // Output Result
        return self::$xml->outputMemory(true);
    }

    /**
     * Method to convert XML string into ArrayObject
     *
     * @param string $xml
     *
     * @return null|ArrayObject<string, ArrayObject|string>
     */
    public function xmlToArrayObject(string $xml): ?ArrayObject
    {
        //====================================================================//
        // SimpleXMLElement Object to Array
        $data = self::simpleXmlToArrayObject($this->xmlToElements($xml));

        return ($data instanceof ArrayObject) ? $data : null;
    }

    /**
     * Method to convert XML string into Array
     *
     * @param string $xml
     *
     * @return null|array<string, array|string>
     */
    public function xmlToArray(string $xml): ?array
    {
        //====================================================================//
        // SimpleXMLElement Object to Array
        $data = self::simpleXmlToArray($this->xmlToElements($xml));

        return is_array($data) ? $data : null;
    }

    //====================================================================//
    //  XML Parser Low Level Core Functions
    //====================================================================//

    /**
     * Method to convert XML string into SimpleXmlElement Object
     *
     * @param string $xml
     *
     * @return null|SimpleXMLElement
     */
    private function xmlToElements(string $xml): ?SimpleXMLElement
    {
        //====================================================================//
        // Convert XML to Object Recursively
        try {
            $result = simplexml_load_string($xml, "SimpleXMLElement", LIBXML_NOERROR);
        } catch (Exception $ex) {
            $this->fault = $ex->getMessage();

            return null;
        }

        return $result ?: null;
    }

    /**
     * Recursive Method to Object  XML string
     *
     * @param XMLWriter $xml
     * @param iterable  $object
     *
     * @return void
     */
    private function objectToXmlCore(XMLWriter $xml, iterable $object): void
    {
        //====================================================================//
        // Read each entities of an object
        foreach ($object as $key => $value) {
            //====================================================================//
            // Insert Array
            //====================================================================//
            if (is_array($value)) {
                //====================================================================//
                // Rename Numeric Keys
                $key = self::keysFilter($key, 'array-');
                //====================================================================//
                // Safety Check
                if (true != $xml->startElement($key)) {
                    $this->fault = "Xml Parser - Wrong StartElement Key "
                        .": ".print_r($key, true)." Value : ".print_r($value, true);
                }
                //====================================================================//
                // Recursive Add Of This Array
                $this->objectToXmlCore($xml, $value);
                $xml->endElement();

                continue;
            }
            //====================================================================//
            // Insert Iterable Object
            //====================================================================//
            if (is_iterable($value)) {
                //====================================================================//
                // Rename Numeric Keys
                $key = self::keysFilter($key, 'object-');
                //====================================================================//
                // Recursive Add Of This Object
                $xml->startElement($key);
                $this->objectToXmlCore($xml, $value);
                $xml->endElement();

                continue;
            }
            //====================================================================//
            // Insert String
            //====================================================================//
            if (is_string($value)) {
                //====================================================================//
                // Rename Numeric Keys
                $key = self::keysFilter($key, 'string-');

            //====================================================================//
            // Insert Numeric
            //====================================================================//
            } elseif (is_numeric($value)) {
                //====================================================================//
                // Rename Numeric Keys
                $key = self::keysFilter($key, 'int-');

            //====================================================================//
            // Insert Boolean
            //====================================================================//
            } elseif (is_bool($value)) {
                //====================================================================//
                // Rename Numeric Keys
                $key = self::keysFilter($key, 'bool-');
                //====================================================================//
                // Convert Value To String
                $value = $value?"1":"0";
            }
            //====================================================================//
            // Insert Data
            $xml->writeElement($key, base64_encode((string) $value));
        }
    }

    /**
     * Filter XML Key to remove single numeric keys by adding a prefix
     *
     * @param string $key
     * @param string $prefix
     *
     * @return string
     */
    private static function keysFilter(string $key, string $prefix): string
    {
        //====================================================================//
        // Convert Numeric Keys with Prefix
        $key = is_numeric($key) ? $prefix.$key : $key;
        //====================================================================//
        // Keep Scalar Keys
        return (string) preg_replace('/[^a-zA-Z0-9\-]/', '_', $key);
    }

    /**
     * Convert a SimpleXML object to an Array
     *
     * @param null|SimpleXMLElement $element
     *
     * @return null|array<string, array|string>|string
     */
    private static function simpleXmlToArray(?SimpleXMLElement $element)
    {
        //====================================================================//
        // Safety Check
        if (null === $element) {
            return null;
        }
        //====================================================================//
        // Init Result
        $result = array();
        //====================================================================//
        // Get First Level Children
        $children = $element->children();
        $isArrayElement = false;
        //====================================================================//
        // For All Children
        foreach ($children as $elementName => $node) {
            //====================================================================//
            // This Element is an ArrayElement
            $isArrayElement = true;
            //====================================================================//
            // If Element Doesn't Already Exists => Store as Single Element
            if (!isset($result[$elementName])) {
                $result[$elementName] = self::simpleXmlToArray($node);

                continue;
            }
            //====================================================================//
            // Element Already Exists => Store as Array Element
            //====================================================================//
            // Convert Single Element to Array Element
            if (!is_array($result[$elementName])) {
                $singleElement = $result[$elementName];         // Store First Element
                $result[$elementName] = array();                // Create New Array
                $result[$elementName][] = $singleElement;       // Append To Array
            }
            //====================================================================//
            // Append Array Element
            /** @phpstan-ignore-next-line */
            $result[$elementName][] = self::simpleXmlToArray($node);
        }
        //====================================================================//
        // Return Single Element
        if (!$isArrayElement && '' == $children->getName()) {
            $result = (string) base64_decode((string) $element, true);
        }

        /** @phpstan-ignore-next-line */
        return $result;
    }

    /**
     * Convert a SimpleXML object to an ArrayObject
     *
     * @param null|SimpleXMLElement $element
     *
     * @return null|ArrayObject<string, ArrayObject|string>|string
     */
    private static function simpleXmlToArrayObject(?SimpleXMLElement $element)
    {
        //====================================================================//
        // Safety Check
        if (null === $element) {
            return null;
        }
        //====================================================================//
        // Init Result
        $result = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        //====================================================================//
        // Get First Level Children
        $children = $element->children();
        $isArrayElement = false;
        //====================================================================//
        // For All Children
        foreach ($children as $elementName => $node) {
            //====================================================================//
            // This Element is an ArrayElement
            $isArrayElement = true;
            //====================================================================//
            // If Element Doesn't Already Exists => Store as Single Element
            if (!isset($result[$elementName])) {
                $result->{$elementName} = self::simpleXmlToArrayObject($node);

                continue;
            }
            //====================================================================//
            // Element Already Exists => Store as Array Element
            //====================================================================//
            // Convert Single Element to Array Element
            if (!is_array($result->{$elementName})) {
                $singleElement = $result[$elementName];       // Store First Element
                /** @phpstan-ignore-next-line */
                $result[$elementName] = array();              // Create New Array
                $result[$elementName][] = $singleElement;     // Append To Array
            }
            //====================================================================//
            // Append Array Element
            $result[$elementName][] = self::simpleXmlToArrayObject($node);
        }
        //====================================================================//
        // Return Single Element
        if (!$isArrayElement && '' == $children->getName()) {
            $result = (string) base64_decode((string) $element, true);
        }

        return $result;
    }
}
