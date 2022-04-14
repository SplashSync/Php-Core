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

namespace Splash\Tests\Tools\Traits;

use Splash\Components\CommitsManager;
use Splash\Components\FieldsManager;
use Splash\Tests\Tools\Fields\FieldInterface;

/**
 * Splash Test Tools - Objects PhpUnit Specific Assertions
 */
trait ObjectsAssertionsTrait
{
    /**
     * @var array<int|string, array>
     */
    protected array $fields = array();

    //==============================================================================
    //      SPLASH ASSERTIONS FUNCTIONS
    //==============================================================================

    /**
     * Verify if Data is present in Array and in right Internal Format
     *
     * @param array<string, array|scalar> $data    Tested Array
     * @param string                      $key     Tested Array Key
     * @param string                      $type    Expected Data Type
     * @param string                      $comment
     *
     * @return void
     */
    public function assertArrayInternalType(array $data, string $key, string $type, string $comment): void
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
     * @param array<string, array|scalar> $data    Tested Array
     * @param string                      $key     Tested Array Key
     * @param string                      $type    Expected Data Type
     * @param string                      $comment
     *
     * @return void
     */
    public function assertArrayInstanceOf(array $data, string $key, string $type, string $comment): void
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
     * @param array|scalar $data
     * @param string       $comment
     *
     * @return void
     */
    public function assertIsSplashBool($data, string $comment): void
    {
        $test = is_bool($data) || ("0" === $data) || ("1" === $data);
        $this->assertTrue($test, $comment);
    }

    /**
     * Verify if Data is present in Array and is Splash Bool
     *
     * @param array<string, array|scalar> $data    Tested Array
     * @param string                      $key     Tested Array Key
     * @param string                      $comment
     *
     * @return void
     */
    public function assertArraySplashBool(array $data, string $key, string $comment): void
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
     * @param array|scalar $data
     * @param string       $comment
     *
     * @return void
     */
    public function assertIsSplashArray($data, string $comment): void
    {
        $test = is_array($data) || ("" === $data);
        $this->assertTrue($test, $comment);
    }

    /**
     * Verify if Data is present in Array and is Splash Bool
     *
     * @param array<string, array|scalar> $data    Tested Array
     * @param string                      $key     Tested Array Key
     * @param string                      $comment
     *
     * @return void
     */
    public function assertArraySplashArray(array $data, string $key, string $comment): void
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
    public function assertIsValidSplashFieldData($data, string $type, string $comment): void
    {
        //====================================================================//
        // Verify Type is Valid
        $className = self::isValidType($type);
        $this->assertNotEmpty($className, "Field Type '".$type."' is not a Valid Splash Field Type.".$comment);
        if (!$className) {
            return;
        }
        //====================================================================//
        // Verify Data is Valid
        /** @var FieldInterface $className */
        //====================================================================//
        // Validate Data Types
        $this->assertTrue(
            is_array($data) || is_scalar($data),
            "Data is not a Scalar or Array (".print_r($data, true).")".$comment
        );
        $this->assertNull(
            $className::validate($data),
            "Data is not a Valid Splash '".$type."'. (".print_r($data, true).")".$comment
        );
    }

    /**
     * Verify Object Field is Defined
     *
     * @param string      $itemType Field Microdata Type Url
     * @param string      $itemProp Field Microdata Property Name
     * @param null|string $comment
     *
     * @return void
     */
    public function assertFieldIsDefined(string $itemType, string $itemProp, string $comment = null): void
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
     * @param string      $itemType Field Microdata Type Url
     * @param string      $itemProp Field Microdata Property Name
     * @param array       $formats  Array of Allowed Splash Field Formats
     * @param null|string $comment
     *
     * @return void
     */
    public function assertFieldHasFormat(
        string $itemType,
        string $itemProp,
        array $formats,
        string $comment = null
    ): void {
        //====================================================================//
        //   Touch this Field
        $field = $this->loadObjectFieldByTag($itemType, $itemProp);
        if (!$field) {
            return;
        }
        //====================================================================//
        //   Verify this Field
        $this->assertTrue(
            in_array($field['type'], $formats, true),
            self::buildResult(
                $itemType,
                $itemProp,
                " must be a ".implode("|", $formats)." Current is ".$field['type'],
                $comment
            )
        );
    }

