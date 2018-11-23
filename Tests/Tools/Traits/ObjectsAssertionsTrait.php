<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2018 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Tests\Tools\Traits;

use ArrayObject;
use Splash\Components\FieldsManager;

/**
 * @abstract    Splash Test Tools - Objects PhpUnit Specific Assertions
 *
 * @author SplashSync <contact@splashsync.com>
 */
trait ObjectsAssertionsTrait
{
    /** @var array */
    protected $fields           = array();
    
    //==============================================================================
    //      SPLASH ASSERTIONS FUNCTIONS
    //==============================================================================
    
    /**
     * @abstract        Verify if Data is present in Array and in right Internal Format
     *
     * @param mixed     $data           Tested Array
     * @param mixed     $key            Tested Array Key
     * @param mixed     $type           Expected Data Type
     * @param string    $comment
     */
    public function assertArrayInternalType($data, $key, $type, $comment)
    {
        $this->assertArrayHasKey($key, $data, $comment . " => Key '" . $key . "' not defined");
        $this->assertNotEmpty($data[$key], $comment . " => Key '" . $key . "' is Empty");
        $this->assertInternalType($type, $data[$key], $comment . " => Key '" . $key . "' is of Expected Internal Type");
    }
    
    /**
     * @abstract        Verify if Data is present in Array and in right Internal Format
     *
     * @param mixed     $data           Tested Array
     * @param mixed     $key            Tested Array Key
     * @param mixed     $type           Expected Data Type
     * @param string    $comment
     */
    public function assertArrayInstanceOf($data, $key, $type, $comment)
    {
        $this->assertArrayHasKey($key, $data, $comment . " => Key '" . $key . "' not defined");
        $this->assertNotEmpty($data[$key], $comment . " => Key '" . $key . "' is Empty");
        $this->assertInstanceOf($type, $data[$key], $comment . " => Key '" . $key . "' is of Expected Internal Type");
    }
    
    /**
     * @abstract        Verify if Data is a valid Splash Data Block Bool Value
     *
     * @param mixed     $data
     * @param string    $comment
     */
    public function assertIsSplashBool($data, $comment)
    {
        $test = is_bool($data) || ("0" === $data) || ("1" === $data);
        $this->assertTrue($test, $comment);
    }
    
    /**
     * @abstract        Verify if Data is present in Array and is Splash Bool
     *
     * @param mixed     $data           Tested Array
     * @param mixed     $key            Tested Array Key
     * @param string    $comment
     */
    public function assertArraySplashBool($data, $key, $comment)
    {
        $this->assertArrayHasKey($key, $data, $comment . " => Key '" . $key . "' not defined");
        $this->assertIsSplashBool($data[$key], $comment . " => Key '" . $key . "' is of Expected Internal Type");
    }

    /**
     * @abstract        Verify if Data is a valid Splash Field Data Value
     *
     * @param mixed     $data
     * @param string    $type
     * @param string    $comment
     */
    public function assertIsValidSplashFieldData($data, $type, $comment)
    {
        //====================================================================//
        // Verify Type is Valid
        $className = self::isValidType($type);
        $this->assertNotEmpty($className, "Field Type '" . $type . "' is not a Valid Splash Field Type." . $comment);
        if (false === $className) {
            return false;
        }
    
        //====================================================================//
        // Verify Data is Valid
        $this->assertTrue(
            $className::validate($data),
            "Data is not a Valid Splash '" . $type . "'. (" . print_r($data, true) . ")" . $comment
        );
    }
    
    /**
     * @abstract    Verify Object Field is Defined
     * @param       string      $itemType           Field Microdata Type Url
     * @param       string      $itemProp           Field Microdata Property Name
     * @param       string      $comment
     * @return void
     */
    public function assertFieldIsDefined($itemType, $itemProp, $comment = null)
    {
        //====================================================================//
        //   Touch this Field
        $field       =   $this->loadObjectFieldByTag($itemType, $itemProp);
        //====================================================================//
        //   Verify this Field
        $this->assertNotEmpty(
            $field,
            self::buildResult($itemType, $itemProp, " must be defined", $comment)
        );
    }
    
    /**
     * @abstract    Verify Object Field is in Allowed Formats
     * @param       string      $itemType           Field Microdata Type Url
     * @param       string      $itemProp           Field Microdata Property Name
     * @param       array       $formats            Array of Allowed Splash Field Formats
     * @param       string      $comment
     * @return void
     */
    public function assertFieldHasFormat($itemType, $itemProp, $formats, $comment = null)
    {
        //====================================================================//
        //   Touch this Field
        $field       =   $this->loadObjectFieldByTag($itemType, $itemProp);
        if (!$field) {
            return;
        }
        //====================================================================//
        //   Verify this Field
        $this->assertTrue(
            in_array($field->type, $formats, true),
            self::buildResult($itemType, $itemProp, " must be a " . implode("|", $formats), $comment)
        );
        $this->assertTrue(
            $field->read,
            "Product Short Description must be readable"
        );
    }
    
