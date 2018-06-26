<?php

namespace Splash\Tests\Tools\Traits\Product;

use ArrayObject;

use Splash\Client\Splash;
use Splash\Tests\Tools\Fields\Ooimage as Image;

/**
 * @abstract    Splash Test Tools - Products Images PhpUnit Specific Features
 *
 * @author SplashSync <contact@splashsync.com>
 */
trait ImagesTrait
{
    /** @var array */
    private $SourceImages;
    /** @var array */
    private $TargetImages;


    //==============================================================================
    //      SPLASH PRODUCT IMAGES SPECIFIC FUNCTIONS
    //==============================================================================

    /**
     * @abstract    Base Test for Products Images Writing
     */
    protected function coreTestImagesFromModule($Sequence, $ObjectType, $Images)
    {
        //====================================================================//
        //   TEST INIT
        //====================================================================//
        if (is_null($Sequence)) {
            $this->assertTrue(true);
            return;
        }
        $this->loadLocalTestSequence($Sequence);

        //====================================================================//
        //   OBJECT CREATE TEST
        //====================================================================//
        
        //====================================================================//
        //   Store Current Tested Images
        $this->CurrentImages    =   $Images;
            
        //====================================================================//
        //   Generate Dummy Object Data (Required Fields Only)
        $NewData = $this->prepareForTesting($ObjectType);
        if ($NewData == false) {
            return true;
        }
        
        //====================================================================//
        //   Execute Create Test
        $ObjectId = $this->setObjectFromModule($ObjectType, $NewData);

        //====================================================================//
        //   VERIFY IMAGES OBJECT DATA
        //====================================================================//
        
        //====================================================================//
        //   Verify Visivble Images
        $this->verifyImages($ObjectType, $ObjectId, $NewData);
        
        //====================================================================//
        //   OBJECT DELETE
        //====================================================================//
        
        //====================================================================//
        //   Delete Object From Module
        $this->deleteObjectFromModule($ObjectType, $ObjectId);
    }
    
    /**
     * @abstract    Generate Image Item
     */
    private function getFakeImageItem($ImageIndex, $setCover, $setVisible, $setPosition)
    {
        //====================================================================//
        //   Load Required Fields
        $Image      =   self::findFieldByTag($this->Fields, "http://schema.org/Product", "image");
        $isCover    =   self::findFieldByTag($this->Fields, "http://schema.org/Product", "isCover");
        $isVisible  =   self::findFieldByTag($this->Fields, "http://schema.org/Product", "isVisibleImage");
        $Position   =   self::findFieldByTag($this->Fields, "http://schema.org/Product", "positionImage");
        
        //====================================================================//
        //   Generate Random Attributes Set
        $SpashImage =   Image::fake(["Images" => [ "fake-image" . $ImageIndex . ".jpg"]]);
        $Item   =   array(
            self::lists()->fieldName($Image->id)    =>      $SpashImage,
            self::lists()->fieldName($isCover->id)  =>      $setCover,
        );
        if ($isVisible->write) {
            $Item[self::lists()->fieldName($isVisible->id)]   =   $setVisible;
        }
        if ($Position->write) {
            $Item[self::lists()->fieldName($Position->id)]  =   $setPosition;
        }
        return $Item;
    }

    /**
     * @abstract    Generate Fake Images List
     */
    private function getFakeImages($Combination)
    {
        //====================================================================//
        //   Load Required Fields
        $Image      =   self::findFieldByTag($this->Fields, "http://schema.org/Product", "image");
        //====================================================================//
        //   Check Required Fields
        $this->assertNotEmpty($Image);
        //====================================================================//
        //   Build Images List
        $Images = array();
        foreach ($Combination as $Item) {
            $Images[]   =   $this->getFakeImageItem($Item[0], $Item[1], $Item[2], $Item[3]);
        }
        
        return array(self::lists()->listName($Image->id) => $Images);
    }