    /**
     * Verify Object Field is Readable
     *
     * @param string      $itemType Field Microdata Type Url
     * @param string      $itemProp Field Microdata Property Name
     * @param null|string $comment
     *
     * @return void
     */
    public function assertFieldIsRead(string $itemType, string $itemProp, string $comment = null): void
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
            (bool) $field['read'],
            self::buildResult($itemType, $itemProp, " must be readable.", $comment)
        );
    }

    /**
     * Verify Object Field is Writeable
     *
     * @param string      $itemType Field Microdata Type Url
     * @param string      $itemProp Field Microdata Property Name
     * @param null|string $comment
     *
     * @return void
     */
    public function assertFieldIsWrite(string $itemType, string $itemProp, string $comment = null): void
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
            (bool) $field['write'],
            self::buildResult($itemType, $itemProp, " must be writeable.", $comment)
        );
    }

    /**
     * Verify Object Field is NOT Writeable
     *
     * @param string      $itemType Field Microdata Type Url
     * @param string      $itemProp Field Microdata Property Name
     * @param null|string $comment
     *
     * @return void
     */
    public function assertFieldNotWrite(string $itemType, string $itemProp, string $comment = null): void
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
            (bool) $field['write'],
            self::buildResult($itemType, $itemProp, " must be read-only.", $comment)
        );
    }

    /**
     * Verify Object Field is Required
     *
     * @param string      $itemType Field Microdata Type Url
     * @param string      $itemProp Field Microdata Property Name
     * @param null|string $comment
     *
     * @return void
     */
    public function assertFieldIsRequired(string $itemType, string $itemProp, string $comment = null): void
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
            (bool) $field['required'],
            self::buildResult($itemType, $itemProp, " must be required.", $comment)
        );
    }

    /**
     * Verify Object Field is NOT Required
     *
     * @param string      $itemType Field Microdata Type Url
     * @param string      $itemProp Field Microdata Property Name
     * @param null|string $comment
     *
     * @return void
     */
    public function assertFieldNotRequired(string $itemType, string $itemProp, string $comment = null): void
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
            (bool) $field['required'],
            self::buildResult($itemType, $itemProp, " must not be Required.", $comment)
        );
    }

    /**
     * Verify Object Field is In List
     *
     * @param string      $itemType Field Microdata Type Url
     * @param string      $itemProp Field Microdata Property Name
     * @param null|string $comment
     *
     * @return void
     */
    public function assertFieldIsInList(string $itemType, string $itemProp, string $comment = null): void
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
            (bool) $field['inList'],
            $comment." ".$itemType.":".$itemProp." must be readable."
        );
    }

    /**
     * Verify Last Commit is Valid and Conform to Expected
     *
     * @param string $action     Expected Action
     * @param string $objectType Expected Object Type
     * @param string $objectId   Expected Object ID
     *
     * @return void
     */
    public function assertIsLastCommitted(string $action, string $objectType, string $objectId): void
    {
        $this->assertIsCommitted($action, $objectType, $objectId, false);
    }

    /**
     * Verify First Commit is Valid and Conform to Expected
     *
     * @param string $action     Expected Action
     * @param string $objectType Expected Object Type
     * @param string $objectId   Expected Object ID
     *
     * @return void
     */
    public function assertIsFirstCommitted(string $action, string $objectType, string $objectId): void
    {
        $this->assertIsCommitted($action, $objectType, $objectId);
    }

    /**
     * Verify First Commit is Valid and Conform to Expected
     *
     * @param string $action     Expected Action
     * @param string $objectType Expected Object Type
     * @param string $objectId   Expected Object ID
     * @param bool   $first      Check First or Last Committed
     *
     * @return void
     */
    private function assertIsCommitted(string $action, string $objectType, string $objectId, bool $first = true): void
    {
        $sessionCommits = CommitsManager::getSessionCommitted();
        //====================================================================//
        //   Verify Object Change Was Committed
        $this->assertNotEmpty(
            $sessionCommits,
            "No Object Change Committed by your Module. Please check your triggers."
        );
        //====================================================================//
        //   Get First / Last Committed
        $committed = $first
            ? array_shift($sessionCommits)
            : array_pop($sessionCommits)
        ;
        //====================================================================//
        // Check Committed Infos
        $this->assertIsArray($committed, CommitsManager::class."::committed format is wrong");
        $this->assertArrayHasKey("type", $committed, CommitsManager::class."::committed");
        $this->assertIsString($committed['type'], CommitsManager::class."::committed");
        $this->assertArrayHasKey("action", $committed, CommitsManager::class."::committed");
        $this->assertIsString($committed['action'], CommitsManager::class."::committed");
        $this->assertArrayHasKey("id", $committed, CommitsManager::class."::committed");
        $this->assertIsArray($committed['id'], CommitsManager::class."::committed");
        $this->assertArrayHasKey("user", $committed);
        $this->assertArrayHasKey("comment", $committed);

        //====================================================================//
        //   Check Object Type is OK
        $this->assertEquals(
            $objectType,
            $committed['type'],
            "Change Commit => Object Type is wrong."
        );

        //====================================================================//
        //   Check Object Action is OK
        $this->assertEquals(
            $action,
            $committed['action'],
            "Change Commit => Change Type is wrong."
        );

        //====================================================================//
        //   Check Object Id value Format
        $committedObjectIds = $committed['id'];
        //====================================================================//
        //   Check each Object Ids
        foreach ($committedObjectIds as $committedObjectId) {
            $this->assertIsScalar(
                $committedObjectId,
                "Change Commit => Object Id Array Value is in wrong Format. "
                    ."(Expected String or Integer. / Given "
                    .print_r($committedObjectId, true)
            );
        }
        //====================================================================//
        //   Extract First Object Id
        $firstId = array_shift($committedObjectIds);
        //====================================================================//
        //   Verify First Object Id is OK
        $this->assertEquals(
            $firstId,
            $objectId,
            "Change Commit => Object Id is wrong. (Expected ".$objectId." / Given ".$firstId
        );

        //====================================================================//
        //   Check Infos are Not Empty
        $this->assertNotEmpty($committed['user'], "Change Commit => User Name is Empty");
        $this->assertNotEmpty($committed['comment'], "Change Commit => Action Comment is Empty");
    }

    /**
     * Load Object Field from List
     *
     * @param string $itemType Field Microdata Type Url
     * @param string $itemProp Field Microdata Property Name
     *
     * @return null|array
     */
    private function loadObjectFieldByTag(string $itemType, string $itemProp): ?array
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
     * @param string      $itemType    Field Microdata Type Url
     * @param string      $itemProp    Field Microdata Property Name
     * @param string      $testComment
     * @param null|string $comment
     *
     * @return string
     */
    private static function buildResult(
        string $itemType,
        string $itemProp,
        string $testComment,
        string $comment = null
    ): string {
        return $comment." (".$itemType.":".$itemProp.") ".$testComment;
    }
}
