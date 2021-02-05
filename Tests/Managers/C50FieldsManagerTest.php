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

namespace Splash\Tests\Managers;

use PHPUnit\Framework\TestCase;
use Splash\Components\FieldsManager;
use Splash\Core\SplashCore     as Splash;

/**
 * Componants Test Suite - Fields Manager Verifications
 *
 * @author SplashSync <contact@splashsync.com>
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class C50FieldsManagerTest extends TestCase
{
    use \Splash\Models\Objects\ObjectsTrait;
    use \Splash\Models\Objects\ListsTrait;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        //====================================================================//
        // BOOT MODULE
        Splash::core();
    }

    //==============================================================================
    //      FIELDS LIST FUNCTIONS
    //==============================================================================

    // TODO

    //==============================================================================
    //      LISTS FIELDS MANAGEMENT
    //==============================================================================

    /**
     * @dataProvider providerIsListFieldFunction
     *
     * @param mixed $input
     * @param mixed $result
     *
     * @return void
     */
    public function testIsListFieldFunction($input, $result)
    {
        $this->assertEquals($result, FieldsManager::isListField($input));
    }

    /**
     * @return array
     */
    public function providerIsListFieldFunction()
    {
        return array(
            array(null,                 false),
            array('',                   false),
            array('Whatever',           false),
            array('object::id',         false),
            array('object@list',        array('fieldname' => 'object',       'listname' => 'list')),
            array('list@object',        array('fieldname' => 'list',         'listname' => 'object')),
            array('object::id@list',    array('fieldname' => 'object::id',   'listname' => 'list')),
            array('object-id@list',     array('fieldname' => 'object-id',   'listname' => 'list')),
        );
    }

    /**
     * @dataProvider providerFieldNameFunction
     *
     * @param mixed $input
     * @param mixed $result
     *
     * @return void
     */
    public function testFieldNameFunction($input, $result)
    {
        $this->assertEquals($result, FieldsManager::fieldName($input));
    }

    /**
     * @return array
     */
    public function providerFieldNameFunction()
    {
        return array(
            array(null,                 false),
            array('',                   false),
            array('Whatever',           false),
            array('object::id',         false),
            array('object@list',        'object'),
            array('list@object',        'list'),
            array('object::id@list',    'object::id'),
            array('object@list::id',    'object'),
            array('object-id@list',     'object-id'),
        );
    }

    /**
     * @dataProvider providerListNameFunction
     *
     * @param mixed $input
     * @param mixed $result
     *
     * @return void
     */
    public function testListNameFunction($input, $result)
    {
        $this->assertEquals($result, FieldsManager::listName($input));
    }

    /**
     * @return array
     */
    public function providerListNameFunction()
    {
        return array(
            array(null,                 false),
            array('',                   false),
            array('Whatever',           false),
            array('object::id',         false),
            array('object@list',        'list'),
            array('list@object',        'object'),
            array('object::id@list',    'list'),
            array('object@list::id',    'list::id'),
            array('object-id@list',     'list'),
        );
    }

    /**
     * @dataProvider providerBaseTypeFunction
     *
     * @param mixed $input
     * @param mixed $result
     *
     * @return void
     */
    public function testBaseTypeFunction($input, $result)
    {
        $this->assertEquals($result, FieldsManager::baseType($input));
    }

    /**
     * @return array
     */
    public function providerBaseTypeFunction()
    {
        //====================================================================//
        // BOOT MODULE
        Splash::core();

        return array(
            array(null,                 false),
            array('',                   ''),
            array('Whatever',           'Whatever'),
            array('object::id',         'id'),
            array('object@list',        'object'),
            array('list@object',        'list'),
            array('id::object@list',    'object'),
            array('object@list::id',    'object'),
            array('object-id@list',     'object-id'),
            array(
                self::objects()->Encode('Object', SPL_T_ID),
                'Object',
            ),
            array(
                self::lists()->Encode('Listname', 'FieldName'),
                'FieldName',
            ),
            array(
                self::lists()->Encode(
                    'ListName',
                    (string) self::objects()->Encode('Object', SPL_T_ID)
                ),
                'Object',
            ),
            array(
                self::lists()->Encode(
                    (string) self::objects()->Encode('Object', SPL_T_ID),
                    'FieldName'
                ),
                'FieldName',
            ),
            array(
                self::lists()->Encode(
                    (string) self::objects()->Encode('Error', SPL_T_ID),
                    (string) self::objects()->Encode('Object', SPL_T_ID)
                ),
                'Object',
            ),
        );
    }

    //==============================================================================
    //      OBJECT ID FIELDS MANAGEMENT
    //==============================================================================

    /**
     * @dataProvider providerIsIdFieldFunction
     *
     * @param mixed $input
     * @param mixed $result
     *
     * @return void
     */
    public function testIsIdFieldFunction($input, $result)
    {
        $this->assertEquals($result, FieldsManager::isIdField($input));
    }

    /**
     * @return array
     */
    public function providerIsIdFieldFunction()
    {
        return array(
            array(null,                 false),
            array('',                   false),
            array('Whatever',           false),
            array('id::type',           array('ObjectType' => 'type',        'ObjectId' => 'id')),
            array('id::type@list',      array('ObjectType' => 'type@list',   'ObjectId' => 'id')),
            array('id@list::type',      array('ObjectType' => 'type',        'ObjectId' => 'id@list')),
            array('id-id::type-list',   array('ObjectType' => 'type-list',   'ObjectId' => 'id-id')),
        );
    }

    /**
     * @dataProvider providerObjectIdFunction
     *
     * @param mixed $input
     * @param mixed $result
     *
     * @return void
     */
    public function testObjectIdFunction($input, $result)
    {
        $this->assertEquals($result, FieldsManager::objectId($input));
    }

    /**
     * @return array
     */
    public function providerObjectIdFunction()
    {
        return array(
            array(null,                 false),
            array('',                   false),
            array('Whatever',           false),
            array('id::object',         'id'),
            array('object::id',         'object'),
            array('object@list',        false),
            array('list@object',        false),
            array('id::object@list',    'id'),
            array('object@list::id',    'object@list'),
        );
    }

    /**
     * @dataProvider providerObjectTypeFunction
     *
     * @param mixed $input
     * @param mixed $result
     *
     * @return void
     */
    public function testObjectTypeFunction($input, $result)
    {
        $this->assertEquals($result, FieldsManager::objectType($input));
    }

    /**
     * @return array
     */
    public function providerObjectTypeFunction()
    {
        return array(
            array(null,                 false),
            array('',                   false),
            array('Whatever',           false),
            array('id::object',         'object'),
            array('object::id',         'id'),
            array('object@list',        false),
            array('list@object',        false),
            array('id::object@list',    'object@list'),
            array('object@list::id',    'id'),
        );
    }

    //==============================================================================
    //      OBJECTS DATA BLOCKS FUNCTIONS
    //==============================================================================

    // TODO
}
