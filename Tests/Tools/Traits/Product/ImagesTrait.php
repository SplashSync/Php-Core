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
    private $TargetFields;
    /** @var array */
    private $TargetImages;
    
    /** @var string */
    private $ListId;
    /** @var string */
    private $ImageId;
    /** @var string */
    private $IsCoverId;
    /** @var string */
    private $IsVisibleId;
    /** @var string */
    private $PositionId;


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
        //   Verify Images Fields are Valid
        $this->verifyImagesFields($ObjectType);
        
        //====================================================================//
        //   READ OBJECT DATA
        //====================================================================//

        //====================================================================//
        //   Build List of Fields to Read
        $toRead =   array_merge($this->reduceFieldList($this->Fields), $this->TargetFields);
        
        //====================================================================//
        //   Read Object Data
        $Target    =   Splash::object($ObjectType)->get($ObjectId, $toRead);
        
        //====================================================================//
        //   Verify Images Are Here
        $this->assertNotEmpty($Source[$this->ListId], "Source Product Images List is Empty");
        $this->assertNotEmpty($Target[$this->ListId], "Target Product Images List is Empty");
        $this->SourceImages = $Source[$this->ListId];
        $this->TargetImages = $Target[$this->ListId];

        //====================================================================//
        //   VERIFY TARGET IMAGES
        //====================================================================//
        
        //====================================================================//
        //   Walk on Target Images List
        $Position = -1;
        foreach ($this->TargetImages as $TargetImage) {
            //====================================================================//
            //   Check if Image Data is Set
            $this->assertArrayHasKey($this->ImageId, $TargetImage);
            //====================================================================//
            //   Check if Image Data is Valid
            $Validate = Image::validate($TargetImage[$this->ImageId]);
            $this->assertTrue($Validate, "Target Image définition Array is Invalid " . $Validate);
            //====================================================================//
            //   Check if Image Flags are Set
            $this->assertArrayHasKey($this->IsVisibleId, $TargetImage);
            $this->assertArrayHasKey($this->IsCoverId, $TargetImage);
            $this->assertArrayHasKey($this->PositionId, $TargetImage);
            //====================================================================//
            //   Check if Images Position are Following
            $this->assertGreaterThan(
                $Position,
                $TargetImage[$this->PositionId],
                "Product Images Positions are not Correctly Numbered"
            );
            $Position = $TargetImage[$this->PositionId];
        }
        
        //====================================================================//
        //   SOURCE VS TARGET IMAGES
        //====================================================================//
        
        //====================================================================//
        //   Walk on Source Images List
        foreach ($this->SourceImages as $SrcImage) {
            //====================================================================//
            //   Verify Visible Flag
            $this->verifyVisibleImages($SrcImage, $this->ImageId, $this->IsVisibleId);
            //====================================================================//
            //   Verify Cover Flag
            $this->verifyCoverImages($SrcImage, $this->ImageId, $this->IsCoverId);
        }
    }

    /**
     * @abstract    Verify Images are Correctly Stored
     * @param       string      $ObjectType
     * @return      void
     */
    private function verifyImagesFields($ObjectType)
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
        $this->assertNotEmpty($Image, "Product Images List Field not Found");
        $this->assertNotEmpty($isCover, "Product Images is Cover Field not Found");
        $this->assertNotEmpty($isVisible, "Product Images is Visible Field not Found");
        $this->assertNotEmpty($Position, "Product Images Position Field not Found");
        //====================================================================//
        //   Extract Fields Ids
        $this->ListId         = self::lists()->listName($Image->id);
        $this->ImageId        = self::lists()->fieldName($Image->id);
        $this->IsCoverId      = self::lists()->fieldName($isCover->id);
        $this->IsVisibleId    = self::lists()->fieldName($isVisible->id);
        $this->PositionId     = self::lists()->fieldName($Position->id);
        //====================================================================//
        //   Build To Read Fields Ids
        $this->TargetFields =   [$Image->id, $isCover->id, $isVisible->id, $Position->id];
        //====================================================================//
        //   Check Required Fields
        $this->assertNotEmpty($this->ImageId);
        $this->assertNotEmpty($this->IsCoverId);
        $this->assertNotEmpty($this->IsVisibleId);
        $this->assertNotEmpty($this->PositionId);
    }
    
    /**
     * @abstract    Identify Image in List by Md5 and Active Flag
     * @param   array       $Source         Source Image
     * @param   string      $ImageId        Image Field Id
     * @param   string      $FlagId         Flag Field Id
     * @return  array|null
     */
    protected function findImageByMd5AndFlag($Source, $ImageId, $FlagId)
    {
        //====================================================================//
        //   Check if Image Md5 is Set
        $this->assertNotEmpty(
            $Source[$ImageId]["md5"],
            "Source Image has no Md5... Check input Combinations!"
        );

        //====================================================================//
        //   Walk on Target Images
        foreach ($this->TargetImages as $Image) {
            //====================================================================//
            //   Compare Images Md5
            if ($Image[$ImageId]["md5"] != $Source[$ImageId]["md5"]) {
                continue;
            }
            //====================================================================//
            //   Compare Images Flags
            if (!isset($Image[$FlagId]) || empty($Image[$FlagId])) {
                continue;
            }
            return $Image;
        }
        //====================================================================//
        //   Images not Found
        return null;
    }
    
    /**
     * @abstract    Verify Visible Image Flag
     * @param   array       $Source         Source Image
     * @param   string      $ImageId        Image Field Id
     * @param   string      $isVisibleId    is Visible Flag Field Id
     * @return  void
     */
    private function verifyVisibleImages($Source, $ImageId, $isVisibleId)
    {
        //====================================================================//
        //   Check if Image Visible Flag is Set
        $this->assertArrayHasKey($isVisibleId, $Source);
        if (!$Source[$isVisibleId]) {
            return;
        }
        //====================================================================//
        //   Verify Image is Flagged as Visible
        $this->assertNotEmpty(
            $this->findImageByMd5AndFlag($Source, $ImageId, $isVisibleId),
            "Source Image is NOT flagged as Visible in Target List" . PHP_EOL
                . "Source : " . print_r($Source, true)
                . "Target : " . print_r($this->TargetImages, true)
        );
    }
    
    /**
     * @abstract    Verify Cover Image Flag
     * @param   array       $Source         Source Image
     * @param   string      $ImageId        Image Field Id
     * @param   string      $isCoverId      is Cover Flag Field Id
     */
    private function verifyCoverImages($Source, $ImageId, $isCoverId)
    {
        //====================================================================//
        //   Check if Image Cover Flag is Set
        $this->assertArrayHasKey($isCoverId, $Source);
        if (!$Source[$isCoverId]) {
            return;
        }
        //====================================================================//
        //   Verify Image is Flagged as Cover
        $this->assertNotEmpty(
            $this->findImageByMd5AndFlag($Source, $ImageId, $isCoverId),
            "Source Image is NOT flagged as Cover in Target List" . PHP_EOL
                . "Source : " . print_r($Source, true)
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
            $this->markTestSkipped('No Product Images Combination Found.');
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
        
        //====================================================================//
        //   Advanced Set with Cover not in First Position
        $Combinations[] =   array(
            array(1,false,true,0),
            array(2,true,true,1),
            array(3,false,true,2),
        );
        
        //====================================================================//
        //   Advanced Set with Cover not in First Position & Not Visible
        $Combinations[] =   array(
            array(1,false,true,0),
            array(3,false,true,1),
            array(2,false,true,2),
            array(4,true,false,3),
        );
        
        return new ArrayObject($Combinations, ArrayObject::ARRAY_AS_PROPS);
    }
}
