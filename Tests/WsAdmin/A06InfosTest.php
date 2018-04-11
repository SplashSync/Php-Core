<?php
namespace Splash\Tests\WsAdmin;

use ArrayObject;

use Splash\Client\Splash;
use Splash\Tests\Tools\AbstractBaseCase;

/**
 * @abstract    Admin Test Suite - Server Infos Client Verifications
 *
 * @author SplashSync <contact@splashsync.com>
 */
class A06InfosTest extends AbstractBaseCase
{
    public function testFromClass()
    {
        //====================================================================//
        //   Execute Action From Module
        $Data = Splash::informations();
        
        //====================================================================//
        //   Module May Return an Array (ArrayObject created by WebService)
        if (is_array($Data)) {
            $Data   =   new ArrayObject($Data);
        }
        //====================================================================//
        //   Verify Response
        $this->verifyResponse($Data);
    }

    
    public function testFromAdmin()
    {
        
        //====================================================================//
        //   Execute Action From Splash Server to Module
        $Data = $this->genericAction(SPL_S_ADMIN, SPL_F_GET_INFOS, __METHOD__);
        
        //====================================================================//
        //   Verify Response
        $this->verifyResponse($Data);
    }
    
    
    public function verifyResponse($Data)
    {
        
        //====================================================================//
        //   Verify Main Informations
        $this->assertArrayHasKey("shortdesc", $Data, "Server Short Description is Missing");
        $this->assertArrayHasKey("longdesc", $Data, "Server Long Description is Missing");
        
        //====================================================================//
        //   Verify Main Informations
        $this->assertArrayInternalType($Data, "shortdesc", "string", "Server Short Description");
        $this->assertArrayInternalType($Data, "longdesc", "string", "Server Long Description");
        $this->assertArrayInternalType($Data, "servertype", "string", "Server Type Name");
        $this->assertArrayInternalType($Data, "serverurl", "string", "Server Url");
        $this->assertArrayInternalType($Data, "moduleauthor", "string", "Module Author");
        $this->assertArrayInternalType($Data, "moduleversion", "string", "Module Version");
        
        //====================================================================//
        //   Verify Local Informations
        $this->assertArrayInternalType($Data, "company", "string", "Local Informations");
        $this->assertArrayInternalType($Data, "address", "string", "Local Informations");
        $this->assertArrayInternalType($Data, "zip", "string", "Local Informations");
        $this->assertArrayInternalType($Data, "town", "string", "Local Informations");
        $this->assertArrayInternalType($Data, "www", "string", "Local Informations");
        $this->assertArrayInternalType($Data, "email", "string", "Local Informations");
        $this->assertArrayInternalType($Data, "phone", "string", "Local Informations");

        //====================================================================//
        //   Verify Server Icon
        $FilesTooltip  =    "Set it by using Splash::File()->ReadFileContents(\"/path\my\icon.ico\")";
        $this->assertArrayInternalType($Data, "icoraw", "string", "Raw Ico is Missing. " . $FilesTooltip);
        
        $this->assertTrue(
            !empty($Data["logourl"]) || !empty($Data["logoraw"]),
            "You must provide a logo for your module. "
                . "Pass an image url on 'logourl' or a ra logo contents on 'logoraw' information."
        );
        
        if (!empty($Data["logourl"])) {
            $this->assertArrayInternalType($Data, "logourl", "string", "Module Logo Url is not a string.");
        }
        if (!empty($Data["logoraw"])) {
            $this->assertArrayInternalType(
                $Data,
                "logoraw",
                "string",
                "Module Logo Raw is not a string. " . $FilesTooltip
            );
        }
    }
}