    /**
     * @abstract    Verify Images are Correctly Stored
     * @param   string      $ObjectType
     * @param   string      $ObjectId
     * @param   array       $Source
     */
    private function verifyImages($ObjectType, $ObjectId, $Source)
    {
        //====================================================================//
        //   Load Fields
        $this->Fields   =   Splash::object($ObjectType)->fields();
        $this->assertNotEmpty($this->Fields, "Product Fields List is Empty!");
        //====================================================================//
        //   Load Required Fields
        $Image      =   self::findFieldByTag($this->Fields, "http://schema.org/Product", "image");
        $isCover    =   self::findFieldByTag($this->Fields, "http://schema.org/Product", "isCover");
        $isVisible  =   self::findFieldByTag($this->Fields, "http://schema.org/Product", "isVisibleImage");
        $Position   =   self::findFieldByTag($this->Fields, "http://schema.org/Product", "positionImage");
        //====================================================================//
        //   Check Required Fields
        $this->assertNotEmpty($Image);
        $this->assertNotEmpty($isCover);
        $this->assertNotEmpty($isVisible);
        $this->assertNotEmpty($Position);
        //====================================================================//
        //   Extract Fields Ids
        $ListId         = self::lists()->listName($Image->id);
        $ImageId        = self::lists()->fieldName($Image->id);
        $isCoverId      = self::lists()->fieldName($isCover->id);
        $isVisibleId    = self::lists()->fieldName($isVisible->id);
        
        //====================================================================//
        //   READ OBJECT DATA
        //====================================================================//

        //====================================================================//
        //   Build List of Fields to Read
        $toRead =   array_merge(
            $this->reduceFieldList($this->Fields),
            [$Image->id, $isCover->id, $isVisible->id, $Position->id]
        );
        
        //====================================================================//
        //   Read Object Data
        $Target    =   Splash::object($ObjectType)->get($ObjectId, $toRead);
        
        //====================================================================//
        //   Verify Images Are Here
        $this->assertNotEmpty($Source[$ListId], "Source Product Images List is Empty");
        $this->assertNotEmpty($Target[$ListId], "Target Product Images List is Empty");
        $this->SourceImages = $Source[$ListId];
        $this->TargetImages = $Target[$ListId];
        //====================================================================//
        //   Walk on Source Images List
        foreach ($this->SourceImages as $SrcImage) {
            //====================================================================//
            //   Find Image in List
            $TagetImage =   $this->findImageItembyMd5($ImageId, $SrcImage[$ImageId]["md5"]);
            //====================================================================//
            //   Verify Visible Flag
            $this->verifyVisibleImages($SrcImage, $TagetImage, $isVisibleId);
            //====================================================================//
            //   Verify Cover Flag
            $this->verifyCoverImages($SrcImage, $TagetImage, $isCoverId);
        }
    }
    
    /**
     * @abstract    Identify Image in List by Md5
     * @param   string  $ImageId
     * @param   string  $Md5
     * @return  array|null
     */
    protected function findImageItembyMd5($ImageId, $Md5)
    {
        foreach ($this->TargetImages as $Image) {
            if ($Image[$ImageId]["md5"] == $Md5) {
                return $Image;
            }
        }
        //====================================================================//
        //   Verify Image was Found
        $this->assertNotNull(
            null,
            "Source Image " . $Md5 . " was not found in Target List" . PHP_EOL
                . "Source : " . print_r($this->SourceImages, true)
                . "Target : " . print_r($this->TargetImages, true)
        );
        return null;
    }
    
    /**
     * @abstract    Verify Visible Image Flag
     * @param   array       $Source         Source Image
     * @param   array       $Target         Target Image
     * @param   string      $isVisibleId    is Visible Flag Field Id
     */
    private function verifyVisibleImages($Source, $Target, $isVisibleId)
    {
        //====================================================================//
        //   Check if Image Visible Flag is Set
        if (!isset($Source[$isVisibleId]) || !$Source[$isVisibleId]) {
            return;
        }
        //====================================================================//
        //   Verify Image is Flagged as Visible
        $this->assertNotEmpty(
            $Target[$isVisibleId],
            "Source Image is NOT flagged as Visible in Target List" . PHP_EOL
                . "Source : " . print_r($this->SourceImages, true)
                . "Target : " . print_r($this->TargetImages, true)
        );
    }
    
