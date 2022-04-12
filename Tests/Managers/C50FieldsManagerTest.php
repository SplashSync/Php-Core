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

namespace Splash\Tests\Managers;

use PHPUnit\Framework\TestCase;
use Splash\Components\FieldsManager;
use Splash\Core\SplashCore     as Splash;

/**
 * Components Test Suite - Fields Manager Verifications
 *
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
    //      LISTS FIELDS MANAGEMENT
    //==============================================================================

    /**
     * @dataProvider providerIsListFieldFunction
     *
     * @param null|string $input
     * @param null|array  $result
     *
     * @return void
     */
    public function testIsListFieldFunction(?string $input, ?array $result): void
    {
        $this->assertEquals($result, FieldsManager::isListField($input));
    }

    /**
     * @return array
     */
    public function providerIsListFieldFunction(): array
    {
        return array(
            array(null,                 null),
            array('',                   null),
            array('Whatever',           null),
            array('object::id',         null),
            array('object@list',        array('fieldname' => 'object',       'listname' => 'list')),
            array('list@object',        array('fieldname' => 'list',         'listname' => 'object')),
            array('object::id@list',    array('fieldname' => 'object::id',   'listname' => 'list')),
            array('object-id@list',     array('fieldname' => 'object-id',   'listname' => 'list')),
        );
    }

    /**
     * @dataProvider providerFieldNameFunction
     *
     * @param null|string $input
     * @param null|string $result
     *
     * @return void
     */
    public function testFieldNameFunction(?string $input, ?string $result): void
    {
        $this->assertEquals($result, FieldsManager::fieldName($input));
    }

    /**
     * @return array
     */
    public function providerFieldNameFunction(): array
    {
        return array(
            array(null,                 null),
            array('',                   null),
            array('Whatever',           null),
            array('object::id',         null),
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
     * @param null|string $input
     * @param null|string $result
     *
     * @return void
     */
    public function testListNameFunction(?string $input, ?string $result): void
    {
        $this->assertEquals($result, FieldsManager::listName($input));
    }

    /**
     * @return array
     */
    public function providerListNameFunction(): array
    {
        return array(
            array(null,                 null),
            array('',                   null),
            array('Whatever',           null),
            array('object::id',         null),
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
     * @param null|string $input
     * @param null|string $result
     *
     * @return void
     */
    public function testBaseTypeFunction(?string $input, ?string $result): void
    {
        $this->assertEquals($result, FieldsManager::baseType($input));
    }

    /**
     * @return array
     */
    public function providerBaseTypeFunction(): array
    {
        //====================================================================//
        // BOOT MODULE
        Splash::core();

        return array(
            array(null,                 null),
            array('',                   ''),
            array('Whatever',           'Whatever'),
            array('object::id',         'id'),
            array('object@list',        'object'),
            array('list@object',        'list'),
            array('id::object@list',    'object'),
            array('object@list::id',    'object'),
            array('object-id@list',     'object-id'),
            array(
                self::objects()->encode('Object', SPL_T_ID),
                'Object',
            ),
            array(
                self::lists()->encode('Listname', 'FieldName'),
                'FieldName',
            ),
            array(
                self::lists()->encode(
                    'ListName',
                    (string) self::objects()->encode('Object', SPL_T_ID)
                ),
                'Object',
            ),
            array(
                self::lists()->encode(
                    (string) self::objects()->encode('Object', SPL_T_ID),
                    'FieldName'
                ),
                'FieldName',
            ),
            array(
                self::lists()->encode(
                    (string) self::objects()->encode('Error', SPL_T_ID),
                    (string) self::objects()->encode('Object', SPL_T_ID)
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
     * @param null|string $input
     * @param null|array  $result
     *
     * @return void
     */
    public function testIsIdFieldFunction(?string $input, ?array $result): void
    {
        $this->assertEquals($result, FieldsManager::isIdField($input));
    }

    /**
     * @return array
     */
    public function providerIsIdFieldFunction(): array
    {
        return array(
            array(null,                 null),
            array('',                   null),
            array('Whatever',           null),
            array('id::type',           array('ObjectType' => 'type',        'ObjectId' => 'id')),
            array('id::type@list',      array('ObjectType' => 'type@list',   'ObjectId' => 'id')),
            array('id@list::type',      array('ObjectType' => 'type',        'ObjectId' => 'id@list')),
            array('id-id::type-list',   array('ObjectType' => 'type-list',   'ObjectId' => 'id-id')),
        );
    }

    /**
     * @dataProvider providerObjectIdFunction
     *
     * @param null|string $input
     * @param null|string $result
     *
     * @return void
     */
    public function testObjectIdFunction(?string $input, ?string $result): void
    {
        $this->assertEquals($result, FieldsManager::objectId($input));
    }

    /**
     * @return array
     */
    public function providerObjectIdFunction(): array
    {
        return array(
            array(null,                 null),
            array('',                   null),
            array('Whatever',           null),
            array('id::object',         'id'),
            array('object::id',         'object'),
            array('object@list',        null),
            array('list@object',        null),
            array('id::object@list',    'id'),
            array('object@list::id',    'object@list'),
        );
    }

    /**
     * @dataProvider providerObjectTypeFunction
     *
     * @param null|string $input
     * @param null|string $result
     *
     * @return void
     */
    public function testObjectTypeFunction(?string $input, ?string $result): void
    {
        $this->assertEquals($result, FieldsManager::objectType($input));
    }

    /**
     * @return array
     */
    public function providerObjectTypeFunction(): array
    {
        return array(
            array(null,                 null),
            array('',                   null),
            array('Whatever',           null),
            array('id::object',         'object'),
            array('object::id',         'id'),
            array('object@list',        null),
            array('list@object',        null),
            array('id::object@list',    'object@list'),
            array('object@list::id',    'id'),
        );
    }
}
