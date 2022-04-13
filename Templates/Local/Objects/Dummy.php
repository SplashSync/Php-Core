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

namespace   Splash\Local\Objects;

use Splash\Models\AbstractObject;
use Splash\Models\Objects\IntelParserTrait;
use Splash\Models\Objects\SimpleFieldsTrait;
use stdClass;

/**
 * TEMPLATE - Dummy Splash Object
 *
 * Add your Own Splash Objects without modifications on our modules
 *  - Place it on one of your app extension folder
 *  - Preserve class namespace
 *
 * In this exemple, we add a new "Dummy" Splash Object
 */
class Dummy extends AbstractObject
{
    // Splash Php Core Traits
    use IntelParserTrait;
    use SimpleFieldsTrait;

    /**
     * @var stdClass
     */
    protected $object;

    //====================================================================//
    // Object Definition Parameters
    //====================================================================//

    /**
     * Object Name (Translated by Module)
     *
     * {@inheritdoc}
     */
    protected static string $name = "Dummy";

    /**
     * Object Description (Translated by Module)
     *
     * {@inheritdoc}
     */
    protected static string $description = "Dolibarr Dummy Object";

    /**
     * Object Icon (FontAwesome or Glyph ico tag)
     *
     * {@inheritdoc}
     */
    protected static $ico = "fa fa-magic";

    //====================================================================//
    // Object CRUD Methods
    //====================================================================//

    /**
     * Load Request Object
     *
     * @param string $objectId Object ID
     *
     * @return null|object
     */
    public function load(string $objectId): ?object
    {
        $object = new stdClass();
        $object->id = $objectId;

        return $object;
    }

    /**
     * Create Request Object
     *
     * @return null|object
     */
    public function create(): ?object
    {
        $object = new stdClass();
        $object->id = rand(100, 1000);

        return $object;
    }

    /**
     * Update Request Object
     *
     * @param bool $needed Is This Update Needed
     *
     * @return false|string Object ID
     */
    public function update(bool $needed): ?string
    {
        if ($needed) {
            //====================================================================//
            // Update Object in Database
            return $this->getObjectIdentifier();
        }

        return $this->getObjectIdentifier();
    }

    /**
     * {@inheritdoc}
     */
    public function delete(string $objectId = null): bool
    {
        //====================================================================//
        // Delete Object in Database
        // $this->object->delete();

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectIdentifier(): ?string
    {
        if (!isset($this->object->id)) {
            return null;
        }

        return (string) $this->object->id;
    }

    /**
     * {@inheritdoc}
     */
    public function objectsList(?string $filter = null, array $params = array()): array
    {
        return array(
            "meta" => array("total" => 0, "current" => 0)
        );
    }
}
