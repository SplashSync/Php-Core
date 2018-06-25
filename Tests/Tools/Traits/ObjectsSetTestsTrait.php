<?php

namespace Splash\Tests\Tools\Traits;

use Splash\Client\Splash;

/**
 * @abstract    Splash Test Tools - Objects Fields Management
 *
 * @author SplashSync <contact@splashsync.com>
 */
trait ObjectsSetTestsTrait
{
    /**
     * @var array
     */
    protected $Fields;

    /**
     * @var string      Md5 CheckSum of Current Field Data Block
     */
    protected $FieldMd5 = null;
    
    //==============================================================================
    //      COMPLETE TESTS EXECUTION FUNCTIONS
    //==============================================================================
    
    /**
     * @abstract    Execute Single Field Test From Module
     * @param       string      $ObjectType         Splash Object Type Name
     * @param       ArrayObject $Field          Current Tested Field (ArrayObject)
     * @param       string      $ForceObjectId      Object Id (Update) or Null (Create)
     * @return      bool
     */
    protected function coreTestSetSingleFieldFromModule($ObjectType, $Field, $ForceObjectId = null)
    {
        //====================================================================//
        //   OBJECT CREATE TEST
        //====================================================================//
        
        //====================================================================//
        //   Generate Dummy Object Data (Required Fields Only)
        $NewData = $this->prepareForTesting($ObjectType, $Field);
        if ($NewData == false) {
            return true;
        }
        
        //====================================================================//
        //   Execute Create Test
        $ObjectId = $this->setObjectFromModule($ObjectType, $NewData, $ForceObjectId);
                
        //====================================================================//
        //   OBJECT UPDATE TEST
        //====================================================================//
        
        //====================================================================//
        //   Update Data Focused Field Data
        $UpdateData = $this->prepareForTesting($ObjectType, $Field);
        $this->assertNotEmpty($UpdateData);

        //====================================================================//
        //   Execute Update Test
        $this->setObjectFromModule($ObjectType, $UpdateData, $ObjectId);
        
        //====================================================================//
        //   OBJECT DELETE
        //====================================================================//
        
        //====================================================================//
        // If Test was Forced on a Specific Object (Local Sequences)
        if (!is_null($ForceObjectId)) {
            return;
        }
        
        //====================================================================//
        //   Delete Object From Module
        $this->deleteObjectFromModule($ObjectType, $ObjectId);
    }
    
    /**
     * @abstract    Execute Single Field Test From Service
     * @param       string      $ObjectType         Splash Object Type Name
     * @param       ArrayObject $Field          Current Tested Field (ArrayObject)
     * @param       string      $ForceObjectId      Object Id (Update) or Null (Create)
     * @return      bool
     */
    public function coreTestSetSingleFieldFromService($ObjectType, $Field, $ForceObjectId = null)
    {
        //====================================================================//
        //   OBJECT CREATE TEST
        //====================================================================//
        
        //====================================================================//
        //   Generate Dummy Object Data (Required Fields Only)
        $NewData = $this->prepareForTesting($ObjectType, $Field);
        if ($NewData == false) {
            return true;
        }
        
        //====================================================================//
        //   Execute Create Test
        $ObjectId = $this->setObjectFromService($ObjectType, $NewData, $ForceObjectId);
        
        //====================================================================//
        // BOOT or REBOOT MODULE
        $this->setUp();
        
        //====================================================================//
        //   OBJECT UPDATE TEST
        //====================================================================//
        
        //====================================================================//
        //   Generate Dummy Object Data (Required Fields Only)
        $UpdateData = $this->prepareForTesting($ObjectType, $Field);
        $this->assertNotEmpty($UpdateData);
        
        //====================================================================//
        //   Execute Update Test
        $this->setObjectFromService($ObjectType, $UpdateData, $ObjectId);
        
        //====================================================================//
        //   OBJECT DELETE
        //====================================================================//
        
        //====================================================================//
        // If Test was Forced on a Specific Object (Local Sequences)
        if (!is_null($ForceObjectId)) {
            return;
        }
        
        //====================================================================//
        //   Delete Object From Module
        $this->deleteObjectFromModule($ObjectType, $ObjectId);
    }
    
    //==============================================================================
    //      UNIT TESTS EXECUTION FUNCTIONS
    //==============================================================================
    
