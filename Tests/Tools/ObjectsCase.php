<?php

namespace Splash\Tests\Tools;

use Splash\Client\Splash;

/**
 * @abstract    Splash Test Tools - Objects Test Case Base Class
 *
 * @author SplashSync <contact@splashsync.com>
 */
class ObjectsCase extends AbstractBaseCase
{

    use \Splash\Tests\Tools\Traits\ObjectsFieldsTrait;
    use \Splash\Tests\Tools\Traits\ObjectsDataTrait;
    use \Splash\Tests\Tools\Traits\ObjectsFakerTrait;
    use \Splash\Tests\Tools\Traits\ObjectsValidatorTrait;
    
    /*
     * @abstract    List of Created & Tested Object used to delete if test failled.
     */
    private $CreatedObjects  =   array();
    
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

    protected function loadLocalTestParameters()
    {
        //====================================================================//
        // Safety Check
        if (is_null(Splash::local()) || !method_exists(Splash::local(), "TestParameters")) {
            return;
        }
        //====================================================================//
        // Read Local Parameters
        $LocalTestSettings  =   Splash::local()->testParameters();
        
        //====================================================================//
        // Validate Local Parameters
        if (!Splash::validate()->isValidLocalTestParameterArray($LocalTestSettings)) {
            return;
        }
        //====================================================================//
        // Import Local Parameters
        foreach ($LocalTestSettings as $key => $value) {
            $this->settings[$key]   =   $value;
        }
    }
    
    protected function loadLocalTestSequence($Sequence)
    {
        //====================================================================//
        // Check if Local Tests Sequences are defined
        if (is_null(Splash::local()) || !method_exists(Splash::local(), "TestSequences")) {
            return;
        }
        //====================================================================//
        // Setup Test Sequence
        Splash::local()->testSequences($Sequence);
        
        //====================================================================//
        // Reload Local Tests Parameters
        $this->loadLocalTestParameters();
    }
    
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
     * @param string    $Action         Expected Action
     * @param string    $ObjectType     Expected Object Type
     * @param string    $ObjectId       Expected Object Id
     */
    public function assertIsLastCommited($Action, $ObjectType, $ObjectId)
    {
        $this->assertIsCommited($Action, $ObjectType, $ObjectId, false);
    }

    /**
     * @abstract        Verify First Commit is Valid and Conform to Expected
     *
     * @param string    $Action         Expected Action
     * @param string    $ObjectType     Expected Object Type
     * @param string    $ObjectId       Expected Object Id
     */
    public function assertIsFirstCommited($Action, $ObjectType, $ObjectId)
    {
        $this->assertIsCommited($Action, $ObjectType, $ObjectId, true);
    }
      
    /**
     * @abstract        Verify First Commit is Valid and Conform to Expected
     *
     * @param string    $Action         Expected Action
     * @param string    $ObjectType     Expected Object Type
     * @param string    $ObjectId       Expected Object Id
     * @param bool      $First          Check First or Last Commited
     *
     */
    private function assertIsCommited($Action, $ObjectType, $ObjectId, $First = true)
    {
        //====================================================================//
        //   Verify Object Change Was Commited
        $this->assertNotEmpty(
            Splash::$Commited,
            "No Object Change Commited by your Module. Please check your triggers."
        );
        
        //====================================================================//
        //   Get First / Last Commited
        $Commited = $First ? array_shift(Splash::$Commited) : array_pop(Splash::$Commited);
        
        //====================================================================//
        //   Check Object Type is OK
        $this->assertEquals(
            $Commited->type,
            $ObjectType,
            "Change Commit => Object Type is wrong. "
                . "(Expected " . $ObjectType . " / Given " . $Commited->type
        );
        
        //====================================================================//
        //   Check Object Action is OK
        $this->assertEquals(
            $Commited->action,
            $Action,
            "Change Commit => Change Type is wrong. (Expected " . $Action . " / Given " . $Commited->action
        );
        
        //====================================================================//
        //   Check Object Id value Format
        $this->assertTrue(
            is_scalar($Commited->id) || is_array($Commited->id) || is_a($Commited->id, "ArrayObject"),
            "Change Commit => Object Id Value is in wrong Format. "
                . "(Expected String or Array of Strings. / Given "
                . print_r($Commited->id, true)
        );
        
        //====================================================================//
        //   If Commited an Array of Ids
        if (is_array($Commited->id) || is_a($Commited->id, "ArrayObject")) {
            //====================================================================//
            //   Check each Object Ids
            foreach ($Commited->id as $Id) {
                $this->assertTrue(
                    is_scalar($Id),
                    "Change Commit => Object Id Array Value is in wrong Format. "
                        . "(Expected String or Integer. / Given "
                        . print_r($Id, true)
                );
            }
            //====================================================================//
            //   Extract First Object Id
            $FirstId = array_shift($Commited->id);
            //====================================================================//
            //   Verify First Object Id is OK
            $this->assertEquals(
                $FirstId,
                $ObjectId,
                "Change Commit => Object Id is wrong. (Expected " . $ObjectId . " / Given " . $FirstId
            );
        } else {
            //====================================================================//
            //   Check Object Id is OK
            $this->assertEquals(
                $Commited->id,
                $ObjectId,
                "Change Commit => Object Id is wrong. (Expected " . $ObjectId . " / Given " . $Commited->id
            );
        }
        
        //====================================================================//
        //   Check Infos are Not Empty
        $this->assertNotEmpty($Commited->user, "Change Commit => User Name is Empty");
        $this->assertNotEmpty($Commited->comment, "Change Commit => Action Comment is Empty");
    }
    
