<?php
namespace Splash\Tests\WsObjects;

use Splash\Tests\Tools\ObjectsCase;
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
    public function testSetSingleFieldFromModule($testSequence, $objectType, $field, $forceObjectId = null)
    {
        //====================================================================//
        //   Load Test Sequence
        $this->loadLocalTestSequence($testSequence);
        
        //====================================================================//
        //   Execute Set Test
        $this->coreTestSetSingleFieldFromModule($objectType, $field, $forceObjectId);
    }
    
    /**
     * @dataProvider objectFieldsProvider
     */
    public function testSetSingleFieldFromService($testSequence, $objectType, $field, $forceObjectId = null)
    {
        //====================================================================//
        //   Load Test Sequence
        $this->loadLocalTestSequence($testSequence);
        
        //====================================================================//
        //   Execute Set Test
        $this->coreTestSetSingleFieldFromService($objectType, $field, $forceObjectId);
    }
}
