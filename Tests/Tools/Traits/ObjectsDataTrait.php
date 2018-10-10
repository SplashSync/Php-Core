<?php

namespace Splash\Tests\Tools\Traits;

use Splash\Tests\Tools\Fields\Ooobjectid as ObjectId;

use Splash\Models\Fields\FieldsManagerTrait;

/**
 * @abstract    Splash Test Tools - Objects Data Management
 *
 * @author SplashSync <contact@splashsync.com>
 */
trait ObjectsDataTrait
{
    use FieldsManagerTrait;
        
    /**
     * @abstract    Check Two Data Blocks Have Similar Data
     *
     * @param   array   $Block1             Raw Data to Compare
     * @param   array   $Block2             Raw Data to Compare
     * @param   object  $TestController     Provide PhpUnit Test Controller Class to Use PhpUnit assertions
     * @param   string  $Comment            Comment on this Test
     *
     * @return bool
     */
    public function compareRawData($Block1, $Block2, $TestController = null, $Comment = null)
    {
        //====================================================================//
        // Filter ArrayObjects
        if (is_a($Block1, "ArrayObject")) {
            $Block1 = $Block1->getArrayCopy();
        }
        if (is_a($Block2, "ArrayObject")) {
            $Block2 = $Block2->getArrayCopy();
        }
        
        //====================================================================//
        // Remove Id Data if Present on Block
        if (is_array($Block1)) {
            unset($Block1['id']);
        }
        if (is_array($Block2)) {
            unset($Block2['id']);
        }
        
        //====================================================================//
        // Normalize Data Blocks
        $this->normalize($Block1);
        $this->normalize($Block2);
        //====================================================================//
        // If Test Controller Given
        if ($TestController) {
            $TestController->assertEquals($Block1, $Block2, $Comment);
            return true;
        }
            
        //====================================================================//
        // If NO Test Controller Given => Do Raw Array Compare
        //====================================================================//
        
        //====================================================================//
        // Sort Data Blocks
        $this->sort($Block1);
        $this->sort($Block2);

        $Serialized1 = serialize($Block1);
        $Serialized2 = serialize($Block2);
        
        return ($Serialized1 === $Serialized2);
    }
    
    /**
     * @abstract    Check Two Object Data Blocks using Field's Compare functions
     *
     * @param   array   $Fields             Array of OpenObject Fields Definitions
     * @param   array   $Block1             Raw Data to Compare
     * @param   array   $Block2             Raw Data to Compare
     * @param   string  $Comment            Comment on this Test
     *
     * @return bool
     */
    public function compareDataBlocks($Fields, $Block1, $Block2, $Comment = null)
    {

        //====================================================================//
        // For Each Object Fields
        foreach ($Fields as $Field) {
            //====================================================================//
            // Extract Field Data
            $Data1        =  $this->filterData($Block1, array($Field->id));
            $Data2        =  $this->filterData($Block2, array($Field->id));

            //====================================================================//
            // Compare List Data
            $FieldType      =  self::isListField($Field->type);
            if ($FieldType) {
                $Result = $this->compareListField(
                    $FieldType["fieldname"],
                    $Field->id,
                    $Data1,
                    $Data2,
                    $Comment . "->" . $Field->id
                );
                
            //====================================================================//
            // Compare Single Fields
            } else {
                $Result = $this->compareField(
                    $Field->type,
                    $Data1[$Field->id],
                    $Data2[$Field->id],
                    $Comment . "->" . $Field->id
                );
            }
                
            //====================================================================//
            // If Compare Failled => Return Fail Code
            if ($Result !== true) {
                return $Result;
            }
        }
        
        return true;
    }
    
