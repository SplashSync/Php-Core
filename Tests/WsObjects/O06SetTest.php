<?php
namespace Splash\Tests\WsObjects;

use Splash\Tests\Tools\ObjectsCase;
use Splash\Client\Splash;
use Splash\Tests\Tools\Fields\Ooobjectid;

/**
 * @abstract    Objects Test Suite - Verify Read/Write of any R/W fields is Ok.
 *
 * @author SplashSync <contact@splashsync.com>
 */
class O06SetTest extends ObjectsCase
{
    /**
     * @var array
     */
    private $Fields;
    
    /**
     * @dataProvider objectFieldsProvider
     */
    public function testSetSingleFieldFromModule($Sequence, $ObjectType, $Field, $ForceObjectId = null)
    {
        $this->loadLocalTestSequence($Sequence);
        
        //====================================================================//
        //   Generate Dummy Object Data (Required Fields Only)
        $NewData = $this->prepareForTesting($ObjectType, $Field);
        if ($NewData == false) {
            return true;
        }
        
        //====================================================================//
        //   OBJECT CREATE TEST
        //====================================================================//
        
        //====================================================================//
        // Lock New Objects To Avoid Action Commit
        Splash::object($ObjectType)->lock($ForceObjectId);
        //====================================================================//
        // Clean Objects Commited Array
        Splash::$Commited = array();
        //====================================================================//
        //   Create a New Object on Module
        $ObjectId = Splash::object($ObjectType)->set($ForceObjectId, $NewData);

        //====================================================================//
        //   Verify Response
        $this->verifyResponse($ObjectType, $ObjectId, ($ForceObjectId ? SPL_A_UPDATE : SPL_A_CREATE), $NewData);
        
        //====================================================================//
        // UnLock New Objects To Avoid Action Commit
        Splash::object($ObjectType)->unLock();

        //====================================================================//
        // Lock This Object To Avoid Being Selected for Linking
        $this->setCurrentObject($ObjectType, $ObjectId);
                
        //====================================================================//
        //   OBJECT UPDATE TEST
        //====================================================================//
        
        //====================================================================//
        //   Update Focused Field Data
        $UpdateData = $this->prepareForTesting($ObjectType, $Field);
        if ($UpdateData == false) {
            return true;
        }
        //====================================================================//
        // Lock New Objects To Avoid Action Commit
        Splash::object($ObjectType)->lock($ObjectId);
        //====================================================================//
        // Clean Objects Commited Array
        Splash::$Commited = array();
        //====================================================================//
        //   Update Object on Module
        Splash::object($ObjectType)->set($ObjectId, $UpdateData);
        
        //====================================================================//
        //   Verify Response
        $this->verifyResponse($ObjectType, $ObjectId, SPL_A_UPDATE, $UpdateData);
        
        //====================================================================//
        //   OBJECT DELETE
        //====================================================================//
        
        //====================================================================//
        // If Test was Forced on a Specific Object (Local Sequences)
        if (!is_null($ForceObjectId)) {
            return;
        }
        
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
    
    /**
     * @dataProvider objectFieldsProvider
     */
    public function testSetSingleFieldFromService($Sequence, $ObjectType, $Field, $ForceObjectId = null)
    {
        $this->loadLocalTestSequence($Sequence);
        
        //====================================================================//
        //   Generate Dummy Object Data (Required Fields Only)
        $NewData = $this->prepareForTesting($ObjectType, $Field);
        if ($NewData == false) {
            return true;
        }
        $NewFieldData = md5(serialize($this->filterData($NewData, [$Field->id])));
                
        //====================================================================//
        //   OBJECT CREATE TEST
        //====================================================================//
        
        //====================================================================//
        // Clean Objects Commited Array
        Splash::$Commited = array();
        //====================================================================//
        //   Create a New Object via Service
        $ObjectId = $this->genericAction(
            SPL_S_OBJECTS,
            SPL_F_SET,
            __METHOD__,
            [ "id" => $ForceObjectId, "type" => $ObjectType, "fields" => $NewData]
        );
        //====================================================================//
        //   Verify Response
        $this->verifyResponse($ObjectType, $ObjectId, ($ForceObjectId ? SPL_A_UPDATE : SPL_A_CREATE), $NewData);
        //====================================================================//
        // UnLock New Objects To Avoid Action Commit
        Splash::object($ObjectType)->unLock();
        //====================================================================//
        // Lock This Object To Avoid Being Selected for Linking
        $this->setCurrentObject($ObjectType, $ObjectId);
        
        //====================================================================//
        // BOOT or REBOOT MODULE
        $this->setUp();
        
        //====================================================================//
        //   OBJECT UPDATE TEST
        //====================================================================//
        
        $Try = 0;
        do {
            //====================================================================//
            //   Update Focused Field Data
            $UpdateData = $this->prepareForTesting($ObjectType, $Field);
            if ($UpdateData == false) {
                return true;
            }
            $UpdateFieldData = md5(serialize($this->filterData($UpdateData, [$Field->id])));

            //====================================================================//
            //   Ensure Field Data was modified
            $Try++;
        } while (($UpdateFieldData === $NewFieldData) && ($Try < 5));
        
        //====================================================================//
        // Clean Objects Commited Array
        Splash::$Commited = array();
        //====================================================================//
        //   Create a New Object via Service
        $this->genericAction(
            SPL_S_OBJECTS,
            SPL_F_SET,
            __METHOD__,
            [ "id" => $ObjectId, "type" => $ObjectType, "fields" => $UpdateData]
        );
        
        //====================================================================//
        //   Verify Response
        $this->verifyResponse($ObjectType, $ObjectId, SPL_A_UPDATE, $UpdateData);
        
        //====================================================================//
        //   OBJECT DELETE
        //====================================================================//
        
        //====================================================================//
        // If Test was Forced on a Specific Object (Local Sequences)
        if (!is_null($ForceObjectId)) {
            return;
        }
        
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
    
    
    public function verifyTestIsAllowed($ObjectType, $Field = null)
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

    public function prepareForTesting($ObjectType, $Field)
    {
        //====================================================================//
        //   Verify Test is Required
        if (!$this->verifyTestIsAllowed($ObjectType, $Field)) {
            return false;
        }
        
        //====================================================================//
        // Prepare Fake Object Data
        //====================================================================//
        
        $this->Fields   =   $this->fakeFieldsList($ObjectType, [$Field->id], true);
        $FakeData       =   $this->fakeObjectData($this->Fields);
        
        return $FakeData;
    }
    
    
    public function verifyResponse($ObjectType, $ObjectId, $Action, $ExpectedData)
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
