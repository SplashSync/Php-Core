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

namespace Splash\Tests\Tools;

use ArrayObject;
use Splash\Client\Splash;

/**
 * @abstract    Splash Test Tools - Objects Test Case Base Class
 *
 * @author SplashSync <contact@splashsync.com>
 */
class ObjectsCase extends AbstractBaseCase
{
    use \Splash\Models\Fields\FieldsManagerTrait;
    use \Splash\Tests\Tools\Traits\ObjectsDataTrait;
    use \Splash\Tests\Tools\Traits\ObjectsFakerTrait;
    
    /**
     * @abstract    List of Created & Tested Object used to delete if test failled.
     * @var     array
     */
    private $createdObjects  =   array();
    
    /**
     * Formater Fake Field Generator Options
     * @var array
     */
    private $settings = array(
        //==============================================================================
        //  List generation
        'ListItems'                 =>  2,               // Number of Items to Add in Lists

        //==============================================================================
        //  Double & Prices Fields
        "DoublesPrecision"          =>  6,              // Default Doubles Compare Precision (Number of Digits)
        
        //==============================================================================
        //  Currency Fields
        "Currency"                  =>  "EUR",          // Default Currency
            
        //==============================================================================
        //  Price Fields
        "VAT"                       =>  20,              // Default Vat Rate
        "PriceBase"                 =>  "HT",            // Default Price base
        "PricesPrecision"           =>  6,               // Default Prices Compare Precision (Number of Digits)
            
        //==============================================================================
        //  Url Generator Parameters
        "Url_Prefix"                => "",               // Add a prefix to generated Url (i.e: http://)
        "Url_Sufix"                 => ".splashsync.com",// Add a sufix to generated Url
            
        //==============================================================================
        //  Multilanguage Fields
        "Langs"                     =>  array(          // Available Languages for Multilang Fields
            "en_US",
            "fr_FR",
            "fr_BE",
            "fr_CA",
        ),
            
        //==============================================================================
        //  Country Fields
        "Country"                    =>  array(          // Defaults State Iso Codes
            "US",
            "FR",
            "BE",
            "CA",
        ),
            
        //==============================================================================
        //  State Fields
        "States"                    =>  array(          // Defaults State Iso Codes
            "CA",
            "FL"
        ),
           
        //==============================================================================
        //  Files Fields
        "Files"                    =>  array(          // Defaults Raw Files
            "fake-file1.pdf",
            "fake-file2.pdf",
            "fake-file3.pdf",
            "fake-file4.pdf",
        ),
            
        //==============================================================================
        //  Images Fields
        "Images"                    =>  array(          // Defaults Image Files
            "fake-image1.jpg",
            "fake-image2.jpg",
            "fake-image3.jpg",
            "fake-image4.jpg",
        ),
        
        //            //==============================================================================
        //            //  Objects Id Fields
        //            //  Default is An Empty List To be completed by User Before Generation
        //            "Objects"                   =>  array(),
    );
    
    protected function setUp()
    {
        parent::setUp();
        
        //====================================================================//
        // BOOT or REBOOT MODULE
        Splash::reboot();
        
        //====================================================================//
        // FAKE SPLASH SERVER HOST URL
        Splash::configuration()->WsHost = "No.Commit.allowed.not";
        
        //====================================================================//
        // Load Module Local Configuration (In Safe Mode)
        //====================================================================//
        $this->loadLocalTestParameters();
    }
    
    /**
     * @abstract        Verify Last Commit is Valid and Conform to Expected
     *
     * @param string    $action         Expected Action
     * @param string    $objectType     Expected Object Type
     * @param string    $objectId       Expected Object Id
     */
    public function assertIsLastCommited($action, $objectType, $objectId)
    {
        $this->assertIsCommited($action, $objectType, $objectId, false);
    }

    /**
     * @abstract        Verify First Commit is Valid and Conform to Expected
     *
     * @param string    $action         Expected Action
     * @param string    $objectType     Expected Object Type
     * @param string    $objectId       Expected Object Id
     */
    public function assertIsFirstCommited($action, $objectType, $objectId)
    {
        $this->assertIsCommited($action, $objectType, $objectId, true);
    }
    
    //====================================================================//
    //   Data Provider Functions
    //====================================================================//
    
    public function objectTypesProvider()
    {
        $result = array();
        
        self::setUp();

        //====================================================================//
        // Check if Local Tests Sequences are defined
        if (method_exists(Splash::local(), "TestSequences")) {
            $testSequences  =   Splash::local()->testSequences("List");
        } else {
            $testSequences  =   array( 1 => "None");
        }
        
        //====================================================================//
        //   For Each Test Sequence
        foreach ($testSequences as $testSequence) {
            $this->loadLocalTestSequence($testSequence);
            
            //====================================================================//
            //   For Each Object Type
            foreach (Splash::objects() as $objectType) {
                //====================================================================//
                //   Filter Tested Object Types  =>> Skip
                if (!self::isAllowedObjectType($objectType)) {
                    continue;
                }
                //====================================================================//
                //   Add Object Type to List
                $result[] = array($testSequence, $objectType);
            }
        }
        
        self::tearDown();
        
        return $result;
    }

