<?php

namespace Splash\Tests\Tools\Fields;

use Splash\Client\Splash;
use Splash\Models\Fields\FieldsManagerTrait;

/**
 * @abstract    Object ID Field : price definition Array
 */
class Ooobjectid
{
    use FieldsManagerTrait;

    //==============================================================================
    //      Structural Data
    //==============================================================================

    protected $FORMAT = 'ObjectId';

    //==============================================================================
    //      DATA VALIDATION
    //==============================================================================

    /**
     * Verify given Raw Data is Valid.
     *
     * @param string $data
     *
     * @return string|true
     */
    public static function validate($data)
    {
        //==============================================================================
        //      Verify Data is Not Empty
        if (is_null($data) || empty($data) || ('0' === $data)) {
            return true;
        }

        //==============================================================================
        //      Verify Data is a string
        if (!empty($data) && !is_string($data)) {
            return 'Field  Data is not a String.';
        }

        //==============================================================================
        //      Verify Data is an Id Field
        $list = explode(IDSPLIT, $data);
        if (is_array($list) && (2 == count($list))) {
            return true;
        }

        return 'Field Data is not an Object Id String.';
    }

    //==============================================================================
    //      FAKE DATA GENERATOR
    //==============================================================================

    /**
     * Generate Fake Raw Field Data for Debugger Simulations.
     *
     * @param string $objectType Pointed Object Type Name
     * @param array  $settings   User Defined Faker Settings
     *
     * @return mixed
     */
    public static function fake($objectType, $settings)
    {
        //====================================================================//
        // Get Object List
        $objectsList = Splash::object($objectType)->objectsList();
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
    //      DATA COMPARATOR (OPTIONNAL)
    //==============================================================================

    /**
     * Compare Two Data Block to See if similar (Update Required).
     *
     * !important : Target Data is always validated before compare
     *
     * @param mixed $source Original Data Block
     * @param mixed $target New Data Block
     *
     * @return bool TRUE if both Data Block Are Similar
     */
    public static function compare($source, $target)
    {
        //dump($Source);
        //dump($Target);
        //====================================================================//
        // Both Objects Ids Are Empty
        if (empty($source) && empty($target)) {
            return true;
        }
        //====================================================================//
        // Both Objects Ids Are Similar
        if ($source == $target) {
            return true;
        }

        return false;
    }

    //====================================================================//
    //  OBJECTID FIELDS MANAGEMENT
    //====================================================================//

    /**
     *      @abstract   Encode an Object Identifier Field
     *
     *      @param      string       $objectId             Object Id
     *      @param      string       $objectType           Object Type Name
     *
     *      @return     null|string
     */
    public static function encodeIdField($objectId, $objectType)
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
     * @abstract   Retrieve Id form an Object Identifier Data
     *
     * @deprecated since version 2.0
     *
     * @param string $objectId osWs Object Identifier
     *
     * @return false|string
     */
    public static function decodeIdField($objectId)
    {
        //====================================================================//
        // Forward to Fields Manager
        return   self::objectId($objectId);
    }

    private static function filterObjectList(&$objectsList, $objectType, $settings)
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