    /**
     * @abstract    Check Two Object Data Blocks using Field's Compare functions
     *
     * @param   string  $FieldType          Field Type Name
     * @param   array   $Block1             Raw Data to Compare
     * @param   array   $Block2             Raw Data to Compare
     * @param   string  $Comment            Comment on this Test
     *
     * @return string   error / success translator string for debugger
     */
    private function compareField($FieldType, $Block1, $Block2, $Comment = null)
    {
        
        //====================================================================//
        // Build Full ClassName
        if (ObjectId::decodeIdField($FieldType)) {
            $ClassName      = self::isValidType("objectid");
        } else {
            $ClassName      = self::isValidType($FieldType);
        }
        
        //====================================================================//
        // Verify Class has its own Validate & Compare Function*
        $this->assertTrue(
            method_exists($ClassName, "validate"),
            "Field of type " . $FieldType . " has no Validate Function."
        );
        $this->assertTrue(
            method_exists($ClassName, "compare"),
            "Field of type " . $FieldType . " has no Compare Function."
        );
        
        //====================================================================//
        // Validate Data Using Field Type Validator
        $this->assertTrue(
            $ClassName::validate($Block1),
            $Comment . " Source Data is not a valid " . $FieldType . " Field Data Block (" . print_r($Block1, 1) . ")"
        );
        $this->assertTrue(
            $ClassName::validate($Block2),
            $Comment . " Target Data is not a valid " . $FieldType . " Field Data Block (" . print_r($Block2, 1) . ")"
        );
            
        //====================================================================//
        // Compare Data Using Field Type Comparator
        if (!$ClassName::compare($Block1, $Block2, $this->settings)) {
            echo PHP_EOL . "Source :" . print_r($Block1, true);
            echo PHP_EOL . "Target :" . print_r($Block2, true);
        }
        $this->assertTrue(
            $ClassName::compare($Block1, $Block2, $this->settings),
            $Comment . " Source and Target Data are not similar " . $FieldType . " Field Data Block"
        );

        return true;
    }
    
    /**
     * @abstract    Check Two List Data Blocks using Field's Compare functions
     *
     * @param   string  $FieldType          Field Type Name
     * @param   string  $FieldId            Field Identifier
     * @param   array   $Block1             Raw Data to Compare
     * @param   array   $Block2             Raw Data to Compare
     * @param   string  $Comment            Comment on this Test
     *
     * @return string   error / success translator string for debugger
     */
    private function compareListField($FieldType, $FieldId, $Block1, $Block2, $Comment = null)
    {
        //====================================================================//
        // Explode List Field Id
        $FieldIdArray      =  self::isListField($FieldId);
        $this->assertNotEmpty($FieldIdArray);
        $FieldName  = $FieldIdArray["fieldname"];
        $ListName   = $FieldIdArray["listname"];

        //====================================================================//
        // Extract List Data
        $List1 = $Block1[$ListName];
        $List2 = $Block2[$ListName];
        
        //====================================================================//
        // Verify Data Count is similar
        $this->assertEquals(
            count($List1),
            count($List2),
            "Source and Target List Data have different number of Items "
                . PHP_EOL . " Source " . print_r($List1, true)
                . PHP_EOL . " Target " . print_r($List2, true)
        );

        //====================================================================//
        // Normalize Data Blocks
        $this->normalize($List1);
        $this->normalize($List2);
        while (!empty($List1)) {
            //====================================================================//
            // Extract Next Item
            $Item1  =   array_shift($List1);
            $Item2  =   array_shift($List2);

            //====================================================================//
            // Verify List field is Available
            $this->assertArrayHasKey(
                $FieldName,
                $Item1,
                "Field " . $FieldType . " not found in Source List Data "
            );
            $this->assertArrayHasKey(
                $FieldName,
                $Item2,
                "Field " . $FieldType . " not found in Target List Data "
            );
            
            //====================================================================//
            // Compare Items
            $Result = $this->compareField($FieldType, $Item1[$FieldName], $Item2[$FieldName], $Comment);
            if ($Result !== true) {
                return $Result;
            }
        }
        
        return true;
    }
}
