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
    protected static $NAME = "Dummy";

    /**
     * Object Description (Translated by Module)
     *
     * {@inheritdoc}
     */
    protected static $DESCRIPTION = "Dolibarr Dummy Object";

    /**
     * Object Icon (FontAwesome or Glyph ico tag)
     *
     * {@inheritdoc}
     */
    protected static $ICO = "fa fa-magic";

    //====================================================================//
    // Object CRUD Methods
    //====================================================================//

    /**
     * Load Request Object
     *
     * @param string $objectId Object id
     *
     * @return false|object
     */
    public function load(string $objectId)
    {
        $object = new stdClass();
        $object->id = $objectId;

        return $object;
    }

    /**
     * Create Request Object
     *
     * @return false|object
     */
    public function create()
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
    public function update(bool $needed)
    {
        if ($needed) {
            //====================================================================//
            // Update Object in Database
            // $this->object->update();

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
    public function getObjectIdentifier()
    {
        if (!isset($this->object->id)) {
            return false;
        }

        return (string) $this->object->id;
    }

    /**
     * {@inheritdoc}
     */
    public function objectsList($filter = null, $params = null)
    {
        return array(
            "meta" => array("total" => 0, "current" => 0)
        );
    }
}