    /**
     * @abstract        Set Current Tested Object to Filter Objects List upon Fake ObjectId Creation
     *
     * @param string    $ObjectType     Expected Object Type
     * @param string    $ObjectId       Expected Object Id
     */
    protected function setCurrentObject($ObjectType, $ObjectId)
    {
        $this->settings["CurrentType"]  =   $ObjectType;
        $this->settings["CurrentId"]    =   $ObjectId;
    }
    
    //====================================================================//
    //   Data Provider Functions
    //====================================================================//
    
    public function objectTypesProvider()
    {
        $Result = array();
        
        self::setUp();

        //====================================================================//
        // Check if Local Tests Sequences are defined
        if (!is_null(Splash::local()) && method_exists(Splash::local(), "TestSequences")) {
            $Sequences  =   Splash::local()->testSequences("List");
        } else {
            $Sequences  =   array( 1 => "None");
        }
        
        //====================================================================//
        //   For Each Test Sequence
        foreach ($Sequences as $Sequence) {
            $this->loadLocalTestSequence($Sequence);
            
            //====================================================================//
            //   For Each Object Type
            foreach (Splash::objects() as $ObjectType) {
                //====================================================================//
                //   Filter Tested Object Types  =>> Skip
                if (!self::isAllowedObjectType($ObjectType)) {
                    continue;
                }
                //====================================================================//
                //   Add Object Type to List
                $Result[] = array($Sequence, $ObjectType);
            }
        }
        
        self::tearDown();
        
        return $Result;
    }

    public function objectFieldsProvider()
    {
        $Result = array();
        
        self::setUp();
        
        //====================================================================//
        // Check if Local Tests Sequences are defined
        if (!is_null(Splash::local()) && method_exists(Splash::local(), "TestSequences")) {
            $Sequences  =   Splash::local()->testSequences("List");
        } else {
            $Sequences  =   array( 1 => "None");
        }
        
        //====================================================================//
        //   For Each Test Sequence
        foreach ($Sequences as $Sequence) {
            $this->loadLocalTestSequence($Sequence);
            //====================================================================//
            //   For Each Object Type
            foreach (Splash::objects() as $ObjectType) {
                //====================================================================//
                //   Filter Tested Object Types  =>> Skip
                if (!self::isAllowedObjectType($ObjectType)) {
                    continue;
                }
                //====================================================================//
                //   For Each Field Type
                foreach (Splash::object($ObjectType)->fields() as $Field) {
                    //====================================================================//
                    //   Filter Tested Object Fields  =>> Skip
                    if (!self::isAllowedObjectField($Field->id)) {
                        continue;
                    }
                    $Result[] = array($Sequence, $ObjectType, $Field);
                }
            }
        }
        
        self::tearDown();
        
        return $Result;
    }
    
    //==============================================================================
    //      OBJECTS DELETE AT THE END OF TESTS
    //==============================================================================
    
    protected function addTestedObject($ObjectType, $ObjectId = null)
    {
        $this->CreatedObjects[] =   array(
            "ObjectType"    =>  $ObjectType,
            "ObjectId"      =>  $ObjectId,
        );
    }
    
    protected function cleanTestedObjects()
    {
        foreach ($this->CreatedObjects as $Object) {
            if (empty($Object["ObjectId"])) {
                continue;
            }
            //====================================================================//
            //   Verify Delete is Allowed
            $Definition = Splash::object($Object["ObjectType"])->description();
            if ($Definition["allow_push_deleted"]) {
                continue;
            }
            Splash::object($Object["ObjectType"])->delete($Object["ObjectId"]);
        }
    }
}
