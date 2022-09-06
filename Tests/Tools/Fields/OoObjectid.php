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

namespace Splash\Tests\Tools\Fields;

use Splash\Client\Splash;
use Splash\Models\Fields\FieldsManagerTrait;

/**
 * Object ID Field : price definition Array
 */
class OoObjectid implements FieldInterface
{
    use FieldsManagerTrait;

    //==============================================================================
    //      Structural Data
    //==============================================================================

    /**
     * @var string
     */
    const FORMAT = 'ObjectId';

    //==============================================================================
    //      DATA VALIDATION
    //==============================================================================

    /**
     * {@inheritdoc}
     */
    public static function validate($data): ?string
    {
        //==============================================================================
        //      Verify Data is Not Empty
        if (empty($data)) {
            return null;
        }
        //==============================================================================
        //      Verify Data is a string
        if (!is_string($data)) {
            return 'Field  Data is not a String.';
        }
        //==============================================================================
        //      Verify Data is an Id Field
        $list = explode(IDSPLIT, $data);
        if (is_array($list) && (2 == count($list))) {
            return null;
        }

        return 'Field Data is not an Object Id String.';
    }

    //==============================================================================
    //      FAKE DATA GENERATOR
    //==============================================================================

    /**
     * @param array $settings
     *
     * @return null|string
     */
    public static function fake(array $settings, string $objectType = null)
    {
        //====================================================================//
        // Safety Check
        if (empty($objectType)) {
            return null;
        }
        //====================================================================//
        // Get Object List
        try {
            $objectsList = Splash::object($objectType)->objectsList();
        } catch (\Exception $e) {
            $objectsList = array();
        }
        //====================================================================//
        // Unset MetaData from Objects List
        if (isset($objectsList['meta'])) {
            unset($objectsList['meta']);
        }
        if (empty($objectsList)) {
            return null;
        }
        //====================================================================//
        // Filter Objects List to Remove Current Tested
        self::filterObjectList($objectsList, $objectType, $settings);
        //====================================================================//
        // Select an Object in Given List
        $item = $objectsList[array_rand($objectsList, 1)];
        if (isset($item['id']) && !empty($item['id'])) {
            //====================================================================//
            // Generate Object Id String
            return self::encodeIdField($item['id'], $objectType);
        }

        return null;
    }

    //==============================================================================
    //      DATA COMPARATOR (OPTIONAL)
    //==============================================================================

    /**
     * {@inheritdoc}
     */
    public static function compare($source, $target, array $settings): bool
    {
        //====================================================================//
        // Both Objects Ids Are Empty
        if (empty($source) && empty($target)) {
            return true;
        }
        //====================================================================//
        //  Both Are Scalar
        if (!is_scalar($source) || !is_scalar($target)) {
            return false;
        }
        //====================================================================//
        // Both Objects Ids Are Similar
        if ($source == $target) {
            return true;
        }

        return false;
    }

    //====================================================================//
    //  OBJECT ID FIELDS MANAGEMENT
    //====================================================================//

    /**
     * Encode an Object Identifier Field
     *
     * @param string $objectId   Object ID
     * @param string $objectType Object Type Name
     *
     * @return null|string
     */
    public static function encodeIdField(string $objectId, string $objectType): ?string
    {
        //====================================================================//
        // Safety Checks
        if (empty($objectType)) {
            return null;
        }
        if (empty($objectId)) {
            return null;
        }

        //====================================================================//
        // Create & Return Field Id Data String
        return $objectId.IDSPLIT.$objectType;
    }

    /**
     * Retrieve ID form an Object Identifier Data
     *
     * @param string $objectId osWs Object Identifier
     *
     * @return null|string
     *
     * @deprecated since version 2.0, use objectId instead
     */
    public static function decodeIdField(string $objectId): ?string
    {
        //====================================================================//
        // Forward to Fields Manager
        return self::objectId($objectId);
    }

    /**
     * @param array  $objectsList
     * @param string $objectType
     * @param array  $settings
     *
     * @return void
     */
    private static function filterObjectList(array &$objectsList, string $objectType, array $settings): void
    {
        //====================================================================//
        // Filter Objects List to Remove Current Tested
        $filterObjectId = null;
        if (isset($settings['CurrentType']) && ($objectType == $settings['CurrentType'])) {
            $filterObjectId = $settings['CurrentId'];
        }
        if (!empty($filterObjectId)) {
            foreach ($objectsList as $index => $item) {
                if ($item['id'] == $filterObjectId) {
                    unset($objectsList[$index]);
                }
            }
        }
    }
}