    /**
     * @abstract    Verify Cover Image Flag
     * @param   array       $Source         Source Image
     * @param   array       $Target         Target Image
     * @param   string      $isCoverId      is Cover Flag Field Id
     */
    private function verifyCoverImages($Source, $Target, $isCoverId)
    {
        //====================================================================//
        //   Check if Image Cover Flag is Set
        if (!isset($Source[$isCoverId]) || !$Source[$isCoverId]) {
            return;
        }
        //====================================================================//
        //   Verify Image is Flagged as Cover
        $this->assertNotEmpty(
            $Target[$isCoverId],
            "Source Image is NOT flagged as Cover in Target List" . PHP_EOL
                . "Source : " . print_r($this->SourceImages, true)
                . "Target : " . print_r($this->TargetImages, true)
        );
    }
    
    /**
     * @abstract     Check if Product Images Tests is Required
     */
    private function isAllowedProductImagesTests($ObjectType)
    {
        //====================================================================//
        //   Check Object Type
        if (!$this->assertIsProductType($ObjectType)) {
            return false;
        }
        //====================================================================//
        //   Load Fields
        $this->Fields   =   Splash::object($ObjectType)->fields();
        $this->assertNotEmpty($this->Fields, "Product Fields List is Empty!");
        //====================================================================//
        //   Verify Product has Writable Images List
        $ImageList    =   self::findFieldByTag($this->Fields, "http://schema.org/Product", "image");
        if (!$ImageList || !$ImageList->write) {
            return false;
        }
        return true;
    }
    
    /**
     * @abstract     Provide Products Images Tests Combinations
     */
    public function productImagesProvider()
    {
        $Result = array();
        //====================================================================//
        //   For Each Object Types
        foreach ($this->objectTypesProvider() as $TestCase) {
            //====================================================================//
            //   Setup Sequence
            $this->loadLocalTestSequence($TestCase[0]);
            //====================================================================//
            //   Check Test is Allowed
            if (!$this->isAllowedProductImagesTests($TestCase[1])) {
                continue;
            }
            //====================================================================//
            //   For Each Object Types
            foreach ($this->getProductImagesSequences() as $ImagesSequence) {
                $DataSet    = $TestCase;
                $DataSet[3] = $this->getFakeImages($ImagesSequence);
            }
            $Result[]   =   $DataSet;
        }
        if (empty($Result)) {
            return [null, null, null, null];
        }
        return $Result;
    }

    /**
     * @abstract     Provide Products Images Combinations to Test
     */
    public function getProductImagesSequences()
    {
        $Combinations   =   array();
        
        //====================================================================//
        //   Images Sets Definitions
        //====================================================================//
        //   $ImageIndex, $setCover, $setVisible, $setPosition
        //====================================================================//
        
        //====================================================================//
        //   Basic Set
        $Combinations[] =   array(
            array(1,true,true,0),
        );
        
        //====================================================================//
        //   Basic Set
        $Combinations[] =   array(
            array(1,true,true,0),
            array(2,false,true,1),
            array(3,false,true,2),
        );
        
        //====================================================================//
        //   Basic Set with Hidden Images
        $Combinations[] =   array(
            array(1,true,true,0),
            array(2,false,false,1),
            array(3,false,true,2),
        );
        
//        //====================================================================//
//        //   Advanced Set with Cover not in First Position
//        $Combinations[] =   array(
//            array(1,false,true,0),
//            array(2,true,true,1),
//            array(3,false,true,2),
//        );
        
        return new ArrayObject($Combinations, ArrayObject::ARRAY_AS_PROPS);
    }
}
