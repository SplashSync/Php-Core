<?php

namespace Splash\Tests\Tools\Traits;

use Splash\Tests\Tools\Fields\Ooobjectid as ObjectId;
use PHPUnit\Framework\TestCase;

/**
 * @abstract    Splash Test Tools - Objects Data Management
 *
 * @author SplashSync <contact@splashsync.com>
 */
trait ObjectsDataTrait
{
        
    /**
     * @abstract    Check Two Data Blocks Have Similar Data
     *
     * @param   array   $block1             Raw Data to Compare
     * @param   array   $block2             Raw Data to Compare
     * @param   TestCase  $testController     Provide PhpUnit Test Controller Class to Use PhpUnit assertions
     * @param   string  $comment            Comment on this Test
     *
     * @return bool
     */
    public function compareRawData($block1, $block2, $testController = null, $comment = null)
    {
        //====================================================================//
        // Filter ArrayObjects
        if (is_a($block1, "ArrayObject")) {
            $block1 = $block1->getArrayCopy();
        }
        if (is_a($block2, "ArrayObject")) {
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
            $testController->assertEquals($block1, $block2, $comment);
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
     * @abstract    Check Two Object Data Blocks using Field's Compare functions
     *
     * @param   array   $fields             Array of OpenObject Fields Definitions
     * @param   array   $block1             Raw Data to Compare
     * @param   array   $block2             Raw Data to Compare
     * @param   string  $comment            Comment on this Test
     *
     * @return bool
     */
    public function compareDataBlocks($fields, $block1, $block2, $comment = null)
    {

        //====================================================================//
        // For Each Object Fields
        foreach ($fields as $field) {
            //====================================================================//
            // Extract Field Data
            $data1        =  $this->filterData($block1, array($field->id));
            $data2        =  $this->filterData($block2, array($field->id));

            //====================================================================//
            // Compare List Data
            $fieldType      =  self::isListField($field->type);
            if ($fieldType) {
                $result = $this->compareListField(
                    $fieldType["fieldname"],
                    $field->id,
                    $data1,
                    $data2,
                    $comment . "->" . $field->id
                );
                
            //====================================================================//
            // Compare Single Fields
            } else {
                $result = $this->compareField(
                    $field->type,
                    $data1[$field->id],
                    $data2[$field->id],
                    $comment . "->" . $field->id
                );
            }
                
            //====================================================================//
            // If Compare Failled => Return Fail Code
            if ($result !== true) {
                return $result;
            }
        }
        
        return true;
    }
    
    /**
     * @abstract    Check Two Object Data Blocks using Field's Compare functions
     *
     * @param   string  $fieldType          Field Type Name
     * @param   array   $block1             Raw Data to Compare
     * @param   array   $block2             Raw Data to Compare
     * @param   string  $comment            Comment on this Test
     *
     * @return bool
     */
    private function compareField($fieldType, $block1, $block2, $comment = null)
    {
        
        //====================================================================//
        // Build Full ClassName
        if (ObjectId::decodeIdField($fieldType)) {
            $className      = self::isValidType("objectid");
        } else {
            $className      = self::isValidType($fieldType);
        }
        
        //====================================================================//
        // Verify Class has its own Validate & Compare Function*
        $this->assertTrue(
            method_exists($className, "validate"),
            "Field of type " . $fieldType . " has no Validate Function."
        );
        $this->assertTrue(
            method_exists($className, "compare"),
            "Field of type " . $fieldType . " has no Compare Function."
        );
        
        //====================================================================//
        // Validate Data Using Field Type Validator
        $this->assertTrue(
            $className::validate($block1),
            $comment . " Source Data is not a valid " . $fieldType . " Field Data Block (" . print_r($block1, 1) . ")"
        );
        $this->assertTrue(
            $className::validate($block2),
            $comment . " Target Data is not a valid " . $fieldType . " Field Data Block (" . print_r($block2, 1) . ")"
        );
            
        //====================================================================//
        // Compare Data Using Field Type Comparator
        if (!$className::compare($block1, $block2, $this->settings)) {
            echo PHP_EOL . "Source :" . print_r($block1, true);
            echo PHP_EOL . "Target :" . print_r($block2, true);
        }
        $this->assertTrue(
            $className::compare($block1, $block2, $this->settings),
            $comment . " Source and Target Data are not similar " . $fieldType . " Field Data Block"
        );

        return true;
    }
    
    /**
     * @abstract    Check Two List Data Blocks using Field's Compare functions
     *
     * @param   string  $fieldType          Field Type Name
     * @param   string  $fieldId            Field Identifier
     * @param   array   $block1             Raw Data to Compare
     * @param   array   $block2             Raw Data to Compare
     * @param   string  $comment            Comment on this Test
     *
     * @return bool
     */
    private function compareListField($fieldType, $fieldId, $block1, $block2, $comment = null)
    {
        //====================================================================//
        // Explode List Field Id
        $fieldIdArray   =   self::isListField($fieldId);
        $this->assertNotEmpty($fieldIdArray);
        $fieldName      =   $fieldIdArray["fieldname"];
        $listName       =   $fieldIdArray["listname"];

        //====================================================================//
        // Extract List Data
        $list1 = $block1[$listName];
        $list2 = $block2[$listName];
        
        //====================================================================//
        // Verify Data Count is similar
        $this->assertEquals(
            count($list1),
            count($list2),
            "Source and Target List Data have different number of Items "
                . PHP_EOL . " Source " . print_r($list1, true)
                . PHP_EOL . " Target " . print_r($list2, true)
        );

        //====================================================================//
        // Normalize Data Blocks
        $this->normalize($list1);
        $this->normalize($list2);
        while (!empty($list1)) {
            //====================================================================//
            // Extract Next Item
            $item1  =   array_shift($list1);
            $item2  =   array_shift($list2);

            //====================================================================//
            // Verify List field is Available
            $this->assertArrayHasKey(
                $fieldName,
                $item1,
                "Field " . $fieldType . " not found in Source List Data "
            );
            $this->assertArrayHasKey(
                $fieldName,
                $item2,
                "Field " . $fieldType . " not found in Target List Data "
            );
            
            //====================================================================//
            // Compare Items
            $result = $this->compareField($fieldType, $item1[$fieldName], $item2[$fieldName], $comment);
            if ($result !== true) {
                return $result;
            }
        }
        
        return true;
    }
}