    /**
     * @abstract    Execute Object Create or Update Test with Given Data (From Module)
     * @param       string      $ObjectType         Splash Object Type Name
     * @param       array       $ObjectData         Splash Data Block
     * @param       string      $ForceObjectId      Object Id (Update) or Null (Create)
     */
    protected function setObjectFromModule($ObjectType, $ObjectData, $ForceObjectId = null)
    {
        //====================================================================//
        // Lock New Objects To Avoid Action Commit
        Splash::object($ObjectType)->lock($ForceObjectId);
        //====================================================================//
        // Clean Objects Commited Array
        Splash::$Commited = array();
        //====================================================================//
        //   Update Object on Module
        $ObjectId = Splash::object($ObjectType)->set($ForceObjectId, $ObjectData);
        //====================================================================//
        //   Verify Response
        $this->verifySetResponse($ObjectType, $ObjectId, ($ForceObjectId ? SPL_A_UPDATE : SPL_A_CREATE), $ObjectData);
        //====================================================================//
        // UnLock New Objects To Avoid Action Commit
        Splash::object($ObjectType)->unLock();
        //====================================================================//
        // Lock This Object To Avoid Being Selected for Linking
        $this->setCurrentObject($ObjectType, $ObjectId);
        //====================================================================//
        // Retun Object Id
        return $ObjectId;
    }
    
    /**
     * @abstract    Execute Object Create or Update Test with Given Data (From Service)
     * @param       string      $ObjectType         Splash Object Type Name
     * @param       array       $ObjectData         Splash Data Block
     * @param       string      $ForceObjectId      Object Id (Update) or Null (Create)
     */
    protected function setObjectFromService($ObjectType, $ObjectData, $ForceObjectId = null)
    {
        //====================================================================//
        // Clean Objects Commited Array
        Splash::$Commited = array();
        //====================================================================//
        //   Create a New Object via Service
        $ObjectId = $this->genericAction(
            SPL_S_OBJECTS,
            SPL_F_SET,
            __METHOD__,
            [ "id" => $ForceObjectId, "type" => $ObjectType, "fields" => $ObjectData]
        );
        //====================================================================//
        //   Verify Response
        $this->verifySetResponse($ObjectType, $ObjectId, ($ForceObjectId ? SPL_A_UPDATE : SPL_A_CREATE), $ObjectData);
        //====================================================================//
        // UnLock New Objects To Avoid Action Commit
        Splash::object($ObjectType)->unLock();
        //====================================================================//
        // Lock This Object To Avoid Being Selected for Linking
        $this->setCurrentObject($ObjectType, $ObjectId);
        //====================================================================//
        // Retun Object Id
        return $ObjectId;
    }
    
    /**
     * @abstract    Execute Object Delete Test (From Module)
     * @param       string      $ObjectType         Splash Object Type Name
     * @param       string      $ObjectId           Object Id
     */
    protected function deleteObjectFromModule($ObjectType, $ObjectId)
    {
        //====================================================================//
        // Lock New Objects To Avoid Action Commit
        Splash::object($ObjectType)->lock($ObjectId);
        //====================================================================//
        //   Delete Object on Module
        $Data = Splash::object($ObjectType)->delete($ObjectId);
        //====================================================================//
        //   Verify Response
        $this->verifyDeleteResponse($ObjectType, $ObjectId, $Data);
    }
    
    //==============================================================================
    //      TESTS PREPARATION FUNCTIONS
    //==============================================================================

    protected function verifyTestIsAllowed($ObjectType, $Field = null)
    {
        $Definition = Splash::object($ObjectType)->description();

        $this->assertNotEmpty($Definition);
        //====================================================================//
        //   Verify Create is Allowed
        if (!$Definition["allow_push_created"]) {
            return false;
        }
        //====================================================================//
        //   Verify Update is Allowed
        if (!$Definition["allow_push_updated"]) {
            return false;
        }
        //====================================================================//
        //   Verify Delete is Allowed
        if (!$Definition["allow_push_deleted"]) {
            return false;
        }
        //====================================================================//
        //   Verify Field is To Be Tested
        if (!is_null($Field) && $Field->notest) {
            return false;
        }
        
        return true;
    }

