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
        $data = Splash::informations();
        //====================================================================//
        //   Verify Response
        $this->verifyResponse($data);
    }

    
    public function testFromAdmin()
    {
        //====================================================================//
        //   Execute Action From Splash Server to Module
        $data = $this->genericAction(SPL_S_ADMIN, SPL_F_GET_INFOS, __METHOD__);
        //====================================================================//
        //   Verify Response
        $this->verifyResponse($data);
    }
    
    
    public function verifyResponse($data)
    {
        
        //====================================================================//
        //   Verify Main Informations
        $this->assertArrayHasKey("shortdesc", $data, "Server Short Description is Missing");
        $this->assertArrayHasKey("longdesc", $data, "Server Long Description is Missing");
        
        //====================================================================//
        //   Verify Main Informations
        $this->assertArrayInternalType($data, "shortdesc", "string", "Server Short Description");
        $this->assertArrayInternalType($data, "longdesc", "string", "Server Long Description");
        $this->assertArrayInternalType($data, "servertype", "string", "Server Type Name");
        $this->assertArrayInternalType($data, "serverurl", "string", "Server Url");
        $this->assertArrayInternalType($data, "moduleauthor", "string", "Module Author");
        $this->assertArrayInternalType($data, "moduleversion", "string", "Module Version");
        
        //====================================================================//
        //   Verify Local Informations
        $this->assertArrayInternalType($data, "company", "string", "Local Informations");
        $this->assertArrayInternalType($data, "address", "string", "Local Informations");
        $this->assertArrayInternalType($data, "zip", "string", "Local Informations");
        $this->assertArrayInternalType($data, "town", "string", "Local Informations");
        $this->assertArrayInternalType($data, "www", "string", "Local Informations");
        $this->assertArrayInternalType($data, "email", "string", "Local Informations");
        $this->assertArrayInternalType($data, "phone", "string", "Local Informations");

        //====================================================================//
        //   Verify Server Icon
        $tooltip  =    "Set it by using Splash::File()->ReadFileContents(\"/path\my\icon.ico\")";
        $this->assertArrayInternalType($data, "icoraw", "string", "Raw Ico is Missing. " . $tooltip);
        
        $this->assertTrue(
            !empty($data["logourl"]) || !empty($data["logoraw"]),
            "You must provide a logo for your module. "
                . "Pass an image url on 'logourl' or a ra logo contents on 'logoraw' information."
        );
        
        if (!empty($data["logourl"])) {
            $this->assertArrayInternalType($data, "logourl", "string", "Module Logo Url is not a string.");
        }
        if (!empty($data["logoraw"])) {
            $this->assertArrayInternalType(
                $data,
                "logoraw",
                "string",
                "Module Logo Raw is not a string. " . $tooltip
            );
        }
    }
}
