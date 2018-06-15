<?php

namespace Splash\Tests\Tools\Traits;

use Splash\Tests\Tools\Fields\Ooobjectid as ObjectId;

/**
 * @abstract    Splash Test Tools - Objects Data Management
 *
 * @author SplashSync <contact@splashsync.com>
 */
trait ObjectsDataTrait
{
    //==============================================================================
    //      OBJECTS DATA BLOCKS FUNCTIONS
    //==============================================================================
        
    /**
     *   @abstract   Extract Raw Field Data from an Object Data Block
     *
     *   @param      array      $DataBlock          Object Data Block
     *   @param      string      $Filter            Single Fields Id
     *
     *   @return     array
     */
    public static function extractRawData($DataBlock, $Filter)
    {
        $FilteredData   =   self::filterData($DataBlock, array($Filter));
        
        //====================================================================//
        // Explode List Field Id
        $List       =   self::isListField($Filter);
        
        //====================================================================//
        // Simple Single Field
        if (!$List) {
            if (isset($FilteredData[$Filter])) {
                return $FilteredData[$Filter];
            }
            
        //====================================================================//
        // List Field
        } else {
            //====================================================================//
            // Check List Exists
            if (!array_key_exists($List["listname"], $FilteredData)) {
                return null;
            }
            
            //====================================================================//
            // Parse Raw List Data
            $Result = array();
            foreach ($FilteredData[$List["listname"]] as $Key => $ListItem) {
                $Result[$Key]   =   $ListItem[$List["fieldname"]];
            }
            return $Result;
        }
        
        //====================================================================//
        // Field Not Received or is Empty
        return null;
    }
    
    /**
     *   @abstract   Filter a Object Data Block to keap only given Fields
     *
     *   @param      array      $DataBlock      Object Data Block
     *   @param      array      $Filters        Array of Fields Ids
     *
     *   @return     array
     */
    public static function filterData($DataBlock, $Filters = array())
    {
        $Result         =   array();
        $ListFilters    =   array();
        
        //====================================================================//
        // Process All Single Fields Ids & Store Sorted List Fields Ids
        foreach ($Filters as $FieldId) {
            //====================================================================//
            // Explode List Field Id
            $List       =   self::isListField($FieldId);
            //====================================================================//
            // Single Field Data Type
            if ((!$List) && (array_key_exists($FieldId, $DataBlock))) {
                $Result[$FieldId] = $DataBlock[$FieldId];
            } elseif (!$List) {
                continue;
            }
            //====================================================================//
            // List Field Data Type
            $ListName   =   $List["listname"];
            $FieldName  =   $List["fieldname"];
            //====================================================================//
            // Check List Data are Present in Block
            if (!array_key_exists($ListName, $DataBlock)) {
                continue;
            }
            //====================================================================//
            // Create List
            if (!array_key_exists($ListName, $ListFilters)) {
                $ListFilters[$ListName] = array();
            }
            $ListFilters[$ListName][] = $FieldName;
        }
        
        //====================================================================//
        // Process All List Fields Ids Filters
        foreach ($ListFilters as $ListName => $ListFilters) {
            $Result[$ListName] = self::filterListData($DataBlock[$ListName], $ListFilters);
        }
        
        return $Result;
    }
    
    /**
     *   @abstract   Filter a Object List Data Block to keap only given Fields
     *
     *   @param      array      $ListBlock  Object Data Block
     *   @param      array      $Filters    Array of Fields Ids
     *
     *   @return     array
     */
    public static function filterListData($ListBlock, $Filters = array())
    {
        $Result =   array();
        
        foreach ($ListBlock as $ItemBlock) {
            $FilteredItems = array();
            
            //====================================================================//
            // Search for Field in Item Block
            if (!is_array($ItemBlock) && !is_a($ItemBlock, "ArrayObject")) {
                continue;
            }
            
            //====================================================================//
            // Search for Field in Item Block
            foreach ($Filters as $FieldId) {
                if (array_key_exists($FieldId, $ItemBlock)) {
                    $FilteredItems[$FieldId] = $ItemBlock[$FieldId];
                }
            }
            
            $Result[] = $FilteredItems;
        }
        
        return $Result;
    }
    
    /**
     *  @abstract   Normalize An Object Data Block (ie: before Compare)
     *
     *  @param      mixed       $array      Input Array
     *
     *  @return     array                   Sorted Array
     */
    public static function normalize(&$In)
    {
       
        //==============================================================================
        //      Convert ArrayObjects To Simple Array
        if (is_a($In, "ArrayObject")) {
            $In = $In->getArrayCopy();
            //==============================================================================
            // Normalize Contents
            self::normalize($In);
            
        //==============================================================================
        // Normalize Array Contents
        } elseif (is_array($In)) {
            foreach ($In as &$value) {
                self::normalize($value);
            }
            
            //==============================================================================
        // Normalize Bool as Strings
        } elseif (is_bool($In)) {
            $In = $In?"1":"0";
            
        //==============================================================================
        // Normalize Numbers as Strings
        } elseif (is_numeric($In)) {
            $In = strval($In);
        }
        
        return $In;
    }
    
    /**
    *   @abstract   kSort of An Object Data Block (ie: before Compare)
    *
    *   @param      array       $array      Input Array
    *
    *   @return     array                   Sorted Array
    */
    public static function sort(&$In)
    {
        if (!is_array($In)) {
            return $In;
        }
            
        //==============================================================================
        // Sort All Sub-Contents
        foreach ($In as &$value) {
            if (is_array($value)) {
                self::sort($value);
            }
        }
        return ksort($In);
    }
    
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
