<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2021 Splash Sync  <www.splashsync.com>
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
use Splash\Client\Splash;
use Splash\Components\FieldsManager;

/**
 * Splash Test Tools - Objects PhpUnit Specific Assertions
 *
 * @author SplashSync <contact@splashsync.com>
 */
trait ObjectsAssertionsTrait
{
    /** @var array */
    protected $fields = array();

    //==============================================================================
    //      SPLASH ASSERTIONS FUNCTIONS
    //==============================================================================

    /**
     * Verify if Data is present in Array and in right Internal Format
     *
     * @param mixed  $data    Tested Array
     * @param string $key     Tested Array Key
     * @param string $type    Expected Data Type
     * @param string $comment
     *
     * @return void
     */
    public function assertArrayInternalType($data, $key, $type, $comment)
    {
        $this->assertArrayHasKey($key, $data, $comment." => Key '".$key."' not defined");
        $this->assertNotEmpty($data[$key], $comment." => Key '".$key."' is Empty");
        switch ($type) {
            case "bool":
                $this->assertIsBool($data[$key], $comment." => Key '".$key."' is of Expected Internal Type");

                break;
            case "string":
                $this->assertIsString($data[$key], $comment." => Key '".$key."' is of Expected Internal Type");

                break;
            case "scalar":
                $this->assertIsScalar($data[$key], $comment." => Key '".$key."' is of Expected Internal Type");

                break;
            case "array":
                $this->assertIsArray($data[$key], $comment." => Key '".$key."' is of Expected Internal Type");

                break;
        }
    }

    /**
     * Verify if Data is present in Array and in right Internal Format
     *
     * @param mixed  $data    Tested Array
     * @param string $key     Tested Array Key
     * @param string $type    Expected Data Type
     * @param string $comment
     *
     * @return void
     */
    public function assertArrayInstanceOf($data, $key, $type, $comment)
    {
        $this->assertArrayHasKey($key, $data, $comment." => Key '".$key."' not defined");
        $this->assertNotEmpty($data[$key], $comment." => Key '".$key."' is Empty");
        $this->assertTrue(class_exists($type));
        $this->assertInstanceOf(
            $type,
            $data[$key],
            $comment." => Key '".$key."' is of Expected Instance of ".$type
        );
    }

    /**
     * Verify if Data is a valid Splash Data Block Bool Value
     *
     * @param mixed  $data
     * @param string $comment
     *
     * @return void
     */
    public function assertIsSplashBool($data, $comment)
    {
        $test = is_bool($data) || ("0" === $data) || ("1" === $data);
        $this->assertTrue($test, $comment);
    }

    /**
     * Verify if Data is present in Array and is Splash Bool
     *
     * @param mixed  $data    Tested Array
     * @param string $key     Tested Array Key
     * @param string $comment
     *
     * @return void
     */
    public function assertArraySplashBool($data, $key, $comment)
    {
        $this->assertArrayHasKey($key, $data, $comment." => Key '".$key."' not defined");
        $this->assertIsSplashBool(
            $data[$key],
            $comment." => Key '".$key."' is of Expected Bool Type (bool | '0' | '1')"
        );
    }

    /**
     * Verify if Data is a valid Splash Data Block Array Value
     *
     * @param mixed  $data
     * @param string $comment
     *
     * @return void
     */
    public function assertIsSplashArray($data, $comment)
    {
        $test = is_array($data) || ($data instanceof ArrayObject) || ("" === $data);
        $this->assertTrue($test, $comment);
    }

    /**
     * Verify if Data is present in Array and is Splash Bool
     *
     * @param mixed  $data    Tested Array
     * @param string $key     Tested Array Key
     * @param string $comment
     *
     * @return void
     */
    public function assertArraySplashArray($data, $key, $comment)
    {
        $this->assertArrayHasKey($key, $data, $comment." => Key '".$key."' not defined");
        $this->assertIsSplashArray(
            $data[$key],
            $comment." => Key '".$key."' is of Expected Array Type (array | null | '')"
        );
    }

    /**
     * Verify if Data is a valid Splash Field Data Value
     *
     * @param mixed  $data
     * @param string $type
     * @param string $comment
     *
     * @return void
     */
    public function assertIsValidSplashFieldData($data, $type, $comment)
    {
        //====================================================================//
        // Verify Type is Valid
        $className = self::isValidType($type);
        $this->assertNotEmpty($className, "Field Type '".$type."' is not a Valid Splash Field Type.".$comment);
        if (false === $className) {
            return;
        }

        //====================================================================//
        // Verify Data is Valid
        $this->assertTrue(
            $className::validate($data),
            "Data is not a Valid Splash '".$type."'. (".print_r($data, true).")".$comment
        );
    }

