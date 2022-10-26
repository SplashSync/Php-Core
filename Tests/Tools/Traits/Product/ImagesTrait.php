<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Tests\Tools\Traits\Product;

use Exception;
use Splash\Client\Splash;
use Splash\Tests\Tools\Fields\OoImage as Image;

/**
 * Splash Test Tools - Products Images PhpUnit Specific Features
 */
trait ImagesTrait
{
    /**
     * @var array
     */
    private array $targetFields;

    /**
     * @var array
     */
    private array $targetImages;

    /**
     * @var null|string
     */
    private ?string $listId;

    /**
     * @var null|string
     */
    private ?string $imageId;

    /**
     * @var null|string
     */
    private ?string $isCoverId;

    /**
     * @var null|string
     */
    private ?string $isVisibleId;

    /**
     * @var null|string
     */
    private ?string $positionId;

    /**
     * Provide Products Images Tests Combinations
     *
     * @throws Exception
     *
     * @return array
     */
    public function productImagesProvider(): array
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
        }
        if (empty($result)) {
            $this->markTestSkipped('No Product Images Combination Found.');
        }

        return $result;
    }

    /**
     * Provide Products Images Combinations to Test
     *
     * @return array[]
     */
    public function getProductImagesSequences(): array
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

        return $combinations;
    }

    //==============================================================================
    //      SPLASH PRODUCT IMAGES SPECIFIC FUNCTIONS
    //==============================================================================

    /**
     * Base Test for Products Images Writing
     *
     * @param null|string $testSequence
     * @param string      $objectType
     * @param array[]     $images
     *
     * @throws Exception
     *
     * @return void
     */
    protected function coreTestImagesFromModule(?string $testSequence, string $objectType, array $images): void
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
        //   Verify Visible Images
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
    protected function findImageByMd5AndFlag(array $source, string $imageId, string $flagId): ?array
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
            if (empty($image[$flagId])) {
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
     * @param string $index
     * @param bool   $setCover
     * @param bool   $setVisible
     * @param int    $setPosition
     *
     * @return array
     */
    private function getFakeImageItem(string $index, bool $setCover, bool $setVisible, int $setPosition): array
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
        $splashImage = Image::fake(array("Images" => array( "fake-image".$index.".jpg")));
        $item = array(
            self::lists()->fieldName($image['id']) => $splashImage,
            self::lists()->fieldName($isCover['id']) => $setCover,
        );
        if ($isVisible['write']) {
            $item[self::lists()->fieldName($isVisible['id'])] = $setVisible;
        }
        if ($position['write']) {
            $item[self::lists()->fieldName($position['id'])] = $setPosition;
        }

        return $item;
    }

    /**
     * Generate Fake Images List
     *
     * @param array[] $combination
     *
     * @return array
     */
    private function getFakeImages(array $combination): array
    {
        //====================================================================//
        //   Load Required Fields
        $image = self::findFieldByTag($this->fields, "http://schema.org/Product", "image");
        //====================================================================//
        //   Check Required Fields
        $this->assertNotEmpty($image);
        //====================================================================//
        //   Build Images List
        $images = array();
        foreach ($combination as $item) {
            $images[] = $this->getFakeImageItem($item[0], $item[1], $item[2], $item[3]);
        }

        return array(self::lists()->listName($image['id']) => $images);
    }

    /**
     * Verify Images are Correctly Stored
     *
     * @param string $objectType
     * @param string $objectId
     * @param array  $source
     *
     * @throws Exception
     *
     * @return void
     */
    private function verifyImages(string $objectType, string $objectId, array $source): void
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
        $this->assertIsArray($target[$this->listId], "Target Product Images List is NOT an Array");
        $sourceImages = $source[$this->listId];
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
            $this->assertNull($validate, "Target Image définition Array is Invalid ".$validate);
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
        foreach ($sourceImages as $srcImage) {
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
     * @throws Exception
     *
     * @return void
     */
    private function verifyImagesFields(string $objectType): void
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
        $this->listId = self::lists()->listName($image['id']);
        $this->imageId = self::lists()->fieldName($image['id']);
        $this->isCoverId = self::lists()->fieldName($isCover['id']);
        $this->isVisibleId = self::lists()->fieldName($isVisible['id']);
        $this->positionId = self::lists()->fieldName($position['id']);
        //====================================================================//
        //   Build To Read Fields Ids
        $this->targetFields = array($image['id'], $isCover['id'], $isVisible['id'], $position['id']);
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
    private function verifyVisibleImages(array $source, string $imageId, string $isVisibleId): void
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
     * @param string $isCoverId is Cover Flag Field ID
     *
     * @return void
     */
    private function verifyCoverImages(array $source, string $imageId, string $isCoverId): void
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
     * @param string $objectType
     *
     * @throws Exception
     *
     * @return bool
     */
    private function isAllowedProductImagesTests(string $objectType): bool
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
        if (!$imgList || !$imgList['write']) {
            return false;
        }

        return true;
    }
}
