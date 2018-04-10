<?php
namespace Splash\Tests\WsObjects;

use Splash\Tests\Tools\ObjectsCase;
use Splash\Client\Splash;
use ArrayObject;

/**
 * @abstract    Objects Test Suite - Objects List Reading Verifications
 *
 * @author SplashSync <contact@splashsync.com>
 */
class O03ListTest extends ObjectsCase
{
    
    
    /**
     * @dataProvider objectTypesProvider
     */
    public function testFromModule($Sequence, $ObjectType)
    {
        $this->loadLocalTestSequence($Sequence);
        
        //====================================================================//
        //   Execute Action Directly on Module
        $Data = Splash::object($ObjectType)->objectsList();
        //====================================================================//
        //   Module May Return an Array (ArrayObject created by WebService)
        if (is_array($Data)) {
            $Data   =   new ArrayObject($Data);
        }
        //====================================================================//
        //   Verify Response
        $this->verifyResponse($Data, $ObjectType);
    }
    
    /**
     * @dataProvider objectTypesProvider
     */
    public function testFromObjectsService($Sequence, $ObjectType)
    {
        $this->loadLocalTestSequence($Sequence);
        
        //====================================================================//
        //   Execute Action From Splash Server to Module
        $Data = $this->GenericAction(SPL_S_OBJECTS, SPL_F_LIST, __METHOD__, [ "id" => null, "type" => $ObjectType]);
        
        //====================================================================//
        //   Verify Response
        $this->verifyResponse($Data, $ObjectType);
    }

    public function testFromObjectsServiceErrors()
    {
        //====================================================================//
        //      Request definition without Sending ObjectType
        $this->GenericErrorAction(SPL_S_OBJECTS, SPL_F_LIST, __METHOD__);
    }
    
    public function verifyResponse($Data, $ObjectType)
    {
        //====================================================================//
        //   Verify Response
        $this->assertNotEmpty($Data, "Objects List is Empty");
        $this->assertInstanceOf("ArrayObject", $Data, "Objects List is Not an ArrayObject");
        
        $this->verifyMetaInformations($Data, $ObjectType);
        $this->verifyAvailableFields($Data, $ObjectType);
    }
    

    public function verifyAvailableFields($Data, $ObjectType)
    {
        //====================================================================//
        // Verify Fields are Available
        $Fields = Splash::object($ObjectType)->fields();
        if (is_null($Fields)) {
            return false;
        }

//        //====================================================================//
//        // Verify List Datas
//        $Object = reset($Data);
        
        //====================================================================//
        // Verify List Data Items
        foreach ($Data as $Item) {
            //====================================================================//
            // Verify Object Id field is available
            $this->assertArrayHasKey(
                "id",
                $Item,
                $ObjectType . " List => Object Identifier (id) is not defined in List."
            );
            $this->assertInternalType(
                "scalar",
                $Item["id"],
                $ObjectType . " List => Object Identifier (id) is not String convertible."
            );

             
            //====================================================================//
            // Verify all "inlist" fields are available
            foreach ($Fields as $Field) {
                if (isset($Field['inlist']) && !empty($Field['inlist'])) {
                    $this->assertArrayHasKey(
                        $Field["id"],
                        $Item,
                        $ObjectType . " List => Field (" . $Field["name"]. ") is marked as 'inlist' but not found."
                    );
                    $this->assertInternalType(
                        "scalar",
                        $Item["id"],
                        $ObjectType . " List => Field (" . $Field["name"]. ") is not String convertible."
                    );
                }
            }
        }
        
        return true;
    }

    public function verifyMetaInformations($Data, $ObjectType)
    {
        //====================================================================//
        // Verify List Meta Are Available
        $this->assertArrayHasKey("meta", $Data, $ObjectType . " List => Meta Informations are not defined");
        $Meta   =   $Data["meta"];
        $this->assertArrayHasKey("current", $Meta, $ObjectType . " List => Meta current value not defined");
        $this->assertArrayHasKey("total", $Meta, $ObjectType . " List => Meta total value are not defined");
        
        if (!empty($Meta["current"]) && !empty($Meta["total"])) {
            //====================================================================//
            // Verify List Meta Format
            $this->assertArrayInternalType(
                $Meta,
                "current",
                "numeric",
                $ObjectType . " List => Current Object Count not an Integer"
            );
            $this->assertArrayInternalType(
                $Meta,
                "total",
                "numeric",
                $ObjectType . " List => Total Object Count not an Integer"
            );
        }
        
        //====================================================================//
        // Verify List Meta Informations
        unset($Data["meta"]);
        $this->assertEquals(
            $Meta["current"],
            count($Data),
            $ObjectType . " List => Current Object Count is different from Given Meta['current'] count."
        );
    }
}