    /**
     * @abstract    Generate Fake Object Data
     *              -> This Function uses Preloaded Fields
     *              -> If Md5 provided, check Current Field was Modified
     *
     * @param       string      $ObjectType     Current Object Type
     * @param       ArrayObject $Field          Current Tested Field (ArrayObject)
     * @param       bool        $Unik           Ask for Unik Field Data
     *
     * @return      array|bool      Generated Data Block or False if not Allowed
     */
    protected function generateObjectData($ObjectType, $Field, $Unik = false)
    {
        //====================================================================//
        // Generate Required Fields List
        $this->Fields   =   $this->fakeFieldsList($ObjectType, [$Field->id], true);
        
        //====================================================================//
        // Prepare Fake Object Data
        //====================================================================//
        $Try = 0;
        do {
            //====================================================================//
            // Generate Object Data
            $FakeData       =   $this->fakeObjectData($this->Fields);
            if ($FakeData == false) {
                return false;
            }
            //====================================================================//
            // Check if Compare is Required
            if (($Unik == false) || (empty($this->FieldMd5))) {
                return $FakeData;
            }
            
            $FakeDataMd5 = md5(serialize($this->filterData($FakeData, [$Field->id])));

            //====================================================================//
            //   Ensure Field Data was modified
            $Try++;
        } while (($this->FieldMd5 === $FakeDataMd5) && ($Try < 5));
        
        //====================================================================//
        // Store MD5 of New Generated Field Data
        $this->FieldMd5 = md5(serialize($this->filterData($FakeData, [$Field->id])));

        //====================================================================//
        // Return Generated Object Data
        return $FakeData;
    }
    
    /**
     * @abstract    Ensure Set/Write Test is Possible & Generate Fake Object Data
     *              -> This Function uses Preloaded Fields
     *              -> If Md5 provided, check Current Field was Modified
     *
     * @param       string      $ObjectType     Current Object Type
     * @param       ArrayObject $Field          Current Tested Field (ArrayObject)
     * @param       bool        $Unik           Ask for Unik Field Data
     *
     * @return      array|bool      Generated Data Block or False if not Allowed
     */
    public function prepareForTesting($ObjectType, $Field, $Unik = false)
    {
        //====================================================================//
        //   Verify Test is Required
        if (!$this->verifyTestIsAllowed($ObjectType, $Field)) {
            return false;
        }
        
        //====================================================================//
        // Return Generated Object Data
        return $this->generateObjectData($ObjectType, $Field, $Unik);
    }
    
    
    
    //==============================================================================
    //      DATA VERIFICATION FUNCTIONS
    //==============================================================================
    
    public function verifySetResponse($ObjectType, $ObjectId, $Action, $ExpectedData)
    {
        //====================================================================//
        //   Verify Object Id Is Not Empty
        $this->assertNotEmpty($ObjectId, "Returned New Object Id is Empty");

        //====================================================================//
        //   Add Object Id to Created List
        $this->addTestedObject($ObjectType, $ObjectId);
        
        //====================================================================//
        //   Verify Object Id Is in Right Format
        $this->assertTrue(
            is_integer($ObjectId) || is_string($ObjectId),
            "New Object Id is not an Integer or a Strings"
        );
        
        //====================================================================//
        //   Verify Object Change Was Commited
        $this->assertIsFirstCommited($Action, $ObjectType, $ObjectId);
        
        //====================================================================//
        //   Read Object Data
        $CurrentData    =   Splash::object($ObjectType)
                ->get($ObjectId, $this->reduceFieldList($this->Fields));
        //====================================================================//
        //   Verify Object Data are Ok
        $this->compareDataBlocks($this->Fields, $ExpectedData, $CurrentData, $ObjectType);
    }
    
    public function verifyDeleteResponse($ObjectType, $ObjectId, $Data)
    {
        //====================================================================//
        //   Verify Response
        $this->assertIsSplashBool($Data, "Object Delete Response Must be a Bool");
        $this->assertNotEmpty($Data, "Object Delete Response is Not True");
        
        //====================================================================//
        // Lock New Objects To Avoid Action Commit
        Splash::object($ObjectType)->lock($ObjectId);
        
        //====================================================================//
        //   Verify Repeating Delete as Same Result
        $RepeatedResponse    =   Splash::object($ObjectType)->delete($ObjectId);
        $this->assertTrue(
            $RepeatedResponse,
            "Object Repeated Delete, Must return True even if Object Already Deleted."
        );
        
        //====================================================================//
        //   Verify Object not Present anymore
        $Fields = $this->reduceFieldList(Splash::object($ObjectType)->fields(), true, false);
        $GetResponse    =   Splash::object($ObjectType)->get($ObjectId, $Fields);
        $this->assertFalse($GetResponse, "Object Not Delete, I can still read it!!");
    }
}