    public function objectFieldsProvider()
    {
        $result = array();
        
        self::setUp();
        
        //====================================================================//
        // Check if Local Tests Sequences are defined
        if (method_exists(Splash::local(), "TestSequences")) {
            $testSequences  =   Splash::local()->testSequences("List");
        } else {
            $testSequences  =   array( 1 => "None");
        }
        
        //====================================================================//
        //   For Each Test Sequence
        foreach ($testSequences as $testSequence) {
            $this->loadLocalTestSequence($testSequence);
            //====================================================================//
            //   For Each Object Type
            foreach (Splash::objects() as $objectType) {
                //====================================================================//
                //   Filter Tested Object Types  =>> Skip
                if (!self::isAllowedObjectType($objectType)) {
                    continue;
                }
                //====================================================================//
                //   For Each Field Type
                foreach (Splash::object($objectType)->fields() as $field) {
                    //====================================================================//
                    //   Filter Tested Object Fields  =>> Skip
                    if (!self::isAllowedObjectField($field->id)) {
                        continue;
                    }
                    $result[] = array($testSequence, $objectType, $field);
                }
            }
        }
        
        self::tearDown();
        
        return $result;
    }

    protected function loadLocalTestParameters()
    {
        //====================================================================//
        // Safety Check
        if (!method_exists(Splash::local(), "TestParameters")) {
            return;
        }
        //====================================================================//
        // Read Local Parameters
        $localTestSettings  =   Splash::local()->testParameters();
        
        //====================================================================//
        // Validate Local Parameters
        if (!Splash::validate()->isValidLocalTestParameterArray($localTestSettings)) {
            return;
        }
        
        //====================================================================//
        // Import Local Parameters
        foreach ($localTestSettings as $key => $value) {
            $this->settings[$key]   =   $value;
        }
    }
    
    protected function loadLocalTestSequence($testSequence)
    {
        //====================================================================//
        // Check if Local Tests Sequences are defined
        if (!method_exists(Splash::local(), "TestSequences")) {
            return;
        }
        //====================================================================//
        // Setup Test Sequence
        Splash::local()->testSequences($testSequence);
        
        //====================================================================//
        // Reload Local Tests Parameters
        $this->loadLocalTestParameters();
    }
    
    /**
     * @abstract        Set Current Tested Object to Filter Objects List upon Fake ObjectId Creation
     *
     * @param string    $objectType     Expected Object Type
     * @param string    $objectId       Expected Object Id
     */
    protected function setCurrentObject($objectType, $objectId)
    {
        $this->settings["CurrentType"]  =   $objectType;
        $this->settings["CurrentId"]    =   $objectId;
    }
    
    //==============================================================================
    //      OBJECTS DELETE AT THE END OF TESTS
    //==============================================================================
    
    protected function addTestedObject($objectType, $objectId = null)
    {
        $this->createdObjects[] =   array(
            "ObjectType"    =>  $objectType,
            "ObjectId"      =>  $objectId,
        );
    }
    
    protected function cleanTestedObjects()
    {
        foreach ($this->createdObjects as $object) {
            if (empty($object["ObjectId"])) {
                continue;
            }
            //====================================================================//
            //   Verify Delete is Allowed
            $definition = Splash::object($object["ObjectType"])->description();
            if ($definition["allow_push_deleted"]) {
                continue;
            }
            Splash::object($object["ObjectType"])->delete($object["ObjectId"]);
        }
    }
      
    /**
     * @abstract        Verify First Commit is Valid and Conform to Expected
     *
     * @param string    $action         Expected Action
     * @param string    $objectType     Expected Object Type
     * @param string    $objectId       Expected Object Id
     * @param bool      $first          Check First or Last Commited
     *
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
                . "(Expected " . $objectType . " / Given " . $commited->type
        );
        
        //====================================================================//
        //   Check Object Action is OK
        $this->assertEquals(
            $commited->action,
            $action,
            "Change Commit => Change Type is wrong. (Expected " . $action . " / Given " . $commited->action
        );
        
        //====================================================================//
        //   Check Object Id value Format
        $this->assertTrue(
            is_scalar($commited->id) || is_array($commited->id) || is_a($commited->id, "ArrayObject"),
            "Change Commit => Object Id Value is in wrong Format. "
                . "(Expected String or Array of Strings. / Given "
                . print_r($commited->id, true)
        );
        
        //====================================================================//
        //   If Commited an Array of Ids
        if (is_array($commited->id) || is_a($commited->id, "ArrayObject")) {
            //====================================================================//
            //   Detect Array Object
            if ($commited->id instanceof ArrayObject) {
                $commited->id   =   $commited->id->getArrayCopy();
            }
            //====================================================================//
            //   Check each Object Ids
            foreach ($commited->id as $objectId) {
                $this->assertInternalType(
                    'scalar',
                    $objectId,
                    "Change Commit => Object Id Array Value is in wrong Format. "
                        . "(Expected String or Integer. / Given "
                        . print_r($objectId, true)
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
                "Change Commit => Object Id is wrong. (Expected " . $objectId . " / Given " . $firstId
            );
        } else {
            //====================================================================//
            //   Check Object Id is OK
            $this->assertEquals(
                $commited->id,
                $objectId,
                "Change Commit => Object Id is wrong. (Expected " . $objectId . " / Given " . $commited->id
            );
        }
        
        //====================================================================//
        //   Check Infos are Not Empty
        $this->assertNotEmpty($commited->user, "Change Commit => User Name is Empty");
        $this->assertNotEmpty($commited->comment, "Change Commit => Action Comment is Empty");
    }
}
