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
use PHPUnit\Framework\TestCase;
use Splash\Tests\Tools\Fields\FieldInterface;
use Splash\Tests\Tools\Fields\Ooobjectid as ObjectId;

/**
 * Splash Test Tools - Objects Data Management
 *
 * @author SplashSync <contact@splashsync.com>
 */
trait ObjectsDataTrait
{
    /**
     * Check Two Data Blocks Have Similar Data
     *
     * @param array|ArrayObject $block1         Raw Data to Compare
     * @param array|ArrayObject $block2         Raw Data to Compare
     * @param null|TestCase     $testController Provide PhpUnit Test Controller Class to Use PhpUnit assertions
     * @param null|string       $comment        Comment on this Test
     *
     * @return bool
     */
    public function compareRawData($block1, $block2, $testController = null, $comment = null)
    {
        //====================================================================//
        // Filter ArrayObjects
        if ($block1 instanceof ArrayObject) {
            $block1 = $block1->getArrayCopy();
        }
        if ($block2 instanceof ArrayObject) {
            $block2 = $block2->getArrayCopy();
        }

        //====================================================================//
        // Remove Id Data if Present on Block
        if (is_array($block1)) {
            unset($block1['id']);
        }
        if (is_array($block2)) {
            unset($block2['id']);
        }

        //====================================================================//
        // Normalize Data Blocks
        $this->normalize($block1);
        $this->normalize($block2);
        //====================================================================//
        // If Test Controller Given
        if ($testController && ($testController instanceof TestCase)) {
            $testController->assertEquals($block1, $block2, (string) $comment);

            return true;
        }

        //====================================================================//
        // If NO Test Controller Given => Do Raw Array Compare
        //====================================================================//

        //====================================================================//
        // Sort Data Blocks
        $this->sort($block1);
        $this->sort($block2);

        $serialized1 = serialize($block1);
        $serialized2 = serialize($block2);

        return ($serialized1 === $serialized2);
    }

    /**
     * Check Two Object Data Blocks using Field's Compare functions
     *
     * @param array  $fields  Array of OpenObject Fields Definitions
     * @param array  $block1  Raw Data to Compare
     * @param array  $block2  Raw Data to Compare
     * @param string $comment Comment on this Test
     *
     * @return bool
     */
    public function compareDataBlocks($fields, $block1, $block2, $comment = null)
    {
        //====================================================================//
        // For Each Object Fields
        /** @var ArrayObject $field */
        foreach ($fields as $field) {
            //====================================================================//
            // If Non Readable Field => Skip Verification
            if (!$field->read) {
                continue;
            }

            //====================================================================//
            // Extract Field Data
            $data1 = $this->filterData($block1, array($field->id));
            $data2 = $this->filterData($block2, array($field->id));

            //====================================================================//
            // Compare List Data
            $fieldType = self::isListField($field->type);
            if ($fieldType) {
                $this->assertIsArray($data1, $comment."->".$field->id);
                $this->assertIsArray($data2, $comment."->".$field->id);
                $result = $this->compareListField(
                    $fieldType["fieldname"],
                    $field->id,
                    $data1,
                    $data2,
                    $comment."->".$field->id
                );

            //====================================================================//
            // Compare Single Fields
            } else {
                $this->assertIsArray($data1);
                $this->assertIsArray($data2);
                $result = $this->compareField(
                    $field->type,
                    $data1[$field->id],
                    $data2[$field->id],
                    $comment."->".$field->id
                );
            }

            //====================================================================//
            // If Compare Failled => Return Fail Code
            if (true !== $result) {
                return $result;
            }
        }

        return true;
    }

    /**
     * Check Two Object Data Blocks using Field's Compare functions
     *
     * @param string      $fieldType Field Type Name
     * @param array       $block1    Raw Data to Compare
     * @param array       $block2    Raw Data to Compare
     * @param null|string $comment   Comment on this Test
     *
     * @return bool
     */
    private function compareField($fieldType, $block1, $block2, $comment = null)
    {
        //====================================================================//
        // Build Full ClassName
        if (ObjectId::objectId($fieldType)) {
            $className = self::isValidType("objectid");
        } else {
            $className = self::isValidType($fieldType);
        }
        if (false === $className) {
            return false;
        }

        //====================================================================//
        // Verify Class has its own Validate & Compare Function*
        $this->assertTrue(
            is_subclass_of($className, FieldInterface::class),
            "Field of type ".$fieldType." must Implement ".FieldInterface::class
        );

        //====================================================================//
        // Validate Data Using Field Type Validator
        $this->assertTrue(
            $className::validate($block1),
            $comment." Source Data is invalid ".$fieldType." Field Data Block (".print_r($block1, true).")"
        );
        $this->assertTrue(
            $className::validate($block2),
            $comment." Target Data is invalid ".$fieldType." Field Data Block (".print_r($block2, true).")"
        );

        //====================================================================//
        // Compare Data Using Field Type Comparator
        if (!$className::compare($block1, $block2, $this->settings)) {
            echo PHP_EOL."Source :".print_r($block1, true);
            echo PHP_EOL."Target :".print_r($block2, true);
        }
        $this->assertTrue(
            $className::compare($block1, $block2, $this->settings),
            $comment." Source and Target Data are not similar ".$fieldType." Field Data Block"
        );

        return true;
    }

    /**
     * Check Two List Data Blocks using Field's Compare functions
     *
     * @param string      $fieldType Field Type Name
     * @param string      $fieldId   Field Identifier
     * @param array       $block1    Raw Data to Compare
     * @param array       $block2    Raw Data to Compare
     * @param null|string $comment   Comment on this Test
     *
     * @return bool
     */
    private function compareListField($fieldType, $fieldId, $block1, $block2, $comment = null)
    {
        //====================================================================//
        // Explode List Field Id
        $fieldIdArray = self::isListField($fieldId);
        $this->assertNotEmpty($fieldIdArray);
        $this->assertIsArray($fieldIdArray);
        $fieldName = $fieldIdArray["fieldname"];
        $listName = $fieldIdArray["listname"];

        //====================================================================//
        // Extract List Data
        $list1 = isset($block1[$listName]) ? $block1[$listName] : array();
        $list2 = $block2[$listName];

        //====================================================================//
        // Verify Data Count is similar
        $this->assertEquals(
            count(self::toArray($list1)),
            count(self::toArray($list2)),
            "Source and Target List Data have different number of Items "
                .PHP_EOL." Source ".print_r($list1, true)
                .PHP_EOL." Target ".print_r($list2, true)
        );

        //====================================================================//
        // Normalize Data Blocks
        $this->normalize($list1);
        $this->normalize($list2);
        while (!empty($list1)) {
            //====================================================================//
            // Extract Next Item
            $item1 = array_shift($list1);
            $item2 = array_shift($list2);

            //====================================================================//
            // Verify List field is Available
            $this->assertArrayHasKey(
                $fieldName,
                $item1,
                "Field ".$fieldType." not found in Source List Data "
            );
            $this->assertArrayHasKey(
                $fieldName,
                $item2,
                "Field ".$fieldType." not found in Target List Data "
            );

            //====================================================================//
            // Compare Items
            $result = $this->compareField($fieldType, $item1[$fieldName], $item2[$fieldName], $comment);
            if (true !== $result) {
                return $result;
            }
        }

        return true;
    }
}