    /**
     * Verify Object Field is Defined
     *
     * @param string $itemType Field Microdata Type Url
     * @param string $itemProp Field Microdata Property Name
     * @param string $comment
     *
     * @return void
     */
    public function assertFieldIsDefined($itemType, $itemProp, $comment = null)
    {
        //====================================================================//
        //   Touch this Field
        $field = $this->loadObjectFieldByTag($itemType, $itemProp);
        //====================================================================//
        //   Verify this Field
        $this->assertNotEmpty(
            $field,
            self::buildResult($itemType, $itemProp, " must be defined", $comment)
        );
    }

    /**
     * Verify Object Field is in Allowed Formats
     *
     * @param string $itemType Field Microdata Type Url
     * @param string $itemProp Field Microdata Property Name
     * @param array  $formats  Array of Allowed Splash Field Formats
     * @param string $comment
     *
     * @return void
     */
    public function assertFieldHasFormat($itemType, $itemProp, $formats, $comment = null)
    {
        //====================================================================//
        //   Touch this Field
        $field = $this->loadObjectFieldByTag($itemType, $itemProp);
        if (!$field) {
            return;
        }
        //====================================================================//
        //   Verify this Field
        $this->assertTrue(
            in_array($field->type, $formats, true),
            self::buildResult(
                $itemType,
                $itemProp,
                " must be a ".implode("|", $formats)." Current is ".$field->type,
                $comment
            )
        );
    }

    /**
     * Verify Object Field is Readable
     *
     * @param string $itemType Field Microdata Type Url
     * @param string $itemProp Field Microdata Property Name
     * @param string $comment
     *
     * @return void
     */
    public function assertFieldIsRead($itemType, $itemProp, $comment = null)
    {
        //====================================================================//
        //   Touch this Field
        $field = $this->loadObjectFieldByTag($itemType, $itemProp);
        if (!$field) {
            return;
        }
        //====================================================================//
        //   Verify this Field
        $this->assertTrue(
            (bool) $field->read,
            self::buildResult($itemType, $itemProp, " must be readable.", $comment)
        );
    }

    /**
     * Verify Object Field is Writeable
     *
     * @param string $itemType Field Microdata Type Url
     * @param string $itemProp Field Microdata Property Name
     * @param string $comment
     *
     * @return void
     */
    public function assertFieldIsWrite($itemType, $itemProp, $comment = null)
    {
        //====================================================================//
        //   Touch this Field
        $field = $this->loadObjectFieldByTag($itemType, $itemProp);
        if (!$field) {
            return;
        }
        //====================================================================//
        //   Verify this Field
        $this->assertTrue(
            (bool) $field->write,
            self::buildResult($itemType, $itemProp, " must be writeable.", $comment)
        );
    }

    /**
     * Verify Object Field is NOT Writeable
     *
     * @param string $itemType Field Microdata Type Url
     * @param string $itemProp Field Microdata Property Name
     * @param string $comment
     *
     * @return void
     */
    public function assertFieldNotWrite($itemType, $itemProp, $comment = null)
    {
        //====================================================================//
        //   Touch this Field
        $field = $this->loadObjectFieldByTag($itemType, $itemProp);
        if (!$field) {
            return;
        }
        //====================================================================//
        //   Verify this Field
        $this->assertFalse(
            (bool) $field->write,
            self::buildResult($itemType, $itemProp, " must be read-only.", $comment)
        );
    }

    /**
     * Verify Object Field is Required
     *
     * @param string $itemType Field Microdata Type Url
     * @param string $itemProp Field Microdata Property Name
     * @param string $comment
     *
     * @return void
     */
    public function assertFieldIsRequired($itemType, $itemProp, $comment = null)
    {
        //====================================================================//
        //   Touch this Field
        $field = $this->loadObjectFieldByTag($itemType, $itemProp);
        if (!$field) {
            return;
        }
        //====================================================================//
        //   Verify this Field
        $this->assertTrue(
            (bool) $field->required,
            self::buildResult($itemType, $itemProp, " must be required.", $comment)
        );
    }

    /**
     * Verify Object Field is NOT Required
     *
     * @param string $itemType Field Microdata Type Url
     * @param string $itemProp Field Microdata Property Name
     * @param string $comment
     *
     * @return void
     */
    public function assertFieldNotRequired($itemType, $itemProp, $comment = null)
    {
        //====================================================================//
        //   Touch this Field
        $field = $this->loadObjectFieldByTag($itemType, $itemProp);
        if (!$field) {
            return;
        }
        //====================================================================//
        //   Verify this Field
        $this->assertFalse(
            (bool) $field->required,
            self::buildResult($itemType, $itemProp, " must not be Required.", $comment)
        );
    }

