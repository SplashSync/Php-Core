<?php
namespace Splash\Tests\WsObjects;

use Splash\Tests\Tools\ObjectsCase;
use Splash\Client\Splash;
use Splash\Tests\Tools\Fields\Ooobjectid;
use Splash\Tests\Tools\Traits\ObjectsSetTestsTrait;

/**
 * @abstract    Objects Test Suite - Verify Read/Write of any R/W fields is Ok.
 *
 * @author SplashSync <contact@splashsync.com>
 */
class O06SetTest extends ObjectsCase
{
    use ObjectsSetTestsTrait;
       
    /**
     * @dataProvider objectFieldsProvider
     */
    public function testSetSingleFieldFromModule($Sequence, $ObjectType, $Field, $ForceObjectId = null)
    {
        //====================================================================//
        //   Load Test Sequence
        $this->loadLocalTestSequence($Sequence);
        
        //====================================================================//
        //   Execute Set Test
        $this->coreTestSetSingleFieldFromModule($ObjectType, $Field, $ForceObjectId);
    }
    
    /**
     * @dataProvider objectFieldsProvider
     */
    public function testSetSingleFieldFromService($Sequence, $ObjectType, $Field, $ForceObjectId = null)
    {
        //====================================================================//
        //   Load Test Sequence
        $this->loadLocalTestSequence($Sequence);
        
        //====================================================================//
        //   Execute Set Test
        $this->coreTestSetSingleFieldFromService($ObjectType, $Field, $ForceObjectId);
    }
}
