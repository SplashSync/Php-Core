<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2021 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

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
    private $sourceImages;
    /** @var array */
    private $targetFields;
    /** @var array */
    private $targetImages;

    /** @var false|string */
    private $listId;
    /** @var false|string */
    private $imageId;
    /** @var false|string */
    private $isCoverId;
    /** @var false|string */
    private $isVisibleId;
    /** @var false|string */
    private $positionId;

    /**
     * Provide Products Images Tests Combinations
     *
     * @return array
     */
    public function productImagesProvider()
    {
        $result = array();
        //====================================================================//
        //   For Each Object Types
        foreach ($this->objectTypesProvider() as $testCase) {
            //====================================================================//
            //   Setup Sequence
            $this->loadLocalTestSequence($testCase[0]);
            //====================================================================//
            //   Check Test is Allowed
            if (!$this->isAllowedProductImagesTests($testCase[1])) {
                continue;
            }
            //====================================================================//
            //   For Each Object Types
            foreach ($this->getProductImagesSequences() as $imgSequence) {
                $dataSet = $testCase;
                $dataSet[3] = $this->getFakeImages($imgSequence);
                $result[] = $dataSet;
            }
//            $Result[]   =   $DataSet;
        }
        if (empty($result)) {
            $this->markTestSkipped('No Product Images Combination Found.');
        }

        return $result;
    }

    /**
     * Provide Products Images Combinations to Test
     *
     * @return ArrayObject
     */
    public function getProductImagesSequences()
    {
        $combinations = array();

        //====================================================================//
        //   Images Sets Definitions
        //====================================================================//
        //   $ImageIndex, $setCover, $setVisible, $setPosition
        //====================================================================//

        //====================================================================//
        //   Basic Set
        $combinations[] = array(
            array(1,true,true,0),
        );

        //====================================================================//
        //   Basic Set
        $combinations[] = array(
            array(1,true,true,0),
            array(2,false,true,1),
            array(3,false,true,2),
        );

        //====================================================================//
        //   Basic Set with Hidden Images
        $combinations[] = array(
            array(1,true,true,0),
            array(2,false,false,1),
            array(3,false,true,2),
        );

        //====================================================================//
        //   Advanced Set with Cover not in First Position
        $combinations[] = array(
            array(1,false,true,0),
            array(2,true,true,1),
            array(3,false,true,2),
        );

        //====================================================================//
        //   Advanced Set with Cover not in First Position & Not Visible
        $combinations[] = array(
            array(1,false,true,0),
            array(3,false,true,1),
            array(2,false,true,2),
            array(4,true,false,3),
        );

        return new ArrayObject($combinations, ArrayObject::ARRAY_AS_PROPS);
    }

    //==============================================================================
    //      SPLASH PRODUCT IMAGES SPECIFIC FUNCTIONS
    //==============================================================================

    /**
     * Base Test for Products Images Writing
     *
     * @param null|string $testSequence
     * @param string      $objectType
     * @param mixed       $images
     *
     * @return void
     */
    protected function coreTestImagesFromModule($testSequence, $objectType, $images)
    {
        //====================================================================//
        //   TEST INIT
        //====================================================================//
        if (is_null($testSequence)) {
            $this->assertTrue(true);

            return;
        }
        $this->loadLocalTestSequence($testSequence);

        //====================================================================//
        //   OBJECT CREATE TEST
        //====================================================================//

        //====================================================================//
        //   Store Current Tested Images
        $this->currentImages = $images;

        //====================================================================//
        //   Generate Dummy Object Data (Required Fields Only)
        $newData = $this->prepareForTesting($objectType);
        if (false == $newData) {
            return;
        }

        //====================================================================//
        //   Execute Create Test
        $objectId = $this->setObjectFromModule($objectType, $newData);

        //====================================================================//
        //   VERIFY IMAGES OBJECT DATA
        //====================================================================//

        //====================================================================//
        //   Verify Visivble Images
        $this->verifyImages($objectType, $objectId, $newData);

        //====================================================================//
        //   OBJECT DELETE
        //====================================================================//

        //====================================================================//
        //   Delete Object From Module
        $this->deleteObjectFromModule($objectType, $objectId);
    }

    /**
     * Identify Image in List by Md5 and Active Flag
     *
     * @param array  $source  Source Image
     * @param string $imageId Image Field Id
     * @param string $flagId  Flag Field Id
     *
     * @return null|array
     */
    protected function findImageByMd5AndFlag($source, $imageId, $flagId)
    {
        //====================================================================//
        //   Check if Image Md5 is Set
        $this->assertNotEmpty(
            $source[$imageId]["md5"],
            "Source Image has no Md5... Check input Combinations!"
        );

        //====================================================================//
        //   Walk on Target Images
        foreach ($this->targetImages as $image) {
            //====================================================================//
            //   Compare Images Md5
            if ($image[$imageId]["md5"] != $source[$imageId]["md5"]) {
                continue;
            }
            //====================================================================//
            //   Compare Images Flags
            if (!isset($image[$flagId]) || empty($image[$flagId])) {
                continue;
            }

            return $image;
        }
        //====================================================================//
        //   Images not Found
        return null;
    }

    /**
     * Generate Image Item
     *
     * @param mixed $index
     * @param mixed $setCover
     * @param mixed $setVisible
     * @param mixed $setPosition
     *
     * @return array
     */
    private function getFakeImageItem($index, $setCover, $setVisible, $setPosition)
    {
        //====================================================================//
        //   Load Required Fields
        $image = self::findFieldByTag($this->fields, "http://schema.org/Product", "image");
        $isCover = self::findFieldByTag($this->fields, "http://schema.org/Product", "isCover");
        $isVisible = self::findFieldByTag($this->fields, "http://schema.org/Product", "isVisibleImage");
        $position = self::findFieldByTag($this->fields, "http://schema.org/Product", "positionImage");
        if (is_null($image) || is_null($isCover) || is_null($isVisible) || is_null($position)) {
            return array();
        }

        //====================================================================//
        //   Generate Random Attributes Set
        $spashImage = Image::fake(array("Images" => array( "fake-image".$index.".jpg")));
        $item = array(
            self::lists()->fieldName($image->id) => $spashImage,
            self::lists()->fieldName($isCover->id) => $setCover,
        );
        if ($isVisible->write) {
            $item[self::lists()->fieldName($isVisible->id)] = $setVisible;
        }
        if ($position->write) {
            $item[self::lists()->fieldName($position->id)] = $setPosition;
        }

        return $item;
    }

    /**
     * Generate Fake Images List
     *
     * @param mixed $combination
     *
     * @return array
     */
    private function getFakeImages($combination)
    {
        //====================================================================//
        //   Load Required Fields
        $image = self::findFieldByTag($this->fields, "http://schema.org/Product", "image");
        //====================================================================//
        //   Check Required Fields
        $this->assertNotEmpty($image);
        if (is_null($image)) {
            return array();
        }
        //====================================================================//
        //   Build Images List
        $images = array();
        foreach ($combination as $item) {
            $images[] = $this->getFakeImageItem($item[0], $item[1], $item[2], $item[3]);
        }

        return array(self::lists()->listName($image->id) => $images);
    }

    /**
     * Verify Images are Correctly Stored
     *
     * @param string $objectType
     * @param string $objectId
     * @param array  $source
     *
     * @return void
     */
    private function verifyImages($objectType, $objectId, $source)
    {
        //====================================================================//
        //   Verify Images Fields are Valid
        $this->verifyImagesFields($objectType);

        //====================================================================//
        //   READ OBJECT DATA
        //====================================================================//

        //====================================================================//
        //   Build List of Fields to Read
        $toRead = array_merge($this->reduceFieldList($this->fields), $this->targetFields);

        //====================================================================//
        //   Read Object Data
        $target = Splash::object($objectType)->get($objectId, $toRead);

        //====================================================================//
        //   Verify Images Are Here
        $this->assertNotEmpty($source[$this->listId], "Source Product Images List is Empty");
        $this->assertIsArray($target);
        $this->assertArrayHasKey((string) $this->listId, $target);
        $this->assertNotEmpty($target[$this->listId], "Target Product Images List is Empty");
        $this->sourceImages = $source[$this->listId];
        $this->targetImages = $target[$this->listId];

        //====================================================================//
        //   VERIFY TARGET IMAGES
        //====================================================================//

        //====================================================================//
        //   Walk on Target Images List
        $position = -1;
        foreach ($this->targetImages as $targetImage) {
            //====================================================================//
            //   Check if Image Data is Set
            $this->assertArrayHasKey((string) $this->imageId, $targetImage);
            //====================================================================//
            //   Check if Image Data is Valid
            $validate = Image::validate($targetImage[$this->imageId]);
            $this->assertTrue($validate, "Target Image définition Array is Invalid ".$validate);
            //====================================================================//
            //   Check if Image Flags are Set
            $this->assertArrayHasKey((string) $this->isVisibleId, $targetImage);
            $this->assertArrayHasKey((string) $this->isCoverId, $targetImage);
            $this->assertArrayHasKey((string) $this->positionId, $targetImage);
            //====================================================================//
            //   Check if Images Position are Following
            $this->assertGreaterThan(
                $position,
                $targetImage[$this->positionId],
                "Product Images Positions are not Correctly Numbered"
            );
            $position = $targetImage[$this->positionId];
        }

        //====================================================================//
        //   SOURCE VS TARGET IMAGES
        //====================================================================//

        //====================================================================//
        //   Walk on Source Images List
        foreach ($this->sourceImages as $srcImage) {
            //====================================================================//
            //   Verify Visible Flag
            $this->verifyVisibleImages($srcImage, (string) $this->imageId, (string) $this->isVisibleId);
            //====================================================================//
            //   Verify Cover Flag
            if (!empty($source[$this->isVisibleId])) {
                $this->verifyCoverImages($srcImage, (string) $this->imageId, (string) $this->isCoverId);
            }
        }
    }

    /**
     * Verify Images are Correctly Stored
     *
     * @param string $objectType
     *
     * @return void
     */
    private function verifyImagesFields($objectType)
    {
        //====================================================================//
        //   Load Fields
        $this->fields = Splash::object($objectType)->fields();
        $this->assertNotEmpty($this->fields, "Product Fields List is Empty!");
        //====================================================================//
        //   Load Required Fields
        $image = self::findFieldByTag($this->fields, "http://schema.org/Product", "image");
        $isCover = self::findFieldByTag($this->fields, "http://schema.org/Product", "isCover");
        $isVisible = self::findFieldByTag($this->fields, "http://schema.org/Product", "isVisibleImage");
        $position = self::findFieldByTag($this->fields, "http://schema.org/Product", "positionImage");
        if (is_null($image) || is_null($isCover) || is_null($isVisible) || is_null($position)) {
            return;
        }
        //====================================================================//
        //   Check Required Fields
        $this->assertNotEmpty($image, "Product Images List Field not Found");
        $this->assertNotEmpty($isCover, "Product Images is Cover Field not Found");
        $this->assertNotEmpty($isVisible, "Product Images is Visible Field not Found");
        $this->assertNotEmpty($position, "Product Images Position Field not Found");
        //====================================================================//
        //   Extract Fields Ids
        $this->listId = self::lists()->listName($image->id);
        $this->imageId = self::lists()->fieldName($image->id);
        $this->isCoverId = self::lists()->fieldName($isCover->id);
        $this->isVisibleId = self::lists()->fieldName($isVisible->id);
        $this->positionId = self::lists()->fieldName($position->id);
        //====================================================================//
        //   Build To Read Fields Ids
        $this->targetFields = array($image->id, $isCover->id, $isVisible->id, $position->id);
        //====================================================================//
        //   Check Required Fields
        $this->assertNotEmpty($this->imageId);
        $this->assertNotEmpty($this->isCoverId);
        $this->assertNotEmpty($this->isVisibleId);
        $this->assertNotEmpty($this->positionId);
    }

    /**
     * Verify Visible Image Flag
     *
     * @param array  $source      Source Image
     * @param string $imageId     Image Field Id
     * @param string $isVisibleId is Visible Flag Field Id
     *
     * @return void
     */
    private function verifyVisibleImages($source, $imageId, $isVisibleId)
    {
        //====================================================================//
        //   Check if Image Visible Flag is Set
        $this->assertArrayHasKey($isVisibleId, $source);
        if (!$source[$isVisibleId]) {
            return;
        }
        //====================================================================//
        //   Verify Image is Flagged as Visible
        $this->assertNotEmpty(
            $this->findImageByMd5AndFlag($source, $imageId, $isVisibleId),
            "Source Image is NOT flagged as Visible in Target List".PHP_EOL
                ."Source : ".print_r($source, true)
                ."Target : ".print_r($this->targetImages, true)
        );
    }

    /**
     * Verify Cover Image Flag
     *
     * @param array  $source    Source Image
     * @param string $imageId   Image Field Id
     * @param string $isCoverId is Cover Flag Field Id
     *
     * @return void
     */
    private function verifyCoverImages($source, $imageId, $isCoverId)
    {
        //====================================================================//
        //   Check if Image Cover Flag is Set
        $this->assertArrayHasKey($isCoverId, $source);
        if (!$source[$isCoverId]) {
            return;
        }
        //====================================================================//
        //   Verify Image is Flagged as Cover
        $this->assertNotEmpty(
            $this->findImageByMd5AndFlag($source, $imageId, $isCoverId),
            "Source Image is NOT flagged as Cover in Target List".PHP_EOL
                ."Source : ".print_r($source, true)
                ."Target : ".print_r($this->targetImages, true)
        );
    }

    /**
     * Check if Product Images Tests is Required
     *
     * @param mixed $objectType
     *
     * @return bool
     */
    private function isAllowedProductImagesTests($objectType)
    {
        //====================================================================//
        //   Check Object Type
        if (!$this->assertIsProductType($objectType)) {
            return false;
        }
        //====================================================================//
        //   Load Fields
        $this->fields = Splash::object($objectType)->fields();
        $this->assertNotEmpty($this->fields, "Product Fields List is Empty!");
        //====================================================================//
        //   Verify Product has Writable Images List
        $imgList = self::findFieldByTag($this->fields, "http://schema.org/Product", "image");
        if (!$imgList || !$imgList->write) {
            return false;
        }

        return true;
    }
}