    /**
     * @abstract    Verify Object Field is Readable
     * @param       string      $itemType           Field Microdata Type Url
     * @param       string      $itemProp           Field Microdata Property Name
     * @param       string      $comment
     * @return void
     */
    public function assertFieldIsRead($itemType, $itemProp, $comment = null)
    {
        //====================================================================//
        //   Touch this Field
        $field       =   $this->loadObjectFieldByTag($itemType, $itemProp);
        if (!$field) {
            return;
        }
        //====================================================================//
        //   Verify this Field
        $this->assertTrue(
            $field->read,
            self::buildResult($itemType, $itemProp, " must be readable.", $comment)
        );
    }

    /**
     * @abstract    Verify Object Field is Writeable
     * @param       string      $itemType           Field Microdata Type Url
     * @param       string      $itemProp           Field Microdata Property Name
     * @param       string      $comment
     * @return void
     */
    public function assertFieldIsWrite($itemType, $itemProp, $comment = null)
    {
        //====================================================================//
        //   Touch this Field
        $field       =   $this->loadObjectFieldByTag($itemType, $itemProp);
        if (!$field) {
            return;
        }
        //====================================================================//
        //   Verify this Field
        $this->assertTrue(
            $field->write,
            self::buildResult($itemType, $itemProp, " must be writeable.", $comment)
        );
    }
    
    /**
     * @abstract    Verify Object Field is NOT Writeable
     * @param       string      $itemType           Field Microdata Type Url
     * @param       string      $itemProp           Field Microdata Property Name
     * @param       string      $comment
     * @return void
     */
    public function assertFieldNotWrite($itemType, $itemProp, $comment = null)
    {
        //====================================================================//
        //   Touch this Field
        $field       =   $this->loadObjectFieldByTag($itemType, $itemProp);
        if (!$field) {
            return;
        }
        //====================================================================//
        //   Verify this Field
        $this->assertFalse(
            $field->write,
            self::buildResult($itemType, $itemProp, " must be read-only.", $comment)
        );
    }
    
    /**
     * @abstract    Verify Object Field is Required
     * @param       string      $itemType           Field Microdata Type Url
     * @param       string      $itemProp           Field Microdata Property Name
     * @param       string      $comment
     * @return void
     */
    public function assertFieldIsRequired($itemType, $itemProp, $comment = null)
    {
        //====================================================================//
        //   Touch this Field
        $field       =   $this->loadObjectFieldByTag($itemType, $itemProp);
        if (!$field) {
            return;
        }
        //====================================================================//
        //   Verify this Field
        $this->assertTrue(
            $field->required,
            self::buildResult($itemType, $itemProp, " must be required.", $comment)
        );
    }
    
    /**
     * @abstract    Verify Object Field is NOT Required
     * @param       string      $itemType           Field Microdata Type Url
     * @param       string      $itemProp           Field Microdata Property Name
     * @param       string      $comment
     * @return void
     */
    public function assertFieldNotRequired($itemType, $itemProp, $comment = null)
    {
        //====================================================================//
        //   Touch this Field
        $field       =   $this->loadObjectFieldByTag($itemType, $itemProp);
        if (!$field) {
            return;
        }
        //====================================================================//
        //   Verify this Field
        $this->assertFalse(
            $field->required,
            self::buildResult($itemType, $itemProp, " must not be Required.", $comment)
        );
    }

    /**
     * @abstract    Verify Object Field is In List
     * @param       string      $itemType           Field Microdata Type Url
     * @param       string      $itemProp           Field Microdata Property Name
     * @param       string      $comment
     * @return void
     */
    public function assertFieldIsInList($itemType, $itemProp, $comment = null)
    {
        //====================================================================//
        //   Touch this Field
        $field       =   $this->loadObjectFieldByTag($itemType, $itemProp);
        if (!$field) {
            return;
        }
        //====================================================================//
        //   Verify this Field
        $this->assertTrue(
            $field->inlist,
            $comment . " " . $itemType . ":" . $itemProp . " must be readable."
        );
    }
    
    /**
     * @abstract    Load Object Field from List
     * @param       string      $itemType           Field Microdata Type Url
     * @param       string      $itemProp           Field Microdata Property Name
     * @return     null|ArrayObject
     */
    private function loadObjectFieldByTag($itemType, $itemProp)
    {
        //====================================================================//
        //   Ensure Fields List is Loaded
        $this->assertNotEmpty($this->fields, "Objects Fields List is Empty! Did you load it?");
        //====================================================================//
        //   Touch this Field
        return FieldsManager::findFieldByTag($this->fields, $itemType, $itemProp);
    }
    
    /**
     * @abstract    Build Test Result Comment
     * @param       string      $itemType           Field Microdata Type Url
     * @param       string      $itemProp           Field Microdata Property Name
     * @param       string      $testComment
     * @param       string      $comment
     * @return      string
     */
    private static function buildResult($itemType, $itemProp, $testComment, $comment = null)
    {
        return $comment . " (" . $itemType . ":" . $itemProp . ") " . $testComment;
    }
}