    /**
     * Verify Object Field is In List
     *
     * @param string $itemType Field Microdata Type Url
     * @param string $itemProp Field Microdata Property Name
     * @param string $comment
     *
     * @return void
     */
    public function assertFieldIsInList($itemType, $itemProp, $comment = null)
    {
        //====================================================================//
        //   Touch this Field
        $field = $this->loadObjectFieldByTag($itemType, $itemProp);
        if (!$field) {
            return;
        }
        //====================================================================//
        //   Verify this Field
        $this->assertTrue(
            (bool) $field->inlist,
            $comment." ".$itemType.":".$itemProp." must be readable."
        );
    }

    /**
     * Verify Last Commit is Valid and Conform to Expected
     *
     * @param string $action     Expected Action
     * @param string $objectType Expected Object Type
     * @param string $objectId   Expected Object Id
     *
     * @return void
     */
    public function assertIsLastCommited($action, $objectType, $objectId)
    {
        $this->assertIsCommited($action, $objectType, $objectId, false);
    }

    /**
     * Verify First Commit is Valid and Conform to Expected
     *
     * @param string $action     Expected Action
     * @param string $objectType Expected Object Type
     * @param string $objectId   Expected Object Id
     *
     * @return void
     */
    public function assertIsFirstCommited($action, $objectType, $objectId)
    {
        $this->assertIsCommited($action, $objectType, $objectId, true);
    }

    /**
     * Verify First Commit is Valid and Conform to Expected
     *
     * @param string $action     Expected Action
     * @param string $objectType Expected Object Type
     * @param string $objectId   Expected Object Id
     * @param bool   $first      Check First or Last Commited
     *
     * @return void
     */
    private function assertIsCommited($action, $objectType, $objectId, $first = true)
    {
        //====================================================================//
        //   Verify Object Change Was Commited
        $this->assertNotEmpty(
            Splash::$commited,
            "No Object Change Commited by your Module. Please check your triggers."
        );

        //====================================================================//
        //   Get First / Last Commited
        $commited = $first ? array_shift(Splash::$commited) : array_pop(Splash::$commited);

        //====================================================================//
        //   Check Object Type is OK
        $this->assertEquals(
            $commited->type,
            $objectType,
            "Change Commit => Object Type is wrong. "
                ."(Expected ".$objectType." / Given ".$commited->type
        );

        //====================================================================//
        //   Check Object Action is OK
        $this->assertEquals(
            $commited->action,
            $action,
            "Change Commit => Change Type is wrong. (Expected ".$action." / Given ".$commited->action
        );

        //====================================================================//
        //   Check Object Id value Format
        $this->assertTrue(
            is_scalar($commited->id) || is_array($commited->id) || is_a($commited->id, "ArrayObject"),
            "Change Commit => Object Id Value is in wrong Format. "
                ."(Expected String or Array of Strings. / Given "
                .print_r($commited->id, true)
        );

        //====================================================================//
        //   If Commited an Array of Ids
        if (is_array($commited->id) || ($commited->id instanceof ArrayObject)) {
            //====================================================================//
            //   Detect Array Object
            if ($commited->id instanceof ArrayObject) {
                $commited->id = $commited->id->getArrayCopy();
            }
            //====================================================================//
            //   Check each Object Ids
            foreach ($commited->id as $committedObjectId) {
                $this->assertIsScalar(
                    $committedObjectId,
                    "Change Commit => Object Id Array Value is in wrong Format. "
                        ."(Expected String or Integer. / Given "
                        .print_r($committedObjectId, true)
                );
            }
            //====================================================================//
            //   Extract First Object Id
            $firstId = array_shift($commited->id);
            //====================================================================//
            //   Verify First Object Id is OK
            $this->assertEquals(
                $firstId,
                $objectId,
                "Change Commit => Object Id is wrong. (Expected ".$objectId." / Given ".$firstId
            );
        } else {
            //====================================================================//
            //   Check Object Id is OK
            $this->assertEquals(
                $commited->id,
                $objectId,
                "Change Commit => Object Id is wrong. (Expected ".$objectId." / Given ".$commited->id
            );
        }

        //====================================================================//
        //   Check Infos are Not Empty
        $this->assertNotEmpty($commited->user, "Change Commit => User Name is Empty");
        $this->assertNotEmpty($commited->comment, "Change Commit => Action Comment is Empty");
    }

    /**
     * Load Object Field from List
     *
     * @param string $itemType Field Microdata Type Url
     * @param string $itemProp Field Microdata Property Name
     *
     * @return null|ArrayObject
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
     * Build Test Result Comment
     *
     * @param string $itemType    Field Microdata Type Url
     * @param string $itemProp    Field Microdata Property Name
     * @param string $testComment
     * @param string $comment
     *
     * @return string
     */
    private static function buildResult($itemType, $itemProp, $testComment, $comment = null)
    {
        return $comment." (".$itemType.":".$itemProp.") ".$testComment;
    }
}
